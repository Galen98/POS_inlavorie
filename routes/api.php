<?php 
use App\Controllers\ModuleController\RestoManageController;


Flight::route('GET /api/resto-limit', [RestoManageController::class, 'resto_limit']);
?>