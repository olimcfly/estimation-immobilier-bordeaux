<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true) && !is_dir($backupDir)) {
    throw new RuntimeException('Impossible de créer le dossier de sauvegarde.');
}

$host = defined('DB_HOST') ? (string) DB_HOST : '';
$dbName = defined('DB_NAME') ? (string) DB_NAME : '';
$user = defined('DB_USER') ? (string) DB_USER : '';
$pass = defined('DB_PASS') ? (string) DB_PASS : '';

if ($host === '' || $dbName === '' || $user === '') {
    throw new RuntimeException('Configuration DB incomplète.');
}

$backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';

$command = sprintf(
    'mysqldump --single-transaction --quick --host=%s --user=%s --password=%s %s > %s 2>&1',
    escapeshellarg($host),
    escapeshellarg($user),
    escapeshellarg($pass),
    escapeshellarg($dbName),
    escapeshellarg($backupFile)
);

exec($command, $output, $exitCode);

if ($exitCode !== 0) {
    throw new RuntimeException('Échec mysqldump: ' . implode("
", $output));
}

echo 'Sauvegarde créée : ' . $backupFile . PHP_EOL;
