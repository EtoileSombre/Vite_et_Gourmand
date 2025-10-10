<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host = 'mailhog';
  $mail->Port = 1025;
  $mail->SMTPAuth = false;
  $mail->SMTPSecure = false;

  $mail->setFrom('no-reply@vitegourmand.test', 'Vite & Gourmand');
  $mail->addAddress('test@example.com');

  $mail->Subject = 'Test MailHog / PHPMailer';
  $mail->Body    = "Hello depuis PHPMailer ✅\nHeure: " . date('Y-m-d H:i:s');

  $mail->send();
  echo "📧 Mail envoyé — ouvre MailHog: <a href='http://localhost:8025' target='_blank'>http://localhost:8025</a>";
} catch (Exception $e) {
  echo "❌ Erreur d'envoi: " . $mail->ErrorInfo;
}
