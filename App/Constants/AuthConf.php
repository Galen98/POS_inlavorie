<?php
namespace App\Constants;

use Ghostff\Session\Session;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthConf {
    private static $secretKey = '7c9b0d5c74a9f74f36b35edabb4c77d3e7a4b5c8f59a3b98a10c5fbb7a0a8fdd';

    public static function isLogedIn() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if ($jwtToken) {
            try {
                $decoded = JWT::decode($jwtToken, new Key(self::$secretKey, 'HS256'));
                return [
                    'name' => $decoded->name, 
                    'roles' => $decoded->roles,
                    'userId' => $decoded->userId
                ]; 
            } catch (ExpiredException $e) {
                $session->destroy();
            } catch (SignatureInvalidException $e) {
                $session->destroy(); 
            }
        }
        return false; 
    }
}
