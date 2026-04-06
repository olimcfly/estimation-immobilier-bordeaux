<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Mailer
{
    private string $fromEmail;
    private string $fromName;
    /** @var array<string,mixed> */
    private array $smtpConfig;

    public function __construct()
    {
        $this->fromEmail = defined('SMTP_FROM_EMAIL') ? (string) SMTP_FROM_EMAIL : 'no-reply@localhost';
        $this->fromName = defined('SMTP_FROM_NAME') ? (string) SMTP_FROM_NAME : 'EstimIA';
        $this->smtpConfig = [
            'host' => defined('SMTP_HOST') ? (string) SMTP_HOST : 'localhost',
            'port' => defined('SMTP_PORT') ? (int) SMTP_PORT : 25,
            'user' => defined('SMTP_USER') ? (string) SMTP_USER : '',
            'pass' => defined('SMTP_PASS') ? (string) SMTP_PASS : '',
            'secure' => defined('SMTP_SECURE') ? (string) SMTP_SECURE : '',
        ];
    }

    public function send(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->logEmail($to, $subject, false);
            return false;
        }

        $success = false;

        $vendorAutoload = __DIR__ . '/../vendor/autoload.php';
        if (is_file($vendorAutoload)) {
            require_once $vendorAutoload;
        }

        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $success = $this->sendWithPHPMailer($to, $subject, $htmlBody, $textBody);
            if ($success) {
                $this->logEmail($to, $subject, true);
                return true;
            }
        }

        $success = $this->sendWithMail($to, $subject, $htmlBody, $textBody);
        if (!$success) {
            $success = $this->sendWithBasicMail($to, $subject, strip_tags($textBody !== '' ? $textBody : $htmlBody));
        }

        $this->logEmail($to, $subject, $success);
        return $success;
    }

    private function sendWithPHPMailer(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string) $this->smtpConfig['host'];
            $mail->Port = (int) $this->smtpConfig['port'];
            $mail->SMTPAuth = $this->smtpConfig['user'] !== '';
            $mail->Username = (string) $this->smtpConfig['user'];
            $mail->Password = (string) $this->smtpConfig['pass'];

            $secure = strtolower((string) $this->smtpConfig['secure']);
            if (in_array($secure, ['ssl', 'tls'], true)) {
                $mail->SMTPSecure = $secure;
            }

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody !== '' ? $textBody : strip_tags($htmlBody);

            return $mail->send();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function sendWithMail(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        $boundary = md5(uniqid((string) time(), true));
        $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "X-Mailer: EstimIA\r\n";

        $plainBody = $textBody !== '' ? $textBody : strip_tags($htmlBody);
        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $body .= $plainBody . "\r\n\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";
        $body .= "--{$boundary}--";

        return (bool) @mail($to, $subject, $body, $headers);
    }

    private function sendWithBasicMail(string $to, string $subject, string $textBody): bool
    {
        $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        return (bool) @mail($to, $subject, $textBody, $headers);
    }

    private function logEmail(string $to, string $subject, bool $success): void
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('INSERT INTO activity_log (action, details, admin_email) VALUES (:action, :details, :admin_email)');
            $stmt->execute([
                'action' => 'email_sent',
                'details' => json_encode([
                    'to' => $to,
                    'subject' => $subject,
                    'success' => $success,
                    'sent_at' => date('c'),
                ], JSON_UNESCAPED_UNICODE),
                'admin_email' => defined('ADMIN_EMAIL') ? (string) ADMIN_EMAIL : null,
            ]);
        } catch (Throwable $e) {
            // silence volontaire
        }
    }
}
