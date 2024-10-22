<?php 
use App\Controllers\HomeController;
use App\Controllers\AuthController;

use Ghostff\Session\Session;

// Initialize the session

$session = new Session();

Flight::route('/', [HomeController::class, 'index']);
Flight::route('/getContacts', [HomeController::class, 'getContact']);
Flight::route('POST /postContacts', [HomeController::class, 'storeContact']);

//login route
$user = [
    'username' => 'admin',
    'password' => password_hash('password123', PASSWORD_DEFAULT), // Hash the password
];
Flight::map('login', function($username, $password) use ($session, $user) {
    if ($username === $user['username'] && password_verify($password, $user['password'])) {
        $session->set('user', $username);
        $session->commit();
        return true;
    }
    return false;
});
Flight::map('isLoggedIn', function() use ($session) {
    
    var_dump($session->get('user'));
    exit();
    return $session->get('user') !== null;
});

Flight::map('logout', function() use ($session) {
    $session->destroy();
});

// Routes
Flight::route('POST /login', function() {
    $username = Flight::request()->data->username;
    $password = Flight::request()->data->password;
    if (Flight::login($username, $password)) {
        Flight::redirect('/');
    } else {
        Flight::view()->render('login.latte', ['error' => 'Invalid credentials']);
    }
});
Flight::route('GET /dashboard', function() use ($session) {
    if (Flight::isLoggedIn()) {
        $username = $session->get('user'); 
        Flight::latte()->render('dashboard.latte', ['username' => $username]);
    } else {
        Flight::redirect('/login');
    }
});

Flight::route('/logout', function() {
    Flight::logout();
    Flight::redirect('/login');
});
Flight::route('/login', [AuthController::class, 'index']);
Flight::route('/register', [AuthController::class, 'registerView']);
?>