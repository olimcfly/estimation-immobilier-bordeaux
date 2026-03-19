<?php

declare(strict_types=1);

use App\Controllers\AdminBlogController;
use App\Controllers\BlogController;
use App\Controllers\EstimationController;
use App\Controllers\PageController;

$router->get('/', [EstimationController::class, 'index']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);

$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

$router->get('/admin/blog', [AdminBlogController::class, 'index']);
$router->get('/admin/blog/create', [AdminBlogController::class, 'create']);
$router->post('/admin/blog/store', [AdminBlogController::class, 'store']);
$router->get('/admin/blog/edit/{id}', [AdminBlogController::class, 'edit']);
$router->post('/admin/blog/update/{id}', [AdminBlogController::class, 'update']);
$router->post('/admin/blog/delete/{id}', [AdminBlogController::class, 'delete']);
$router->post('/admin/blog/generate', [AdminBlogController::class, 'generate']);
