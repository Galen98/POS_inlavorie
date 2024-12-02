<?php
namespace App\Models;

use App\Database\Database;
use PDO;

class Profile {
    public $users_id;

    public function __construct($users_id)
    {
        $this->users_id = $users_id;
    }

    public static function getProfile($users_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT profile_pict, alamat_lengkap, kode_pos FROM profile_users WHERE users_id = :users_id");
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUsers($users_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, email, name, password, noHp FROM users WHERE id = :users_id");
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}