<?php 
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Middleware\Auth;
use App\Middleware\Subscribtion;
use Ghostff\Session\Session;
use App\Controllers\ModuleController\RestoManageController;
use App\Constants\AuthConf;

$session = new Session();
$authMiddleware = new Auth();
$subMiddleware = new Subscribtion();

Flight::route('/', [HomeController::class, 'index']);
Flight::route('/getContacts', [HomeController::class, 'getContact']);
Flight::route('POST /postContacts', [HomeController::class, 'storeContact']);

// Route Auth
Flight::route('GET /email-verif/@token', [AuthController::class, 'view_email_verif']);
Flight::map('logout', function() use ($session) {
    $session->destroy();
});
Flight::route('POST /login', function() use ($session) {
    $username = Flight::request()->data->username;
    $password = Flight::request()->data->password;
    $authController = new AuthController();
    if ($authController->login($username, $password)) {
        Flight::redirect('/');
    } else {
        $session->setFlash('eror', 'Cek kembali password atau email anda');
        $session->commit();
        Flight::redirect('/login');
    }
});
Flight::route('/logout', function() {
    Flight::logout();
    Flight::redirect('/login');
});
Flight::route('/register', [AuthController::class, 'registerView']);
Flight::route('POST /register-post', [AuthController::class, 'registerPost']);
Flight::route('GET /login', function() use ($authMiddleware) {
    $authMiddleware->loginException();
    $controller = new AuthController();
    return $controller->index();
});

//Dashboard akun
Flight::route('/dashboard', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new UserController();
    return $controller->index_dashboard();
});

//resto management
Flight::route('GET /dashboard/daftar-resto', function() use ($authMiddleware, $subMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $subMiddleware->users_sub_check();
    $controller = new RestoManageController();
    return $controller->list_daftar_resto();
});

Flight::route('GET /dashboard/daftar-resto/@id', function($id) use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new RestoManageController();
    return $controller->view_resto($id);
});

Flight::route('GET /dashboard/add-daftar-resto', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new RestoManageController();
    return $controller->add_tambah_resto();
});

Flight::route('POST /post-resto', [RestoManageController::class, 'post_resto']);
?>