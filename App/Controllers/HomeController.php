<?php 
namespace App\Controllers;
use Rakit\Validation\Validator;
use App\Actions\Contact\GetAllContact;
use App\Actions\Contact\PostContact;
use Exception;
use Flight;

Flight::map('renderWithUser', function($template, $data = []) {
    $data['username'] = Flight::get('username') ?: '';
    $data['roles'] = Flight::get('roles') ?: '';
    Flight::latte()->render($template, $data);
});


class HomeController extends BaseController{
    public function index(){ 
        $session = Flight::session(); 
        $flash =  $session->getFlashOrDefault('flash', null);
        $success = $session->getFlashOrDefault('success', null);
        $errors =  $session->getFlashOrDefault('error', null);
        $session->commit(); 
        Flight::renderWithUser('home.latte', [
            'title' => 'inLavorie Resto - Home Page',
            'flash' => $flash,
            'error' => $errors,
            'success' => $success
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
