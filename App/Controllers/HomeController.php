<?php 
namespace App\Controllers;
use Rakit\Validation\Validator;
use App\Actions\Contact\GetAllContact;
use App\Actions\Contact\PostContact;
use Exception;
use Flight;
use Ghostff\Session\Session;

class HomeController {
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }

    public function index(){ 
        $session = Flight::session(); 
        $flash =  $session->getFlashOrDefault('flash', null);
        $errors =  $session->getFlashOrDefault('errors', null);
        $session->commit(); 
        Flight::latte()->render('home.latte', [
            'title' => 'inLavorie POS - Home Page',
            'flash' => $flash,
            'errors' => $errors
        ]);
    }

    //contact function
    public function getContact(){
        $day = GetAllContact::execute();
        var_dump($day);
        exit();
    }

    public function storeContact() {
        $session = Flight::session();
        $request = Flight::request()->data->getData();
        $validator = new Validator;
        $validation = $validator->make($_POST, [
            'name' => 'required',
            'email' => 'required',
            'message' => 'required'
        ]);
        $validation->validate();
    
        if ($validation->fails()) {
            $errors = $validation->errors()->all();
            $session->setFlash('errors', $errors);
            $session->commit(); 
            return Flight::redirect('/');
        } else {
            try {
                PostContact::execute($request);
                $session->setFlash('flash', 'Success');
                $session->commit();
                return Flight::redirect('/');
            } catch (Exception $e) {
                $session->setFlash('errors', $e->getMessage());
                $session->commit(); 
                return Flight::redirect('/');
            }
        }
    }
    
}
