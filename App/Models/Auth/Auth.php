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

    public function checkEmailAvail($value){
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $value);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count == 0;
    }

    public function saveUser(){
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO users (name, email, password, noHp) VALUES (:name, :email, :password, :noHp)");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', password_hash($this->password, PASSWORD_DEFAULT));
        $stmt->bindParam(':noHp', $this->noHp);
        $stmt->execute();
    }
}