<?php 
namespace App\Models;

use App\Database\Database;
use PDO;

class ContactUs {
    public $id;
    public $name;
    public $email;
    public $message;

    public function __construct($id, $name, $email, $message)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
    }

    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT email, name, message FROM contact_us");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':message', $this->message);
        $stmt->execute();
    }
}
?>