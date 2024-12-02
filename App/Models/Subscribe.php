<?php
namespace App\Models;

use App\Database\Database;
use PDO;

class Subscribe {
    public $users_id;
    public $subscribe_id;
    public $expired_at;
    public $activated;
    
    public function __construct($users_id, $subscribe_id, $expired_at, $activated)
    {
        $this->users_id = $users_id;
        $this->subscribe_id = $subscribe_id;
        $this->expired_at = $expired_at;
        $this->activated = $activated;
    }

    public static function getSub($users_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.name_subscribed, b.subscribe_id, b.activated, b.created_at, b.expired_at FROM subscribed_masters a LEFT JOIN subcribed_users b ON a.id = b.subscribe_id WHERE b.users_id = :users_id");
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}