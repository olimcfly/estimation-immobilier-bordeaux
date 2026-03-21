<?php

declare(strict_types=1);

/**
 * Page de debug email — Tâche 7
 * Teste la connectivité SMTP / IMAP et l'envoi réel.
 */

// Bootstrap minimal : charger l'env si disponible
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            putenv(trim($line));
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim(trim($value), '"\'');
        }
    }
}

$config = require dirname(__DIR__) . '/config/config.php';
$mail = $config['mail'];

// ── Résultats des tests ──────────────────────────────────────────────

$host       = $mail['smtp_host'] ?: 'mail1.o2switch.net';
$port       = $mail['smtp_port'] ?: 465;
$user       = $mail['smtp_user'];
$pass       = $mail['smtp_pass'];
$encryption = $mail['smtp_encryption'];
$from       = $mail['from'];
$fromName   = $mail['from_name'];

$results = [
    'host'  => $host,
    'port'  => $port,
    'smtp'  => false,
    'imap'  => false,
    'auth'  => false,
    'envoi' => false,
];

// 1) Test SMTP — connexion socket
$smtpError = '';
$errno = 0;
$errstr = '';
$prefix = ($port === 465) ? 'ssl://' : '';
$fp = @fsockopen($prefix . $host, $port, $errno, $errstr, 5);
if ($fp) {
    $banner = @fgets($fp, 512);
    if ($banner && str_starts_with(trim($banner), '220')) {
        $results['smtp'] = true;
    } else {
        $smtpError = 'Bannière inattendue : ' . htmlspecialchars((string) $banner);
    }
    @fclose($fp);
} else {
    $smtpError = "Connexion refusée ($errno) : " . htmlspecialchars($errstr);
}

// 2) Test IMAP
$imapError = '';
$imapFp = @fsockopen('ssl://' . $host, 993, $errno, $errstr, 5);
if ($imapFp) {
    $imapBanner = @fgets($imapFp, 512);
    if ($imapBanner && str_contains($imapBanner, 'OK')) {
        $results['imap'] = true;
    } else {
        $imapError = 'Bannière inattendue : ' . htmlspecialchars((string) $imapBanner);
    }
    @fclose($imapFp);
} else {
    $imapError = "Connexion refusée ($errno) : " . htmlspecialchars($errstr);
}

