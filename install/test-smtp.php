<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function smtpHandshake(array $cfg): array
{
    $host = trim((string) ($cfg['smtp_host'] ?? ''));
    $port = (int) ($cfg['smtp_port'] ?? 465);
    $user = trim((string) ($cfg['smtp_user'] ?? ''));
    $pass = (string) ($cfg['smtp_pass'] ?? '');

    if ($host === '' || $user === '' || $pass === '') {
        return ['success' => false, 'message' => 'Paramètres SMTP incomplets.'];
    }

    $prefix = ($port === 465) ? 'ssl://' : '';
    $conn = @fsockopen($prefix . $host, $port, $errno, $errstr, 10);
    if (!$conn) {
        return ['success' => false, 'message' => "Connexion échouée : {$errstr} ({$errno})"];
    }

    $readReply = static function ($conn): string {
        $reply = '';
        while ($line = fgets($conn, 512)) {
            $reply .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        return $reply;
    };

    $readReply($conn);
    fputs($conn, "EHLO localhost\r\n");
    $readReply($conn);

    fputs($conn, "AUTH LOGIN\r\n");
    $auth = $readReply($conn);

    if (strpos($auth, '334') !== 0) {
        fputs($conn, "QUIT\r\n");
        fclose($conn);

        return ['success' => false, 'message' => 'AUTH LOGIN non supporté : ' . trim($auth)];
    }

    fputs($conn, base64_encode($user) . "\r\n");
    $readReply($conn);
    fputs($conn, base64_encode($pass) . "\r\n");
    $loginResult = $readReply($conn);

    fputs($conn, "QUIT\r\n");
    fclose($conn);

    if (strpos($loginResult, '235') === 0) {
        return ['success' => true, 'message' => 'Connexion SMTP réussie et authentification OK.'];
    }

    return ['success' => false, 'message' => 'Authentification échouée : ' . trim($loginResult)];
}

$result = smtpHandshake([
    'smtp_host' => $_POST['smtp_host'] ?? '',
    'smtp_port' => $_POST['smtp_port'] ?? '',
    'smtp_user' => $_POST['smtp_user'] ?? '',
    'smtp_pass' => $_POST['smtp_pass'] ?? '',
]);

if (!$result['success']) {
    http_response_code(422);
}

echo json_encode($result);
