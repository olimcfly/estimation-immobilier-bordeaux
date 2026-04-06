<?php
// Configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = 'password';
$dbName = 'skyline';
$backupDir = __DIR__ . '/backups/';
$date = date('Y-m-d_H-i-s');

// Créer le répertoire de sauvegarde s'il n'existe pas
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Commande mysqldump
$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s > %s%s.sql',
    escapeshellarg($dbHost),
    escapeshellarg($dbUser),
    escapeshellarg($dbPass),
    escapeshellarg($dbName),
    escapeshellarg($backupDir),
    escapeshellarg($date)
);

exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo "Sauvegarde réussie : $backupDir$date.sql";
} else {
    echo 'Échec de la sauvegarde';
}
?>
