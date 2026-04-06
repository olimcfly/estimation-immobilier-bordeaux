<?php
// Configuration EstimIA - Bordeaux
define('DEBUG_MODE', false);
define('MAINTENANCE_MODE', false);
define('SITE_NAME', 'EstimIA');
define('CITY_NAME', 'Bordeaux');
define('SITE_PHONE', '');

// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'sc2tasq5564_estimia');
define('DB_USER', 'sc2tasq5564_estimia');
define('DB_PASS', ''); // À remplir

// Email
define('SMTP_HOST', '');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_PORT', 587);
define('SMTP_FROM', SMTP_USER);
define('MAIL_FROM', SMTP_FROM);
define('MAIL_FROM_NAME', 'EstimIA Bordeaux');

// IA — Multi-provider fallback
define('AI_OPENAI_KEY', '');
define('AI_ANTHROPIC_KEY', '');
define('AI_PERPLEXITY_KEY', '');
define('AI_MISTRAL_KEY', '');

// Sécurité
define('ADMIN_EMAIL', 'admin@estimia-bordeaux.fr');
define('SECRET_KEY', bin2hex(random_bytes(32)));

// Chemins
define('BASE_URL', 'https://bordeaux.estimia.fr');
define('BASE_PATH', __DIR__ . '/..');

require_once BASE_PATH . '/includes/error-handler.php';
