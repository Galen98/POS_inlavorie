<?php
namespace App\Models\Auth;

use App\Database\Database;
use PDO;

class EmailVerification
{
    private $userId;
    private $token;

    public function __construct($userId, $token)
    { 
        $this->userId = $userId;
        $this->token = $token;
    }

    public function save() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO email_activation (users_id, token) VALUES (:userId, :token)");
        $stmt->bindParam(':userId', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();
    }
}