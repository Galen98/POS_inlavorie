<?php 
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Middleware\Auth;
use App\Middleware\Subscribtion;
use Ghostff\Session\Session;
use App\Controllers\ModuleController\RestoManageController;
use App\Constants\AuthConf;
use App\Database\Database;

$session = new Session();
$authMiddleware = new Auth();
$subMiddleware = new Subscribtion();

Flight::map('notFound', [HomeController::class, 'notFound']);

// Route Home
Flight::route('GET /', [HomeController::class, 'index']);
Flight::route('GET /about-us', [HomeController::class, 'about']);
Flight::route('GET /features', [HomeController::class, 'features']);
Flight::route('/getContacts', [HomeController::class, 'getContact']);
Flight::route('POST /postContacts', [HomeController::class, 'storeContact']);

// Route Auth
Flight::route('GET /resend-verif-email/@token', [AuthController::class, 'resend_verif_email']);
Flight::route('GET /email-verif/@token', [AuthController::class, 'view_email_verif']);
Flight::route('GET /verification-email/@token', [AuthController::class, 'verif_email']);
Flight::map('logout', function() use ($session) {
    $userId = Flight::get('userId');
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET Status = 0 WHERE id = :users_id");
    $stmt->bindParam(':users_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
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
Flight::route('GET /register', function() use ($authMiddleware) {
    $authMiddleware->loginException();
    $controller = new AuthController();
    return $controller->registerView();
});
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

//profile route
Flight::route('/dashboard/profile', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new UserController();
    return $controller->pusat_pengguna();
});

Flight::route('/dashboard/profile/reset-password', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new UserController();
    return $controller->reset_password();
});

Flight::route('POST /update-profile', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new UserController();
    return $controller->update_profile();
});

Flight::route('POST /update-password', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new UserController();
    return $controller->update_password();
});

//subscribe page
Flight::route('/dashboard/subscribe-page', function() use ($authMiddleware) {
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new UserController();
    return $controller->index_subscribe();
});

//resto management
Flight::route('GET /dashboard/daftar-resto', function() use ($authMiddleware, $subMiddleware) {
    $subMiddleware->users_sub_check();
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new RestoManageController();
    return $controller->list_daftar_resto();
});

Flight::route('GET /dashboard/daftar-resto/detail-resto/@id', function($id) use ($authMiddleware, $subMiddleware) {
    $subMiddleware->users_sub_check();
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new RestoManageController();
    return $controller->view_resto($id);
});

Flight::route('GET /dashboard/add-daftar-resto', function() use ($authMiddleware, $subMiddleware) {
    $subMiddleware->users_sub_check();
    $authMiddleware->checkLogin();
    $authMiddleware->isAdmin();
    $controller = new RestoManageController();
    return $controller->add_tambah_resto();
});

Flight::route('POST /post-resto', [RestoManageController::class, 'post_resto']);
?>