<?php
namespace App\Actions\Auth;

use App\Models\Auth\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use App\Models\Auth\EmailVerification;
use App\Actions\Auth\SendEmailVerif;
use EmailActivation;
use Exception;
use Flight;

class SaveUsers {
    public static function execute(array $data) {
        $session = Flight::session();
        try {
            $data = new Auth(null, $data['name'],$data['password'],$data['email'],$data['noHp']);
            $data->save();
            $userId = $data->getId();
            $token = str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
            $verification = new EmailVerification($userId, $token);
            $verification->save();
            SendEmailVerif::sendVerificationEmailAsync($data->email, $token);
            $session->setFlash('success', 'Pendaftaran berhasil silahklan cek email anda untuk aktivasi akun.');
            $session->commit();
            return Flight::redirect('/email-verif/'.$token.'');
        } catch(Exception $e){
            throw $e;
        }
    }
}
