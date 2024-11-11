<?php 
use Latte\Engine as LatteEngine;
require './vendor/autoload.php';
require './routes/route.php';
require './routes/api.php';

use Ghostff\Session\Session;
use Dotenv\Dotenv;
use App\Constants\AuthConf;
use App\Database\Database;
use React\EventLoop\Factory;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory as MySQLFactory;
use React\Promise\Promise;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$secretKey = getenv('JWT_SECRET_KEY');

$app = Flight::app();
$app->register('session', Session::class);
$app->register('latte', LatteEngine::class, [], function(LatteEngine $latte) use ($app) {
  $latte->setTempDirectory(__DIR__ . '/../cache/');

  $latte->setLoader(new \Latte\Loaders\FileLoader($app->get('flight.views.path')));
});

Flight::before('start', function() {
  $config = include './config/database.php';
  $loop = Factory::create();
  $mysqlFactory = new MySQLFactory($loop);
  $db = $mysqlFactory->createLazyConnection(sprintf(
    '%s:%s@%s/%s',
    $config['username'],
    $config['password'],
    $config['host'],
    $config['database']
  ));

  $userData = AuthConf::isLogedIn();
  $userId = $userData && isset($userData['userId']) ? $userData['userId'] : null;
  $subscribeId = null;
  if ($userId) {
    $db->query("SELECT subscribe_id FROM subcribed_users WHERE users_id = ? AND activated = 1", [$userId])
        ->then(function ($result) use ($db, $userData) {
            $subscribeId = $result->resultRows[0]['subscribe_id'] ?? null;
            if ($subscribeId) {
                return $db->query("
                    SELECT mm.module_name AS modulename, mm.category AS category, mm.link AS link
                    FROM user_permissions up
                    INNER JOIN module_masters mm ON up.module_id = mm.id
                    WHERE up.subscribe_id = ?
                ", [$subscribeId]);
            }
            return Promise::resolve([]);
        })
        ->then(function ($result) use ($userData, $subscribeId) {
            $modules = $result->resultRows;
            $module_resto_manage = array_filter($modules, function ($module) {
              return $module['category'] === 'resto_management';
          });

            if ($userData) {
                Flight::set('username', $userData['name']);
                Flight::set('roles', $userData['roles']);
                Flight::set('userId', $userData['userId']);
                Flight::set('subscribeId', $subscribeId);
                Flight::set('module_resto_manage', $module_resto_manage);
            }
        })
        ->otherwise(function (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        });
        $loop->run();
  }
});
Flight::start();
?>