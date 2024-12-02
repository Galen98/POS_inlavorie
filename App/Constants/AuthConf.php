<?php
namespace App\Constants;

use Ghostff\Session\Session;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\Database\Database;
use Flight;
use PDO;

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
                    'userId' => $decoded->userId,
                    'substatus' => $decoded->substatus,
                    'expiredAt' => $decoded->expiredAt,
                    'expsub' => $decoded->expsub,
                    'subactive' => $decoded->subactive
                ]; 
            }  catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Expired token') !== false) {
                    $parts = explode('.', $jwtToken);
                    if (count($parts) === 3) {
                    $payload = json_decode(base64_decode($parts[1]), true);
                    $idUser = $payload['userId'] ?? null;
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("UPDATE users SET Status = 0 WHERE id = :users_id");
                    $stmt->bindParam(':users_id', $idUser, PDO::PARAM_INT);
                    $stmt->execute();

                    $session->destroy();
                    }
                } else {
                    throw $e;
                }
            }
        }
    
        return null; 
    }
}
