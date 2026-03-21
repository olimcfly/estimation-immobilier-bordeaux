<?php

declare(strict_types=1);

use App\Controllers\ActualiteController;
use App\Controllers\AdminActualiteController;
use App\Controllers\AdminBlogController;
use App\Controllers\AdminAchatController;
use App\Controllers\AdminController;
use App\Controllers\AdminDashboardController;
use App\Controllers\AdminDatabaseController;
use App\Controllers\AdminEmailController;
use App\Controllers\AdminImageController;
use App\Controllers\AdminLeadController;
use App\Controllers\AdminPartenaireController;
use App\Controllers\AdminSequenceController;
use App\Controllers\AdminDiagnosticController;
use App\Controllers\AdminApiController;
use App\Controllers\AdminSocialImageController;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\EstimationController;
use App\Controllers\PageController;
use App\Controllers\LandingPageController;
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
$router->get('/admin/diagnostic', [AdminDiagnosticController::class, 'index']);
$router->get('/admin/diagnostic/database', [AdminDiagnosticController::class, 'databaseDiagnostic']);
$router->get('/admin/test-smtp', [AuthController::class, 'testSmtp']);
$router->post('/admin/test-smtp/save', [AuthController::class, 'testSmtpSave']);
$router->post('/admin/test-smtp/reset', [AuthController::class, 'testSmtpReset']);
$router->post('/admin/test-smtp/run', [AuthController::class, 'testSmtpRun']);
$router->post('/admin/test-smtp/send', [AuthController::class, 'testSmtpSendEmail']);
$router->post('/admin/dev-skip-auth/toggle', [AuthController::class, 'toggleDevSkipAuth']);

// Protected admin routes
$router->get('/admin', [AdminDashboardController::class, 'index']);
$router->get('/admin/leads', [AdminLeadController::class, 'index']);
$router->get('/admin/leads/{id}', [AdminLeadController::class, 'show']);
$router->get('/admin/leads/edit/{id}', [AdminLeadController::class, 'edit']);
$router->post('/admin/leads/update/{id}', [AdminLeadController::class, 'update']);
$router->post('/admin/leads/statut/{id}', [AdminLeadController::class, 'updateStatut']);
$router->post('/admin/leads/note/{id}', [AdminLeadController::class, 'addNote']);
$router->post('/admin/leads/note/delete/{id}', [AdminLeadController::class, 'deleteNote']);
$router->post('/admin/leads/delete/{id}', [AdminLeadController::class, 'delete']);

// Admin funnel & portfolio
$router->get('/admin/funnel', [AdminDashboardController::class, 'funnel']);
$router->get('/admin/portfolio', [AdminDashboardController::class, 'portfolio']);
$router->post('/admin/portfolio/commission', [AdminDashboardController::class, 'updateCommissionRate']);

// Admin achats routes
$router->get('/admin/achats', [AdminAchatController::class, 'index']);
$router->get('/admin/achats/edit', [AdminAchatController::class, 'edit']);
$router->post('/admin/achats/save', [AdminAchatController::class, 'save']);
$router->post('/admin/achats/delete', [AdminAchatController::class, 'delete']);
$router->post('/admin/achats/create-table', [AdminAchatController::class, 'createTable']);

// Admin partenaires routes
$router->get('/admin/partenaires', [AdminPartenaireController::class, 'index']);
$router->get('/admin/partenaires/edit', [AdminPartenaireController::class, 'edit']);
$router->post('/admin/partenaires/save', [AdminPartenaireController::class, 'save']);
$router->post('/admin/partenaires/delete', [AdminPartenaireController::class, 'delete']);

// Admin social images routes
$router->get('/admin/social-images', [AdminSocialImageController::class, 'index']);
$router->get('/admin/social-images/history', [AdminSocialImageController::class, 'history']);
$router->post('/admin/social-images/save', [AdminSocialImageController::class, 'save']);
$router->post('/admin/social-images/delete', [AdminSocialImageController::class, 'delete']);

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

