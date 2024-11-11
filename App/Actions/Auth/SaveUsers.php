<?php
namespace App\Actions\Auth;

use App\Models\Auth\Auth;
use App\Models\Auth\EmailVerification;
use EmailActivation;
use Exception;

class SaveUsers {
    public static function execute(array $data){
        try {
            $data = new Auth(null, $data['name'],$data['password'],$data['email'],$data['noHp']);
            $data->save();
            $userId = $data->getId();
            $token = str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
            $verification = new EmailVerification($userId, $token);
            $verification->save();
            return true;
        } catch(Exception $e){
            throw $e;
        }
    }
}
