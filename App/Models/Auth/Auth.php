<?php
namespace App\Models\Auth;

use App\Database\Database;
use PDO;

class Auth {
    public $id;
    public $name;
    public $password;
    public $email;
    public $noHp;

    public function __construct($id, $name, $password, $email, $noHp)
    {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        $this->noHp = $noHp;
    }

    public function save(){
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO users (name, email, password, noHp, Status, roles, activated) VALUES (:name, :email, :password, :noHp, 1, 1, 0)");
        $passwordHashed = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $passwordHashed);
        $stmt->bindParam(':noHp', $this->noHp);
        $stmt->execute();
        $this->id = $db->lastInsertId();

        return true;
    }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, email, password, name, roles FROM users WHERE activated = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getId() {
        return $this->id;
    }
}