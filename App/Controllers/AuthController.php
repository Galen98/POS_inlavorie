<?php
namespace App\Controllers;

use Flight;
use App\Models\Auth\Auth;
use App\Middleware\UniqueRule;
use Ghostff\Session\Session;
use Rakit\Validation\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\Database\Database;
use App\Actions\Auth\SendEmailVerif;
use App\Actions\Auth\SaveUsers;
use App\Actions\Auth\ActiveSubscribe;
use App\Actions\Auth\InsertProfileUser;
use Exception;
use PDO;

Flight::map('renderWithUser', function($template, $data = []) {
    $session = Flight::session();
    $data['csrf'] = Flight::session()->get('csrf_token');
    $data['username'] = Flight::get('username');
    $data['success'] =  $session->getFlashOrDefault('success', null);
    $data['errors'] =  $session->getFlashOrDefault('error', null);
    $session->commit();
    Flight::latte()->render($template, $data);
});

class AuthController{
    private $secretKey = '7c9b0d5c74a9f74f36b35edabb4c77d3e7a4b5c8f59a3b98a10c5fbb7a0a8fdd';

    public function index(){
        $session = new Session();
        $errors = $session->getFlashOrDefault('eror', null);
        $session->commit();
        Flight::renderWithUser('login_page/index.latte', [
            'title' => 'Login - inLavorie POS System',
            'eror' => $errors
        ]);
    }

    public function registerView(){
        $session = new Session();
        $errors = $session->getFlashOrDefault('eror', null);
        $session->commit();
        Flight::renderWithUser('login_page/registrasi_pengguna.latte', [
            'title' => 'Registrasi - inLavorie Resto',
            'eror' => $errors,
        ]);
    }

    //login post function
    public function login($username, $password){
        $users = Auth::getAll();
        foreach ($users as $user) {
        if ($username === $user['email'] && password_verify($password, $user['password'])) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;
            $userId = $user['id'];
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT subscribe_id, expired_at, activated FROM subcribed_users WHERE users_id = $userId");
            $stmt->execute();
            $subscribe = $stmt->fetch(PDO::FETCH_ASSOC);
            $now = time();
            $expiredAtTimestamp = strtotime($subscribe['expired_at']);
            $diffInSeconds = $expiredAtTimestamp - $now;
            $diffInDays = $diffInSeconds / (60 * 60 * 24);

            $expsub = false;
            if ($diffInDays <= 7 && $diffInDays >= 0) {
                $expsub = true;
            }

            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'userId' => $user['id'],
                'username' => $user['email'],
                'name' => $user['name'],
                'roles' => $user['roles'],
                'substatus' => $subscribe['subscribe_id'],
                'expiredAt' => $subscribe['expired_at'],
                'subactive' => $subscribe['activated'],
                'expsub' => $expsub
            ];
            $userId = $user['id'];
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET Status = 1 WHERE id = :users_id");
            $stmt->bindParam(':users_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Buat JWT token
            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
            Flight::session()->set('jwt_token', $jwt);
            Flight::session()->setFlash('success', 'Login Berhasil');
            Flight::session()->commit();
            return true;
        }
    }
    return false;
    }

    public function registerPost() {
        $session = Flight::session();
        $request = Flight::request()->data->getData();
        $validator = new Validator;
        $pdo = Database::getInstance()->getConnection();
        $validator->addValidator('unique', new UniqueRule($pdo));
        $validation = $validator->make($_POST, [
            'name' => 'required',
            'password' => 'required|min:6',
            'noHp' => 'required|numeric',
            'confirm_password' => 'required|same:password',
            'email' => 'required|email|unique:users,email'
        ]);

        $validation->setMessages([
            'name:required' => 'Nama wajib diisi',
            'password:required' => 'Password wajib diisi',
            'password:min' => 'Password minimal harus memiliki 6 karakter',
            'noHp:required' => 'Nomor HP wajib diisi',
            'noHp:numeric' => 'Nomor HP harus berupa angka',
            'confirm_password' => 'Konfirmasi password salah',
            'email:unique' => 'Email sudah digunakan, silakan gunakan email lain'
        ]);
        
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors()->all();
            $session->setFlash('eror', $errors);
            $session->commit(); 
            return Flight::redirect('/register');
        } else {
            try {
            SaveUsers::execute($request);
           } catch(Exception $e){
            return $e->getMessage();
           }
        }
    }

    public function view_email_verif($token) {
        $session = Flight::session();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.email, b.users_id FROM users a LEFT JOIN email_activation b ON a.id = b.users_id WHERE b.token = $token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
        Flight::renderWithUser('email_confirm/email_verif.latte', [
            'title' => 'Verifikasi Email Pendaftaran',
            'user' => $user,
            'token' => $token
        ]);
        } else {
            $session->setFlash('error', 'User tidak tersedia!');
            $session->commit(); 
            return Flight::redirect('/');
        }
    }

    public function verif_email($token) {
        $session = Flight::session();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT users_id FROM email_activation WHERE token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $getId = $stmt->fetch(PDO::FETCH_ASSOC);

        if($getId){
            $stmt = $db->prepare("UPDATE users SET activated = 1 WHERE id = :getId");
            $stmt->bindParam(':getId', $getId['users_id'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM email_activation WHERE token = :token");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            ActiveSubscribe::active_subscribe_free($getId['users_id']);
            InsertProfileUser::profile_user_register($getId['users_id']);
            $session->setFlash('success', 'Akun anda sudah aktif! Silahkan login.');
            $session->commit(); 
            return Flight::redirect('/login');
        } else{
            $session->setFlash('error', 'User tidak tersedia!');
            $session->commit(); 
            return Flight::redirect('/');
        }
    }

    public function resend_verif_email($token) {
        $session = Flight::session();
        $token_new = str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
        try{
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.email FROM users a LEFT JOIN email_activation b ON a.id = b.users_id WHERE b.token = $token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("UPDATE email_activation SET token = $token_new WHERE token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        SendEmailVerif::sendVerificationEmailAsync($user['email'], $token_new);
        $session->setFlash('success', 'Email aktivasi berhasil dikirim.');
        $session->commit();
        return Flight::redirect('/email-verif/'.$token_new.'');
        } catch(Exception $e){
            return $e->getMessage();
        }
    }
}