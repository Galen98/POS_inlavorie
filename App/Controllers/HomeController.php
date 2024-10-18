<?php 
namespace App\Controllers;
use App\Actions\Contact\GetAllContact;
use Exception;
use Flight;

class HomeController {

    public function index(){
        Flight::latte()->render('home.latte', [
            'title' => 'inLavorie POS - Home Page'
        ]);
    }

    public function getContact(){
        $day = GetAllContact::execute();
        var_dump($day);
        exit();
    }
}
