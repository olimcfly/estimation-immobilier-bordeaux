<?php

/**
 * Test d'envoi d'email via SMTP avec PHPMailer
 *
 * Usage :
 *   php tests/send-test.php [destinataire@email.com]
 *
 * Variables d'environnement requises (ou définies dans .env) :
 *   MAIL_SMTP_HOST, MAIL_SMTP_PORT, MAIL_SMTP_USER, MAIL_SMTP_PASS
 *
 * SMTP auth est obligatoire — sans authentification l'email sera rejeté.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ---------------------------------------------------------------------------
// 1. Charger les variables d'environnement depuis .env si présent
// ---------------------------------------------------------------------------
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// ---------------------------------------------------------------------------
// 2. Résoudre la configuration SMTP
// ---------------------------------------------------------------------------
$smtpHost       = $_ENV['MAIL_SMTP_HOST']       ?? $_ENV['MAIL_HOST']       ?? '';
$smtpPort       = (int) ($_ENV['MAIL_SMTP_PORT'] ?? $_ENV['MAIL_PORT']      ?? 587);
$smtpUser       = $_ENV['MAIL_SMTP_USER']        ?? $_ENV['MAIL_USERNAME']  ?? '';
$smtpPass       = $_ENV['MAIL_SMTP_PASS']        ?? $_ENV['MAIL_PASSWORD']  ?? '';
$smtpEncryption = $_ENV['MAIL_SMTP_ENCRYPTION']  ?? $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
$fromAddress    = $_ENV['MAIL_FROM_ADDRESS']      ?? $_ENV['MAIL_FROM']      ?? 'no-reply@estimation-immobilier-lannion.fr';
$fromName       = $_ENV['MAIL_FROM_NAME']         ?? 'Estimation Immobilier Lannion';

// Destinataire = argument CLI ou SMTP user par défaut (envoi à soi-même)
$recipient = $argv[1] ?? $smtpUser;

// ---------------------------------------------------------------------------
// 3. Vérifications pré-envoi
// ---------------------------------------------------------------------------
echo "=== Test d'envoi d'email SMTP ===" . PHP_EOL . PHP_EOL;

$errors = [];
if ($smtpHost === '') {
    $errors[] = 'MAIL_SMTP_HOST non défini';
}
if ($smtpUser === '') {
    $errors[] = 'MAIL_SMTP_USER non défini (auth SMTP obligatoire)';
}
if ($smtpPass === '') {
    $errors[] = 'MAIL_SMTP_PASS non défini (auth SMTP obligatoire)';
}
if ($recipient === '') {
    $errors[] = 'Aucun destinataire — passez une adresse en argument ou définissez MAIL_SMTP_USER';
}

if ($errors !== []) {
    echo "ERREUR — Configuration manquante :" . PHP_EOL;
    foreach ($errors as $err) {
        echo "  • $err" . PHP_EOL;
    }
    echo PHP_EOL . "Définissez les variables dans .env ou en environnement, puis relancez." . PHP_EOL;
    exit(1);
}

echo "Host       : $smtpHost" . PHP_EOL;
echo "Port       : $smtpPort" . PHP_EOL;
echo "Encryption : $smtpEncryption" . PHP_EOL;
echo "User       : $smtpUser" . PHP_EOL;
echo "From       : $fromAddress ($fromName)" . PHP_EOL;
echo "To         : $recipient" . PHP_EOL;
echo PHP_EOL;

// ---------------------------------------------------------------------------
// 4. Envoi avec PHPMailer
// ---------------------------------------------------------------------------
$mail = new PHPMailer(true);

try {
    // Debug SMTP (niveau 2 = messages client + serveur)
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function (string $str, int $level) {
        echo "  [SMTP] $str";
    };

    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->Port       = $smtpPort;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = $smtpEncryption === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($recipient);

    $now = date('Y-m-d H:i:s');

    $mail->isHTML(true);
    $mail->Subject = "Test SMTP — Estimation Immobilier Lannion ($now)";
    $mail->Body    = <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #8B1538;">Test d'envoi SMTP réussi</h2>
        <p>Cet email confirme que la configuration SMTP fonctionne correctement.</p>
        <table style="border-collapse: collapse; width: 100%; margin-top: 15px;">
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Date</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$now}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Serveur SMTP</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$smtpHost}:{$smtpPort}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Chiffrement</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$smtpEncryption}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Expéditeur</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$fromAddress}</td></tr>
        </table>
        <p style="margin-top: 20px; color: #6b6459; font-size: 12px;">
            Envoyé depuis le script de test — Estimation Immobilier Lannion
        </p>
    </div>
    HTML;
    $mail->AltBody = "Test SMTP réussi — Estimation Immobilier Lannion\n"
        . "Serveur: $smtpHost:$smtpPort | Chiffrement: $smtpEncryption\n"
        . "Date: " . date('Y-m-d H:i:s');

    $mail->send();

    echo PHP_EOL . "✅ Email envoyé avec succès à $recipient" . PHP_EOL;
    exit(0);

} catch (Exception $e) {
    echo PHP_EOL . "❌ Échec de l'envoi : " . $mail->ErrorInfo . PHP_EOL;
    exit(1);
} catch (\Throwable $e) {
    echo PHP_EOL . "❌ Erreur inattendue : " . $e->getMessage() . PHP_EOL;
    exit(1);
}
