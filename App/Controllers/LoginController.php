<?php
namespace App\Controllers;
use Flight;

class LoginController{

    public function index(){
        Flight::latte()->render('login_page/index.latte', [
            'title' => 'Login - inLavorie POS System'
        ]);
    }
}