<?php
// app/utils/MailService.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

class MailService
{
    public static function sendStatusEmail($email, $name, $orderId, $status)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'mailhog'; // Docker service name
            $mail->Port       = 1025;
            $mail->SMTPAuth   = false;

            $mail->setFrom('no-reply@gtauto.com', 'Grand Transmission Auto');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Your Order #$orderId Status Has Been Updated";
            $mail->Body    = "
                <p>Dear <strong>$name</strong>,</p>
                <p>Your order <strong>#$orderId</strong> status has changed to <strong style='color:#0d6efd'>" . ucfirst($status) . "</strong>.</p>
                <p>Thank you for choosing Grand Transmission Auto!</p>
                <hr>
                <small>This is an automated message. Please do not reply.</small>
            ";
            $mail->AltBody = "Hello $name, your order #$orderId status is now: $status.";

            $mail->send();
            error_log("✅ MailService: Email sent to $email for order #$orderId");
            return true;
        } catch (Exception $e) {
            error_log("❌ MailService: Failed to send email: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>
