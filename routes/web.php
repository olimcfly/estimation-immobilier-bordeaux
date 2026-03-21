<?php

declare(strict_types=1);

use App\Controllers\AdminBlogController;
use App\Controllers\AdminController;
use App\Controllers\AdminImageController;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\EstimationController;
use App\Controllers\PageController;
use App\Controllers\ToolController;

$router->get('/', [PageController::class, 'home']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->get('/leads', [EstimationController::class, 'leads']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/api/estimation', [EstimationController::class, 'apiEstimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);

// Auth routes
$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/logout', [AuthController::class, 'logout']);
$router->get('/admin/diagnostic', [AuthController::class, 'diagnostic']);
$router->get('/admin/test-smtp', [AuthController::class, 'testSmtp']);

// Protected admin routes
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/leads', [EstimationController::class, 'leads']);

$router->get('/services', [PageController::class, 'services']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/a-propos', [PageController::class, 'aPropos']);
$router->get('/processus-estimation', [PageController::class, 'processusEstimation']);
$router->get('/quartiers', [PageController::class, 'quartiers']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/newsletter', [PageController::class, 'newsletter']);
$router->post('/newsletter', [PageController::class, 'newsletterSubscribe']);
$router->get('/newsletter/confirm', [PageController::class, 'newsletterConfirm']);
$router->get('/exemples-estimation', [PageController::class, 'exemplesEstimation']);
$router->get('/guides', [PageController::class, 'guides']);
$router->post('/contact', [PageController::class, 'contactSubmit']);
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

$router->get('/mentions-legales', [PageController::class, 'mentionsLegales']);
$router->get('/politique-confidentialite', [PageController::class, 'politiqueConfidentialite']);
$router->get('/conditions-utilisation', [PageController::class, 'conditionsUtilisation']);
$router->get('/rgpd', [PageController::class, 'rgpd']);

$router->get('/tools/calculatrice', [ToolController::class, 'calculatrice']);

// Admin blog routes
$router->get('/admin/blog', [AdminBlogController::class, 'index']);
$router->get('/admin/blog/create', [AdminBlogController::class, 'create']);
$router->post('/admin/blog/store', [AdminBlogController::class, 'store']);
$router->get('/admin/blog/edit/{id}', [AdminBlogController::class, 'edit']);
$router->post('/admin/blog/update/{id}', [AdminBlogController::class, 'update']);
$router->get('/admin/blog/delete/{id}', [AdminBlogController::class, 'delete']);
$router->post('/admin/blog/generate', [AdminBlogController::class, 'generate']);
$router->post('/admin/blog/restore/{id}/{revisionId}', [AdminBlogController::class, 'restoreRevision']);

// Admin AI image generation routes
$router->get('/admin/images', [AdminImageController::class, 'index']);
$router->post('/admin/images/generate', [AdminImageController::class, 'generate']);
$router->post('/admin/images/delete', [AdminImageController::class, 'delete']);
$router->post('/admin/api/images/generate', [AdminImageController::class, 'apiGenerate']);
$router->get('/admin/api/images/seo-prompt', [AdminImageController::class, 'apiSeoPrompt']);
