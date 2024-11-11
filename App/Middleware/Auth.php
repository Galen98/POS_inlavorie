<?php 
namespace App\Middleware;
use Ghostff\Session\Session;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Flight;

class Auth {
    private static $secretKey = '7c9b0d5c74a9f74f36b35edabb4c77d3e7a4b5c8f59a3b98a10c5fbb7a0a8fdd';
    public function checkLogin() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if (!$jwtToken) { 
            $session->setFlash('eror', 'Silahkan login dahulu');
            $session->commit();
            Flight::redirect('/login'); 
        }
    }

    public function loginException() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if ($jwtToken) { 
            Flight::redirect('/'); 
        } 
    }

    public function isUser() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if ($jwtToken) {
            $decoded = JWT::decode($jwtToken, new Key(self::$secretKey, 'HS256'));
            $roleAdmin = $decoded->roles; 
            if($roleAdmin != 2) {
                $session->setFlash('error', 'Akses ditolak!');
                $session->commit();
                Flight::redirect('/'); 
            }
        }
    }

    public function isAdmin() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if ($jwtToken) {
            $decoded = JWT::decode($jwtToken, new Key(self::$secretKey, 'HS256'));
            $roleAdmin = $decoded->roles; 
            if($roleAdmin != 1) {
                $session->setFlash('error', 'Akses ditolak!');
                $session->commit();
                Flight::redirect('/'); 
            }
        }
    }
}
