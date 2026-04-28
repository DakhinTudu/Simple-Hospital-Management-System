<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

if (!function_exists('hms_send_email')) {
    function hms_send_email($toEmail, $toName, $subject, $htmlBody, $altBody = '')
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = HMS_SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = HMS_SMTP_USERNAME;
            $mail->Password = HMS_SMTP_PASSWORD;
            $mail->SMTPSecure = HMS_SMTP_SECURE;
            $mail->Port = (int)HMS_SMTP_PORT;

            $mail->setFrom(HMS_MAIL_FROM, HMS_MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $altBody !== '' ? $altBody : strip_tags($htmlBody);

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('hms_send_otp_email')) {
    function hms_send_otp_email($toEmail, $otp, $userType)
    {
        $subject = 'Your Global Hospital Password Reset OTP';
        $userTypeLabel = ucfirst($userType);
        $htmlBody = '<p>Hello,</p>'
            . '<p>We received a password reset request for your <strong>' . htmlspecialchars($userTypeLabel, ENT_QUOTES, 'UTF-8') . '</strong> account.</p>'
            . '<p>Your OTP is: <strong style="font-size:18px;letter-spacing:2px;">' . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . '</strong></p>'
            . '<p>This OTP expires in 15 minutes. If you did not request this, please ignore this email.</p>'
            . '<p>Regards,<br>Global Hospital</p>';
        $altBody = "Your OTP is: " . $otp . ". Expires in 15 minutes.";
        return hms_send_email($toEmail, '', $subject, $htmlBody, $altBody);
    }
}
?>
