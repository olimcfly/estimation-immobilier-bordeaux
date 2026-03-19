<?php

declare(strict_types=1);

use App\Controllers\EstimationController;

$router->get('/', [EstimationController::class, 'index']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->get('/leads', [EstimationController::class, 'leads']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);
