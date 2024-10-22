<?php 
use Latte\Engine as LatteEngine;
require './vendor/autoload.php';
require './routes/route.php';
use Ghostff\Session\Session;

$app = Flight::app();
$app->register('session', Session::class);
$app->register('latte', LatteEngine::class, [], function(LatteEngine $latte) use ($app) {

  // This is where Latte will cache your templates to speed things up
  // One neat thing about Latte is that it automatically refreshes your
  // cache when you make changes to your templates!
  $latte->setTempDirectory(__DIR__ . '/../cache/');

  // Tell Latte where the root directory for your views will be at.
  // $app->get('flight.views.path') is set in the config.php file
  //   You could also just do something like `__DIR__ . '/../views/'`
  $latte->setLoader(new \Latte\Loaders\FileLoader($app->get('flight.views.path')));
});

Flight::start();
?>