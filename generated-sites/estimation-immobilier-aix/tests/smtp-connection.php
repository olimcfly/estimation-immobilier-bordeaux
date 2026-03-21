<?php
/**
 * Test connexion SMTP bas niveau via fsockopen
 *
 * Usage: php tests/smtp-connection.php
 *
 * Vérifie si le serveur SMTP répond sur le host/port configuré.
 * Teste : connexion socket, SSL/TLS, EHLO, mécanismes AUTH, authentification.
 */

// Charger les variables d'environnement depuis .env (sans dépendance externe)
$dotenvFile = __DIR__ . '/../.env';
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $_ENV[$key] = $value;
        }
    }
}

// Récupérer la config SMTP
$host = $_ENV['MAIL_SMTP_HOST'] ?? $_ENV['MAIL_HOST'] ?? '';
$port = (int) ($_ENV['MAIL_SMTP_PORT'] ?? $_ENV['MAIL_PORT'] ?? 587);
$encryption = $_ENV['MAIL_SMTP_ENCRYPTION'] ?? $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
$username = $_ENV['MAIL_SMTP_USER'] ?? $_ENV['MAIL_USERNAME'] ?? '';
$password = $_ENV['MAIL_SMTP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? '';
$timeout = 10; // secondes

echo "=== Test connexion SMTP bas niveau ===\n\n";
echo "Host       : {$host}\n";
echo "Port       : {$port}\n";
echo "Encryption : {$encryption}\n";
echo "Username   : " . ($username !== '' ? substr($username, 0, 3) . '***' : '(vide)') . "\n";
echo "Timeout    : {$timeout}s\n\n";

if (empty($host)) {
    echo "❌ ERREUR : Aucun host SMTP configuré (MAIL_SMTP_HOST / MAIL_HOST)\n";
    exit(1);
}

// ── Étape 1 : Connexion socket ──────────────────────────────────────
$errno = 0;
$errstr = '';

// Pour le port 465 (SMTPS), on wrappe en SSL directement
$connectHost = $host;
if ($port === 465 || $encryption === 'ssl') {
    $connectHost = 'ssl://' . $host;
    echo "[1/5] Connexion SSL vers {$connectHost}:{$port}...\n";
} else {
    echo "[1/5] Connexion vers {$host}:{$port}...\n";
}

$socket = @fsockopen($connectHost, $port, $errno, $errstr, $timeout);

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

stream_set_timeout($socket, $timeout);

// ── Étape 2 : Bannière SMTP ────────────────────────────────────────
$banner = trim((string) fgets($socket, 1024));
$bannerCode = (int) substr($banner, 0, 3);

echo "[2/5] Bannière : {$banner}\n";

if ($bannerCode !== 220) {
    echo "❌ RÉPONSE INATTENDUE — Code {$bannerCode} au lieu de 220\n";
    fclose($socket);
    exit(4);
}
echo "      ✅ Serveur SMTP répond (code 220)\n\n";

// ── Étape 3 : EHLO ─────────────────────────────────────────────────
$ehloHost = gethostname() ?: 'localhost';
fwrite($socket, "EHLO {$ehloHost}\r\n");

$ehloResponse = '';
$authMechanisms = [];

// Lire toutes les lignes de la réponse EHLO (multi-ligne : "250-" puis "250 ")
while (true) {
    $line = fgets($socket, 1024);
    if ($line === false) {
        break;
    }
    $line = trim($line);
    $ehloResponse .= "      {$line}\n";

    // Détecter les mécanismes AUTH
    if (preg_match('/^250[- ]AUTH[= ](.+)$/i', $line, $m)) {
        $authMechanisms = array_map('trim', explode(' ', strtoupper($m[1])));
    }

    // Dernière ligne : "250 " (espace, pas tiret)
    if (str_starts_with($line, '250 ')) {
        break;
    }
}

echo "[3/5] EHLO :\n{$ehloResponse}\n";

// ── Étape 4 : Mécanismes AUTH ───────────────────────────────────────
echo "[4/5] Mécanismes AUTH supportés : ";
if (empty($authMechanisms)) {
    echo "❌ Aucun mécanisme AUTH annoncé par le serveur\n";
    echo "      Le serveur ne supporte peut-être pas l'authentification,\n";
    echo "      ou STARTTLS est requis avant AUTH.\n\n";
} else {
    echo implode(', ', $authMechanisms) . "\n";

    if (in_array('LOGIN', $authMechanisms, true)) {
        echo "      ✅ AUTH LOGIN supporté\n";
    }
    if (in_array('PLAIN', $authMechanisms, true)) {
        echo "      ✅ AUTH PLAIN supporté\n";
    }
    if (in_array('CRAM-MD5', $authMechanisms, true)) {
        echo "      ✅ AUTH CRAM-MD5 supporté\n";
    }
    echo "\n";
}

// ── Étape 5 : Test authentification ─────────────────────────────────
echo "[5/5] Test authentification :\n";

if ($username === '' || $password === '') {
    echo "      ⚠️  Username/password non configurés — auth non testée\n";
    fwrite($socket, "QUIT\r\n");
    fgets($socket, 1024);
    fclose($socket);
    exit(0);
}

$authSuccess = false;
$authMethod = '';

// Tenter AUTH LOGIN si supporté
if (in_array('LOGIN', $authMechanisms, true)) {
    $authMethod = 'LOGIN';
    fwrite($socket, "AUTH LOGIN\r\n");
    $resp = trim((string) fgets($socket, 1024));
    if (str_starts_with($resp, '334')) {
        fwrite($socket, base64_encode($username) . "\r\n");
        $resp = trim((string) fgets($socket, 1024));
        if (str_starts_with($resp, '334')) {
            fwrite($socket, base64_encode($password) . "\r\n");
            $resp = trim((string) fgets($socket, 1024));
            if (str_starts_with($resp, '235')) {
                $authSuccess = true;
            } else {
                echo "      ❌ AUTH LOGIN échoué : {$resp}\n";
            }
        } else {
            echo "      ❌ AUTH LOGIN échoué (username) : {$resp}\n";
        }
    } else {
        echo "      ❌ AUTH LOGIN refusé : {$resp}\n";
    }
}

// Tenter AUTH PLAIN si LOGIN n'a pas fonctionné
if (!$authSuccess && in_array('PLAIN', $authMechanisms, true)) {
    $authMethod = 'PLAIN';
    $authString = base64_encode("\0{$username}\0{$password}");
    fwrite($socket, "AUTH PLAIN {$authString}\r\n");
    $resp = trim((string) fgets($socket, 1024));
    if (str_starts_with($resp, '235')) {
        $authSuccess = true;
    } else {
        echo "      ❌ AUTH PLAIN échoué : {$resp}\n";
    }
}

if ($authSuccess) {
    echo "      ✅ Authentification réussie via AUTH {$authMethod}\n";
} elseif (!empty($authMechanisms)) {
    echo "      ❌ Authentification échouée avec tous les mécanismes testés\n";
    echo "      Vérifiez MAIL_USERNAME et MAIL_PASSWORD dans .env\n";
}

// ── Résumé ──────────────────────────────────────────────────────────
echo "\n=== Résumé ===\n";
echo "Connexion socket : ✅\n";
echo "Bannière SMTP    : ✅\n";
echo "EHLO             : ✅\n";
echo "AUTH mécanismes  : " . (empty($authMechanisms) ? '❌' : '✅ ' . implode(', ', $authMechanisms)) . "\n";
echo "Authentification : " . ($authSuccess ? '✅' : '❌') . "\n";

// Fermer proprement
fwrite($socket, "QUIT\r\n");
fgets($socket, 1024);
fclose($socket);

exit($authSuccess ? 0 : 5);
