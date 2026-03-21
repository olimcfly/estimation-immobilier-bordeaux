<?php

declare(strict_types=1);

require_once __DIR__ . '/app/core/bootstrap.php';

use App\Core\Config;
use App\Services\SmtpLogger;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Test SMTP ===\n\n";

$host = (string) Config::get('mail.smtp_host');
$port = (int) Config::get('mail.smtp_port', 587);
$user = (string) Config::get('mail.smtp_user');
$encryption = (string) Config::get('mail.smtp_encryption', 'tls');

echo "Host       : " . ($host ?: '(non défini)') . "\n";
echo "Port       : {$port}\n";
echo "User       : " . ($user ?: '(non défini)') . "\n";
echo "Encryption : {$encryption}\n\n";

if ($host === '') {
    $msg = 'SMTP host non configuré dans .env';
    echo "Résultat : ECHEC\nErreur   : {$msg}\n";
    SmtpLogger::log('(vide)', $port, 'ECHEC', $msg);
    exit(1);
}

// Test socket connection to SMTP server
echo "Connexion à {$host}:{$port} ... ";
$errno = 0;
$errstr = '';
$timeout = 10;

$socket = @fsockopen(
    ($encryption === 'ssl' ? 'ssl://' : '') . $host,
    $port,
    $errno,
    $errstr,
    $timeout
);

if ($socket === false) {
    $error = "Connexion impossible : [{$errno}] {$errstr}";
    echo "ECHEC\nErreur : {$error}\n";
    SmtpLogger::log($host, $port, 'ECHEC', $error);
    exit(1);
}

// Read server greeting
$greeting = fgets($socket, 512);
fclose($socket);

if ($greeting === false || !str_starts_with(trim($greeting), '220')) {
    $error = 'Réponse serveur inattendue : ' . ($greeting ?: '(aucune réponse)');
    echo "ECHEC\nErreur : {$error}\n";
    SmtpLogger::log($host, $port, 'ECHEC', $error);
    exit(1);
}

echo "OK\n";
echo "Réponse serveur : " . trim($greeting) . "\n";

SmtpLogger::log($host, $port, 'OK');

echo "\n=== Test SMTP terminé avec succès ===\n";
echo "Log enregistré dans logs/smtp_test.log\n";
