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

    public function postLogin(){
        
    }
}