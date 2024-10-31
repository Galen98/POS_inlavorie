<?php 
namespace App\Middleware;
use Ghostff\Session\Session;
use Flight;

class Auth {

    public function checkLogin() {
        $session = new Session();
        $jwtToken = $session->getOrDefault('jwt_token', null);
        $session->commit();

        if (!$jwtToken) { 
            $session->setFlash('eror', 'Silahkan login dahulu');
            $session->commit();
            Flight::redirect('/login'); 
        } else {
            Flight::redirect('/'); 
        }
    }
}
