<?php 
use App\Controllers\ModuleController\RestoManageController;
use App\Controllers\UserController;

//resto manage
Flight::route('POST /api/active-resto', [RestoManageController::class, 'active_resto']);
Flight::route('GET /api/resto-limit', [RestoManageController::class, 'resto_limit']);

//profile
Flight::route('POST /api/update-account', [UserController::class, 'update_account']);
?>