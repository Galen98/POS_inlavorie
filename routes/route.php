<?php 
use App\Controllers\HomeController;
use App\Controllers\LoginController;

Flight::route('/', [HomeController::class, 'index']);
Flight::route('/getContacts', [HomeController::class, 'getContact']);
Flight::route('/login', [LoginController::class, 'index']);
?>