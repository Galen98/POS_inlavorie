<?php
namespace App\Controllers;

use Flight;
use App\Models\Auth\Auth;
use Ghostff\Session\Session;
use Rakit\Validation\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;


class AuthController{
    private $dummyUsers = [
        [
            'username' => 'admin@mail.com',
            'password' => '$2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a',
            'id' => 1
        ],
        [
            'username' => 'user',
            'password' => 'userpassword',
            'id' => 2
        ]
    ];

    private $secretKey = '7c9b0d5c74a9f74f36b35edabb4c77d3e7a4b5c8f59a3b98a10c5fbb7a0a8fdd';

    public function index(){
        $session = new Session();
        $errors = $session->getFlashOrDefault('eror', null);
        $session->commit();
        Flight::latte()->render('login_page/index.latte', [
            'title' => 'Login - inLavorie POS System',
            'eror' => $errors 
        ]);
        
    }

    public function registerView(){
        $session = new Session();
        $errors = $session->getFlashOrDefault('eror', null);
        $session->commit();
        Flight::latte()->render('login_page/registrasi_pengguna.latte', [
            'title' => 'Registrasi - inLavorie POS System',
            'eror' => $errors
        ]);
    }

    //login post function
    public function login($username, $password){
        foreach ($this->dummyUsers as $user) {
        if ($username === $user['username'] && password_verify($password, $user['password'])) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;
            
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'userId' => $user['id'],
                'username' => $user['username']
            ];

            // Buat JWT token
            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
            Flight::session()->set('jwt_token', $jwt);
            Flight::session()->commit();
            return true;
        }
    }
    return false;
    }

    //is logedin function
    public function isLogedIn(){
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();
    
    if ($jwtToken) {
        try {
        $decoded = JWT::decode($jwtToken, new Key($this->secretKey, 'HS256'));
        return $decoded->username; 
        } catch (ExpiredException $e) {
            return false;
        } catch (SignatureInvalidException $e) {
            return false; 
        } 
    }
    return false; 
    }

    public function registerPost() {
        $session = Flight::session();
        $request = Flight::request()->data->getData();
        $validator = new Validator;
        
        // $validator->addValidator('unique', $request);
        $validation = $validator->make($_POST, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'noHp' => 'required|numeric',
            'confirm_password' => 'required|same:password',
        ]);

        $validation->setMessages([
            'name:required' => 'Nama wajib diisi.',
            'email:required' => 'Email wajib diisi.',
            'email:email' => 'Format email tidak valid.',
            'password:required' => 'Password wajib diisi.',
            'password:min' => 'Password minimal harus memiliki 6 karakter.',
            'noHp:required' => 'Nomor HP wajib diisi.',
            'noHp:numeric' => 'Nomor HP harus berupa angka.',
            'confirm_password' => 'Konfirmasi password salah.'
        ]);
        
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors()->all();
            $session->setFlash('eror', $errors);
            $session->commit(); 
            return Flight::redirect('/register');
        } else {

        }
    }
}