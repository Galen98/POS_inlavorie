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
use App\Actions\Auth\SaveUsers;
use Exception;
use PDO;

Flight::map('renderWithUser', function($template, $data = []) {
    $data['username'] = Flight::get('username');
    Flight::latte()->render($template, $data);
});

class AuthController{
    private $secretKey = '7c9b0d5c74a9f74f36b35edabb4c77d3e7a4b5c8f59a3b98a10c5fbb7a0a8fdd';

    public function index(){
        $session = new Session();
        $errors = $session->getFlashOrDefault('eror', null);
        $success = $session->getFlashOrDefault('success', null);
        $session->commit();
        Flight::renderWithUser('login_page/index.latte', [
            'title' => 'Login - inLavorie POS System',
            'eror' => $errors,
            'success' => $success,
        ]);
    }

    public function registerView(){
        $session = new Session();
        $errors = $session->getFlashOrDefault('eror', null);
        $success = $session->getFlashOrDefault('success', null);
        $session->commit();
        Flight::renderWithUser('login_page/registrasi_pengguna.latte', [
            'title' => 'Registrasi - inLavorie Resto',
            'eror' => $errors,
            'success' => $success
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
            $stmt = $db->query("SELECT subscribe_id FROM subcribed_users WHERE users_id = $userId");
            $subscribe = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'userId' => $user['id'],
                'username' => $user['email'],
                'name' => $user['name'],
                'roles' => $user['roles'],
                'substatus' => $subscribe
            ];

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
            'name:required' => 'Nama wajib diisi.',
            'password:required' => 'Password wajib diisi.',
            'password:min' => 'Password minimal harus memiliki 6 karakter.',
            'noHp:required' => 'Nomor HP wajib diisi.',
            'noHp:numeric' => 'Nomor HP harus berupa angka.',
            'confirm_password' => 'Konfirmasi password salah.',
            'email:unique' => 'Email sudah digunakan, silakan gunakan email lain.'
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
            $session->setFlash('success', 'Pendaftaran berhasil silahklan cek email anda untuk aktivasi akun.');
            $session->commit();
            return Flight::redirect('/login');
           } catch(Exception $e){
            return $e->getMessage();
           }
        }
    }

    public function view_email_verif($token) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.email, b.users_id FROM users a LEFT JOIN email_activation b ON a.id = b.users_id WHERE b.token = $token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
        Flight::renderWithUser('email_confirm/email_verif.latte', [
            'title' => 'Verifikasi Email Pendaftaran',
            'user' => $user
        ]);
        } else {
            return Flight::redirect('/');
        }
    }
}