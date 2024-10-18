<?php 
namespace App\Models;

use App\Database\Database;
use PDO;

class ContactUs {
    public $name;
    public $email;
    public $message;

    public function __construct($email = null, $name = null, $message = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->message = $message;
    }

    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT email, name, message FROM contact_us");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>