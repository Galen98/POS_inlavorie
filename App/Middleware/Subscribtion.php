<?php 
namespace App\Middleware;
use Ghostff\Session\Session;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Flight;
use App\Constants\AuthConf;
use App\Models\Subscribe;

class Subscribtion {
    private static $secretKey = '7c9b0d5c74a9f74f36b35edabb4c77d3e7a4b5c8f59a3b98a10c5fbb7a0a8fdd';

    public function users_sub_check() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if ($jwtToken) {
            $decoded = JWT::decode($jwtToken, new Key(self::$secretKey, 'HS256'));
            // $sub = $decoded->substatus; 
            $subactive = $decoded->subactive;
            $sub = Subscribe::getSub($decoded->userId);
            if($subactive == 0) {
                $session->setFlash('errors', 'Akses ditolak!');
                $session->commit();
                Flight::redirect('/'); 
            }
        }
    }

    public function users_sub_check_pro(){
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if ($jwtToken) {
            $decoded = JWT::decode($jwtToken, new Key(self::$secretKey, 'HS256'));
            $sub = $decoded->substatus; 
            $subactive = $decoded->subactive;
            if($sub == 0 || $sub == 1 || $sub == 3 && $subactive == 0) {
                $session->setFlash('errors', 'Akses ditolak!');
                $session->commit();
                Flight::redirect('/dashboard'); 
            }
        }
    }

}