// Actualités (news) routes
$router->get('/actualites', [ActualiteController::class, 'index']);
$router->get('/actualites/{slug}', [ActualiteController::class, 'show']);

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
$router->post('/admin/blog/delete/{id}', [AdminBlogController::class, 'delete']);
$router->post('/admin/blog/generate', [AdminBlogController::class, 'generate']);
$router->post('/admin/blog/restore/{id}/{revisionId}', [AdminBlogController::class, 'restoreRevision']);

// Admin actualités routes
$router->get('/admin/actualites', [AdminActualiteController::class, 'index']);
$router->get('/admin/actualites/create', [AdminActualiteController::class, 'create']);
$router->post('/admin/actualites/store', [AdminActualiteController::class, 'store']);
$router->get('/admin/actualites/edit/{id}', [AdminActualiteController::class, 'edit']);
$router->post('/admin/actualites/update/{id}', [AdminActualiteController::class, 'update']);
$router->post('/admin/actualites/delete/{id}', [AdminActualiteController::class, 'delete']);
$router->post('/admin/actualites/search', [AdminActualiteController::class, 'search']);
$router->post('/admin/actualites/generate', [AdminActualiteController::class, 'generate']);

// Admin AI image generation routes
$router->get('/admin/images', [AdminImageController::class, 'index']);
$router->post('/admin/images/generate', [AdminImageController::class, 'generate']);
$router->post('/admin/images/delete', [AdminImageController::class, 'delete']);
$router->post('/admin/api/images/generate', [AdminImageController::class, 'apiGenerate']);
$router->get('/admin/api/images/seo-prompt', [AdminImageController::class, 'apiSeoPrompt']);

// Admin database management routes
$router->get('/admin/database', [AdminDatabaseController::class, 'index']);
$router->post('/admin/database', [AdminDatabaseController::class, 'index']);

// Admin email template routes
$router->get('/admin/emails', [AdminEmailController::class, 'index']);
$router->get('/admin/emails/edit', [AdminEmailController::class, 'edit']);
$router->post('/admin/emails/save', [AdminEmailController::class, 'save']);
$router->post('/admin/emails/delete', [AdminEmailController::class, 'delete']);
$router->post('/admin/emails/send-test', [AdminEmailController::class, 'sendTest']);
$router->post('/admin/emails/ai-generate', [AdminEmailController::class, 'aiGenerate']);

// Admin email sequence routes
$router->get('/admin/sequences', [AdminSequenceController::class, 'index']);
$router->get('/admin/sequences/edit', [AdminSequenceController::class, 'edit']);
$router->post('/admin/sequences/save', [AdminSequenceController::class, 'save']);
$router->post('/admin/sequences/delete', [AdminSequenceController::class, 'delete']);
$router->post('/admin/sequences/save-persona', [AdminSequenceController::class, 'savePersona']);
$router->get('/admin/sequences/article-suggestions', [AdminSequenceController::class, 'articleSuggestions']);

// Google Ads Landing Pages (capture pages — no navigation)
$router->get('/lp/estimation-lannion', [LandingPageController::class, 'estimationLannion']);
$router->get('/lp/vendre-maison-lannion', [LandingPageController::class, 'vendreMaisonLannion']);
$router->get('/lp/avis-valeur-gratuit', [LandingPageController::class, 'avisValeurGratuit']);
$router->post('/lp/submit', [LandingPageController::class, 'submitLead']);

// Admin: Google Ads guide & best practices
$router->get('/admin/google-ads', [LandingPageController::class, 'guide']);

// Admin API management routes
$router->get('/admin/api-management', [AdminApiController::class, 'index']);
$router->post('/admin/api/test/{apiKey}', [AdminApiController::class, 'testApi']);
$router->post('/admin/api/save-keys', [AdminApiController::class, 'saveKeys']);
