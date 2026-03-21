<?php
/**
 * Test connexion SMTP bas niveau via fsockopen
 *
 * Usage: php tests/smtp-connection.php
 *
 * Vérifie si le serveur SMTP répond sur le host/port configuré.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
$dotenvFile = __DIR__ . '/../.env';
if (file_exists($dotenvFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenvFile));
    $dotenv->load();
}

// Récupérer la config SMTP
$host = $_ENV['MAIL_SMTP_HOST'] ?? $_ENV['MAIL_HOST'] ?? '';
$port = (int) ($_ENV['MAIL_SMTP_PORT'] ?? $_ENV['MAIL_PORT'] ?? 587);
$timeout = 10; // secondes

echo "=== Test connexion SMTP bas niveau ===\n\n";
echo "Host : {$host}\n";
echo "Port : {$port}\n";
echo "Timeout : {$timeout}s\n\n";

if (empty($host)) {
    echo "❌ ERREUR : Aucun host SMTP configuré (MAIL_SMTP_HOST / MAIL_HOST)\n";
    exit(1);
}

// Tentative de connexion socket
$errno = 0;
$errstr = '';

echo "Connexion en cours...\n";

$socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

if (!$socket) {
    if ($errno === 110 || stripos($errstr, 'timed out') !== false) {
        echo "❌ TIMEOUT — Le serveur n'a pas répondu dans les {$timeout}s\n";
        echo "   Erreur [{$errno}] : {$errstr}\n";
        exit(2);
    }

    echo "❌ CONNECTION REFUSED — Impossible de se connecter à {$host}:{$port}\n";
    echo "   Erreur [{$errno}] : {$errstr}\n";
    exit(3);
}

// Lire la bannière SMTP (réponse initiale du serveur)
$response = fgets($socket, 1024);

if ($response === false) {
    echo "❌ ERREUR — Connexion ouverte mais aucune réponse du serveur\n";
    fclose($socket);
    exit(4);
}

$response = trim($response);
$code = (int) substr($response, 0, 3);

echo "Réponse serveur : {$response}\n\n";

if ($code === 220) {
    echo "✅ CONNEXION OK — Le serveur SMTP répond correctement\n";

    // Envoyer QUIT proprement
    fwrite($socket, "QUIT\r\n");
    fgets($socket, 1024);
    fclose($socket);
    exit(0);
}

echo "❌ RÉPONSE INATTENDUE — Code {$code} au lieu de 220\n";
fclose($socket);
exit(5);
