<?php

/**
 * Setup script to create the admin_users table and seed the default admin account.
 *
 * Usage: php database/setup-admin.php
 *
 * Environment variables (or edit values below):
 *   ADMIN_EMAIL - Admin email (default: admin@estimation-immobilier-bordeaux.fr)
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Models\AdminUser;

$email = $_ENV['ADMIN_EMAIL'] ?? 'admin@estimation-immobilier-bordeaux.fr';

echo "Creating admin_users table...\n";
AdminUser::createTable();
echo "Table created.\n";

echo "Seeding admin user: {$email}\n";
AdminUser::seedDefaultAdmin($email);
echo "Admin user ready.\n";
echo "\nYou can now log in at: https://estimation-immobilier-bordeaux.fr/admin/login\n";
echo "A login code will be sent to {$email}\n";
