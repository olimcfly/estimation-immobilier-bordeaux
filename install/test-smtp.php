<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function s(?string $value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

$host = s($_POST['smtp_host'] ?? '');
$port = (int) ($_POST['smtp_port'] ?? 465);
$user = s($_POST['smtp_user'] ?? '');
$pass = s($_POST['smtp_pass'] ?? '');
$adminEmail = s($_POST['admin_email'] ?? '');

if ($host === '' || $user === '' || $pass === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'SMTP_HOST, SMTP_USER, SMTP_PASS requis.']);
    exit;
}

if ($adminEmail !== '' && !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'ADMIN_EMAIL invalide.']);
    exit;
}

$remote = $port === 465 ? 'ssl://' . $host : $host;
$socket = @fsockopen($remote, $port, $errno, $errstr, 10);
if (!$socket) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Connexion SMTP impossible: {$errstr} ({$errno})"]);
    exit;
}

$read = static function ($stream): string {
    $response = '';
    while (($line = fgets($stream, 512)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    return $response;
};

$banner = $read($socket);
fwrite($socket, "EHLO localhost\r\n");
$ehlo = $read($socket);

if ($port !== 465 && stripos($ehlo, 'STARTTLS') !== false) {
    fwrite($socket, "STARTTLS\r\n");
    $starttls = $read($socket);
    if (strpos($starttls, '220') === 0 && stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fwrite($socket, "EHLO localhost\r\n");
        $read($socket);
    }
}

fwrite($socket, "AUTH LOGIN\r\n");
$auth = $read($socket);
if (strpos($auth, '334') !== 0) {
    fclose($socket);
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'AUTH LOGIN non accepté: ' . trim($auth)]);
    exit;
}

fwrite($socket, base64_encode($user) . "\r\n");
$read($socket);
fwrite($socket, base64_encode($pass) . "\r\n");
$login = $read($socket);
fwrite($socket, "QUIT\r\n");
fclose($socket);

if (strpos($login, '235') !== 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Échec authentification SMTP: ' . trim($login)]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Test SMTP réussi (connexion + authentification).']);
