<?php

/**
 * Test multi-config automatique SMTP
 * Boucle sur tous les hosts et ports pour tester la connectivité SMTP.
 *
 * Usage : php tests/full-test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$hosts = [
    'mail1.o2switch.net',
    'mail.estimation-immobilier-nantes.fr',
    'estimation-immobilier-nantes.fr',
];

$ports = [465, 587];

$smtpUser = $_ENV['MAIL_SMTP_USER'] ?? $_ENV['MAIL_USERNAME'] ?? '';
$smtpPass = $_ENV['MAIL_SMTP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? '';
$mailFrom = $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? 'contact@estimation-immobilier-nantes.fr';
$mailTo   = $_ENV['MAIL_TEST_TO'] ?? $mailFrom;

// Load .env if dotenv is available
if (file_exists(__DIR__ . '/../.env') && class_exists('Dotenv\\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
    $smtpUser = $_ENV['MAIL_SMTP_USER'] ?? $_ENV['MAIL_USERNAME'] ?? $smtpUser;
    $smtpPass = $_ENV['MAIL_SMTP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? $smtpPass;
    $mailFrom = $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? $mailFrom;
    $mailTo   = $_ENV['MAIL_TEST_TO'] ?? $mailFrom;
}

echo "=============================================================\n";
echo "  TEST MULTI-CONFIG SMTP AUTOMATIQUE\n";
echo "=============================================================\n";
echo "User : " . ($smtpUser ?: '(non défini)') . "\n";
echo "To   : " . $mailTo . "\n";
echo "Date : " . date('Y-m-d H:i:s') . "\n";
echo "=============================================================\n\n";

$results = [];
$total   = 0;
$success = 0;
$fail    = 0;

foreach ($hosts as $host) {
    foreach ($ports as $port) {
        $total++;
        $encryption = ($port === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $encLabel   = ($port === 465) ? 'SSL' : 'TLS';
        $label      = "$host:$port ($encLabel)";

        echo "── Test #$total : $label\n";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->Port       = $port;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = $encryption;
            $mail->Timeout    = 10;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($mailFrom, 'Test Multi-Config');
            $mail->addAddress($mailTo);
            $mail->isHTML(true);
            $mail->Subject = "Test SMTP - $label";
            $mail->Body    = "<h2>Test SMTP</h2><p>Config : <strong>$label</strong></p>"
                           . "<p>Date : " . date('Y-m-d H:i:s') . "</p>";
            $mail->AltBody = "Test SMTP - Config : $label - " . date('Y-m-d H:i:s');

            $mail->send();

            echo "   ✅ SUCCÈS\n\n";
            $results[] = ['config' => $label, 'status' => 'OK', 'error' => ''];
            $success++;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            echo "   ❌ ÉCHEC : $error\n\n";
            $results[] = ['config' => $label, 'status' => 'FAIL', 'error' => $error];
            $fail++;
        }
    }
}

echo "=============================================================\n";
echo "  RÉCAPITULATIF\n";
echo "=============================================================\n";
echo sprintf("  Total : %d  |  Succès : %d  |  Échecs : %d\n\n", $total, $success, $fail);

echo str_pad('CONFIG', 55) . str_pad('STATUT', 10) . "ERREUR\n";
echo str_repeat('─', 100) . "\n";

foreach ($results as $r) {
    $statusIcon = $r['status'] === 'OK' ? '✅' : '❌';
    echo str_pad($r['config'], 55)
       . str_pad("$statusIcon {$r['status']}", 10)
       . ($r['error'] ? substr($r['error'], 0, 60) : '')
       . "\n";
}

echo str_repeat('─', 100) . "\n";

exit($fail === $total ? 1 : 0);
