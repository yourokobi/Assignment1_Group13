<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'zeti.ozeii@gmail.com';         // 🔁 Replace with your Gmail
    $mail->Password   = 'pfjl dnph pmha mbxl';    // 🔁 Replace with your App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender and recipient
    $mail->setFrom('zeti.ozeii@gmail.com', 'ZETI WIYADA');        // 🔁 Replace as needed
    $mail->addAddress('zeti.ozeii@gmail.com', 'ZETI WIYADA');    // 🔁 Replace with real recipient

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'OTP Code Verification';
    $mail->Body    = 'This is a <b>test email</b> using PHPMailer with Gmail SMTP.';
    $mail->AltBody = 'This is a test email using PHPMailer with Gmail SMTP.';

    // Send the email
    if ($mail->send()) {
        echo '✅ Email sent successfully.';
    } else {
        echo '❌ Mailer Error: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "❌ PHPMailer Exception: {$mail->ErrorInfo}";
}