// 3) Test Auth SMTP (EHLO + AUTH LOGIN)
$authError = '';
if ($results['smtp'] && $user !== '' && $pass !== '') {
    $fp = @fsockopen($prefix . $host, $port, $errno, $errstr, 5);
    if ($fp) {
        @fgets($fp, 512); // banner
        fwrite($fp, "EHLO test-mail.local\r\n");
        $ehloResp = '';
        while ($line = @fgets($fp, 512)) {
            $ehloResp .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        fwrite($fp, "AUTH LOGIN\r\n");
        $authResp = @fgets($fp, 512);
        if ($authResp && str_starts_with(trim($authResp), '334')) {
            fwrite($fp, base64_encode($user) . "\r\n");
            $userResp = @fgets($fp, 512);
            if ($userResp && str_starts_with(trim($userResp), '334')) {
                fwrite($fp, base64_encode($pass) . "\r\n");
                $passResp = @fgets($fp, 512);
                if ($passResp && str_starts_with(trim($passResp), '235')) {
                    $results['auth'] = true;
                } else {
                    $authError = 'Mot de passe refusé';
                }
            } else {
                $authError = 'Utilisateur refusé';
            }
        } else {
            $authError = 'AUTH LOGIN non supporté';
        }
        fwrite($fp, "QUIT\r\n");
        @fclose($fp);
    }
} elseif ($user === '' || $pass === '') {
    $authError = 'Identifiants SMTP non configurés dans .env';
}

// 4) Test envoi réel via mail() ou SMTP
$envoiError = '';
if ($results['auth']) {
    $adminEmail = $config['mail']['from'];
    $subject = '=?UTF-8?B?' . base64_encode('Test email debug — ' . date('Y-m-d H:i:s')) . '?=';
    $body = "Ceci est un email de test envoyé depuis test-mail.php.\r\nDate : " . date('Y-m-d H:i:s');

    $fp = @fsockopen($prefix . $host, $port, $errno, $errstr, 5);
    if ($fp) {
        @fgets($fp, 512);
        fwrite($fp, "EHLO test-mail.local\r\n");
        while ($line = @fgets($fp, 512)) {
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        fwrite($fp, "AUTH LOGIN\r\n");
        @fgets($fp, 512);
        fwrite($fp, base64_encode($user) . "\r\n");
        @fgets($fp, 512);
        fwrite($fp, base64_encode($pass) . "\r\n");
        $authLine = @fgets($fp, 512);

        if ($authLine && str_starts_with(trim($authLine), '235')) {
            fwrite($fp, "MAIL FROM:<{$from}>\r\n");
            @fgets($fp, 512);
            fwrite($fp, "RCPT TO:<{$adminEmail}>\r\n");
            @fgets($fp, 512);
            fwrite($fp, "DATA\r\n");
            @fgets($fp, 512);

            $headers = "From: {$fromName} <{$from}>\r\n";
            $headers .= "To: {$adminEmail}\r\n";
            $headers .= "Subject: {$subject}\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "\r\n";
            $headers .= $body . "\r\n.\r\n";

            fwrite($fp, $headers);
            $dataResp = @fgets($fp, 512);
            if ($dataResp && str_starts_with(trim($dataResp), '250')) {
                $results['envoi'] = true;
            } else {
                $envoiError = 'Réponse DATA : ' . htmlspecialchars(trim((string) $dataResp));
            }
        } else {
            $envoiError = 'Auth échouée lors de l\'envoi';
        }
        fwrite($fp, "QUIT\r\n");
        @fclose($fp);
    }
} else {
    $envoiError = 'Auth requise avant envoi';
}

// ── Rendu HTML ───────────────────────────────────────────────────────

$check = static fn(bool $ok): string => $ok
    ? '<span style="color:#22c55e;font-weight:bold">&#10004;</span>'
    : '<span style="color:#e24b4a;font-weight:bold">&#10008;</span>';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Mail — Debug</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #faf9f7; color: #1a1410; padding: 2rem; }
        h1 { font-size: 1.5rem; margin-bottom: 1.5rem; color: #8B1538; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        th, td { padding: .75rem 1rem; text-align: left; border-bottom: 1px solid #e8dfd7; }
        th { background: #8B1538; color: #fff; font-weight: 600; font-size: .85rem; text-transform: uppercase; letter-spacing: .03em; }
        td { font-size: .95rem; }
        tr:last-child td { border-bottom: none; }
        .mono { font-family: 'SF Mono', 'Fira Code', monospace; font-size: .85rem; color: #6b6459; }
        .detail { font-size: .8rem; color: #999; margin-top: .25rem; }
        .section { margin-top: 2rem; max-width: 800px; }
        .section h2 { font-size: 1.1rem; margin-bottom: .5rem; color: #333; }
        .env-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; }
        .env-item { background: #fff; padding: .5rem .75rem; border-radius: 4px; border: 1px solid #e8dfd7; font-size: .85rem; }
        .env-key { font-weight: 600; color: #8B1538; }
        .env-val { font-family: monospace; color: #6b6459; }
    </style>
</head>
<body>
    <h1>&#9993; Test Mail — Debug</h1>

    <table>
        <thead>
            <tr>
                <th>Host</th>
                <th>Port</th>
                <th>SMTP</th>
                <th>IMAP</th>
                <th>Auth</th>
                <th>Envoi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="mono"><?= htmlspecialchars($results['host']) ?></td>
                <td class="mono"><?= $results['port'] ?></td>
                <td>
                    <?= $check($results['smtp']) ?>
                    <?php if ($smtpError): ?><div class="detail"><?= $smtpError ?></div><?php endif; ?>
                </td>
                <td>
                    <?= $check($results['imap']) ?>
                    <?php if ($imapError): ?><div class="detail"><?= $imapError ?></div><?php endif; ?>
                </td>
                <td>
                    <?= $check($results['auth']) ?>
                    <?php if ($authError): ?><div class="detail"><?= $authError ?></div><?php endif; ?>
                </td>
                <td>
                    <?= $check($results['envoi']) ?>
                    <?php if ($envoiError): ?><div class="detail"><?= $envoiError ?></div><?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section">
        <h2>Configuration .env</h2>
        <div class="env-grid">
            <div class="env-item">
                <span class="env-key">MAIL_HOST</span>
                <span class="env-val"><?= htmlspecialchars($host) ?></span>
            </div>
            <div class="env-item">
                <span class="env-key">MAIL_PORT</span>
                <span class="env-val"><?= $port ?></span>
            </div>
            <div class="env-item">
                <span class="env-key">MAIL_ENCRYPTION</span>
                <span class="env-val"><?= htmlspecialchars($encryption) ?></span>
            </div>
            <div class="env-item">
                <span class="env-key">MAIL_FROM</span>
                <span class="env-val"><?= htmlspecialchars($from) ?></span>
            </div>
            <div class="env-item">
                <span class="env-key">MAIL_USERNAME</span>
                <span class="env-val"><?= $user ? '••••' . substr($user, -4) : '<em>non défini</em>' ?></span>
            </div>
            <div class="env-item">
                <span class="env-key">MAIL_PASSWORD</span>
                <span class="env-val"><?= $pass ? '••••••••' : '<em>non défini</em>' ?></span>
            </div>
        </div>
    </div>

    <p style="margin-top:2rem;font-size:.8rem;color:#999;">
        Généré le <?= date('d/m/Y à H:i:s') ?> — PHP <?= PHP_VERSION ?>
    </p>
</body>
</html>
