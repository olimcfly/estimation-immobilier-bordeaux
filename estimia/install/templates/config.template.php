<?php
// Configuration EstimIA - Généré le {{INSTALL_DATE}}
// NE PAS MODIFIER MANUELLEMENT SAUF SI VOUS SAVEZ CE QUE VOUS FAITES

// ===== BASE DE DONNÉES =====
define('DB_HOST', '{{DB_HOST}}');
define('DB_NAME', '{{DB_NAME}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');
define('DB_CHARSET', 'utf8mb4');

// ===== SITE =====
define('SITE_NAME', '{{SITE_NAME}}');
define('SITE_URL', '{{SITE_URL}}');
define('SITE_COLOR', '{{SITE_COLOR}}');
define('SITE_PHONE', '{{PHONE}}');
define('SECRET_KEY', '{{SECRET_KEY}}');

// ===== ZONE GÉOGRAPHIQUE =====
define('CITY_NAME', '{{CITY_NAME}}');
define('CITY_LAT', '{{CITY_LAT}}');
define('CITY_LNG', '{{CITY_LNG}}');
define('CITY_RADIUS_KM', '{{CITY_RADIUS}}');

// ===== SMTP =====
define('SMTP_HOST', '{{SMTP_HOST}}');
define('SMTP_PORT', '{{SMTP_PORT}}');
define('SMTP_USER', '{{SMTP_USER}}');
define('SMTP_PASS', '{{SMTP_PASS}}');
define('SMTP_SECURE', '{{SMTP_SECURE}}');
define('SMTP_FROM_EMAIL', '{{SMTP_FROM}}');
define('SMTP_FROM_NAME', '{{SITE_NAME}}');

// ===== NOTIFICATIONS =====
define('ADMIN_EMAIL', '{{ADMIN_EMAIL}}');
define('NOTIF_NEW_ESTIMATION', '{{NOTIF_NEW_ESTIMATION}}');
define('NOTIF_NEW_RDV', '{{NOTIF_NEW_RDV}}');
define('NOTIF_HOT_LEAD', '{{NOTIF_HOT_LEAD}}');
define('NOTIF_WEEKLY_REPORT', '{{NOTIF_WEEKLY}}');

// ===== GOOGLE MAPS =====
define('GOOGLE_MAPS_API_KEY', '');  // À renseigner dans l'admin

// ===== AVANCÉ =====
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);
define('MAX_REQUESTS_PER_HOUR', 50);
define('INSTALLED', true);
define('INSTALL_DATE', '{{INSTALL_DATE}}');
