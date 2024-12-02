<?php
namespace App\Actions\Auth;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use React\EventLoop\Factory;
use React\Promise\Promise;
use App\Database\Database;

class InsertProfileUser {
    public static function profile_user_register($userId) {
        $loop = Factory::create();
        $promise = new Promise(function($resolve) use ($userId, $loop){
            $loop->futureTick(function () use ($userId, $resolve){
                $date = date('Y-m-d', strtotime('+1 month'));
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO profile_users (users_id) VALUES (:userId)");
                $stmt->bindParam(':userId', $userId);
                $stmt->execute();
                $resolve(true);
            });
        });

        $loop->run();
    }
}