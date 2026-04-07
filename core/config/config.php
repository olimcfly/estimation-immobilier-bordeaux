<?php
// Configuration EstimIA - Bordeaux
require_once __DIR__ . '/constantes.php';

define('DEBUG_MODE', false);
define('MAINTENANCE_MODE', false);
define('SITE_NAME', 'EstimIA');
define('CITY_NAME', 'Bordeaux');
define('SITE_PHONE', '');

// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'sc2tasq5564_bordeaux');
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

// Google Ads
define('GOOGLE_ADS_DEVELOPER_TOKEN', '');
define('GOOGLE_ADS_CUSTOMER_ID', '');
define('GOOGLE_ADS_CLIENT_ID', '');
define('GOOGLE_ADS_CLIENT_SECRET', '');
define('GOOGLE_ADS_REFRESH_TOKEN', '');

// SEO / Ads lexicon seeds (overridable by DB in production)
define('SEO_KEYWORD_SEEDS', [
    'estimer' => [
        'estimation gratuite appartement bordeaux',
        'prix m2 bordeaux chartrons',
    ],
    'vendre' => [
        'vendre maison merignac rapidement',
        'vendre appartement bordeaux',
    ],
    'acheter' => [
        'acheter loft bordeaux centre',
    ],
    'investir' => [
        'investir immobilier bordeaux',
    ],
    'blog' => [
        'prix immobilier bordeaux 2026',
    ],
]);

// Sécurité
define('ADMIN_EMAIL', 'admin@estimia-bordeaux.fr');
define('SECRET_KEY', bin2hex(random_bytes(32)));

// Chemins
define('BASE_URL', 'https://bordeaux.estimia.fr');
define('BASE_PATH', __DIR__ . '/..');

require_once BASE_PATH . '/includes/error-handler.php';
