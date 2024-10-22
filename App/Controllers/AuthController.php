<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use Flight;

class AuthController extends BaseController {

    public function index(){
        Flight::latte()->render('login_page/index.latte', [
            'title' => 'Login - inLavorie POS System'
        ]);
    }

    public function registerView(){
        Flight::latte()->render('login_page/registrasi_pengguna.latte', [
            'title' => 'Registrasi - inLavorie POS System'
        ]);
    }

    //login post function
    public function loginPost(){
        
    }
}