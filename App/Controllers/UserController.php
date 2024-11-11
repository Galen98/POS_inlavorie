<?php
namespace App\Controllers;
use Flight;

Flight::map('renderWithUser', function($template, $data = []) {
    $session = Flight::session();
    $data['username'] = Flight::get('username');
    $data['module_resto_manage'] = Flight::get('module_resto_manage');
    $data['errors'] =  $session->getFlashOrDefault('errors', null);
    $data['success'] =  $session->getFlashOrDefault('success', null);
    $data['eror'] =  $session->getFlashOrDefault('eror', null);
    $session->commit();
    Flight::latte()->render($template, $data);
});


class UserController {

    public function index_dashboard() {
        Flight::renderWithUser('dashboard_page/index.latte', [
            'title' => 'inLavorie Resto - Dashboard'
        ]);
    }
}
?>