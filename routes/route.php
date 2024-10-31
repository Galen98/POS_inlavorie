<?php 
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Middleware\Auth;
use Ghostff\Session\Session;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

$session = new Session();
$authMiddleware = new Auth();

Flight::route('/', [HomeController::class, 'index']);
Flight::route('/getContacts', [HomeController::class, 'getContact']);
Flight::route('POST /postContacts', [HomeController::class, 'storeContact']);


// Route Auth
Flight::map('logout', function() use ($session) {
    $session->destroy();
});

Flight::route('POST /login', function() use ($session) {
    $username = Flight::request()->data->username;
    $password = Flight::request()->data->password;
    $authController = new AuthController();
    if ($authController->login($username, $password)) {
        $session->setFlash('success', 'Login Berhasil');
        $session->commit();
        Flight::redirect('/');
    } else {
        $session->setFlash('eror', 'Cek kembali password atau email anda');
        $session->commit();
        Flight::redirect('/login');
    }
});

Flight::route('GET /dashboard', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $controller = new AuthController();
    return $controller->registerView();
});

Flight::route('/logout', function() {
    Flight::logout();
    Flight::redirect('/login');
});

Flight::route('/login', [AuthController::class, 'index']);
Flight::route('/register', [AuthController::class, 'registerView']);
Flight::route('/register-post', [AuthController::class, 'registerPost']);
// Flight::route('GET /register', function() use ($authMiddleware) {
//     $authMiddleware->checkLogin();
//     $controller = new AuthController();
//     return $controller->registerView();
// });

?>