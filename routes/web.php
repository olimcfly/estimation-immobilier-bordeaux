<?php

declare(strict_types=1);

use App\Controllers\AdminBlogController;
use App\Controllers\BlogController;
use App\Controllers\EstimationController;
use App\Controllers\PageController;

$router->get('/', [PageController::class, 'home']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->get('/leads', [EstimationController::class, 'leads']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);
$router->get('/admin/leads', [EstimationController::class, 'leads']);

$router->get('/services', [PageController::class, 'services']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/a-propos', [PageController::class, 'aPropos']);
$router->get('/processus-estimation', [PageController::class, 'processusEstimation']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/newsletter', [PageController::class, 'newsletter']);
$router->get('/exemples-estimation', [PageController::class, 'exemplesEstimation']);
$router->get('/guides', [PageController::class, 'guides']);
$router->post('/contact', [PageController::class, 'contactSubmit']);

$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);
