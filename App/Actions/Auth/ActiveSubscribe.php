<?php
namespace App\Actions\Auth;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use React\EventLoop\Factory;
use React\Promise\Promise;
use App\Database\Database;

class ActiveSubscribe {
    //active subscribe plan
    public static function active_subscribe_free($userId) {
        $loop = Factory::create();
        $promise = new Promise(function($resolve) use ($userId, $loop){
            $loop->futureTick(function () use ($userId, $resolve){
                $date = date('Y-m-d', strtotime('+1 month'));
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO subcribed_users (users_id, subscribe_id, activated, expired_at) VALUES (:userId, 3, 1, :date)");
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':date', $date);
                $stmt->execute();
                $resolve(true);
            });
        });

        $loop->run();
    }
}