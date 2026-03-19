<?php

declare(strict_types=1);

use App\Controllers\EstimationController;
use App\Controllers\PageController;

$router->get('/', [PageController::class, 'home']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);

$router->get('/services', [PageController::class, 'services']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/a-propos', [PageController::class, 'aPropos']);
$router->get('/contact', [PageController::class, 'contact']);
$router->post('/contact', [PageController::class, 'contactSubmit']);
