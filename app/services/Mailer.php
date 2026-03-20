<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);

        try {
            $smtpHost = (string) Config::get('mail.smtp_host');

            if ($smtpHost !== '') {
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->Port = (int) Config::get('mail.smtp_port', 587);
                $mail->SMTPAuth = true;
                $mail->Username = (string) Config::get('mail.smtp_user');
                $mail->Password = (string) Config::get('mail.smtp_pass');
                $mail->SMTPSecure = (string) Config::get('mail.smtp_encryption', 'tls');
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom(
                (string) Config::get('mail.from', 'no-reply@estimation-immobilier-bordeaux.fr'),
                (string) Config::get('mail.from_name', 'Estimation Immobilier Bordeaux')
            );
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        }
    }
}
