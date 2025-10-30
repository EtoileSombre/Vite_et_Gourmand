<?php
/**
 * Configuration PHPMailer avec MailHog
 * MailHog capture tous les emails en local pour les tests
 * Interface web: http://localhost:8025
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Créer une instance PHPMailer configurée pour MailHog
 * @return PHPMailer
 */
function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration serveur SMTP (MailHog)
        $mail->isSMTP();
        $mail->Host = 'vitegourmand-mailhog';  // Nom du service Docker
        $mail->Port = 1025;                     // Port SMTP de MailHog
        $mail->SMTPAuth = false;                // MailHog ne nécessite pas d'authentification
        $mail->SMTPAutoTLS = false;             // Désactiver TLS pour MailHog
        
        // Configuration par défaut
        $mail->setFrom('noreply@viteetgourmand.fr', 'Vite & Gourmand');
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        
    } catch (Exception $e) {
        error_log("Erreur configuration PHPMailer: " . $e->getMessage());
    }
    
    return $mail;
}

/**
 * Envoyer un email de contact au restaurant
 * @param string $nom Nom de l'expéditeur
 * @param string $email Email de l'expéditeur
 * @param string $telephone Téléphone de l'expéditeur
 * @param string $message Message
 * @return bool True si envoyé, False sinon
 */
function sendContactEmail($nom, $email, $telephone, $message) {
    $mail = getMailer();
    
    try {
        // Destinataire (email du restaurant)
        $mail->addAddress('contact@viteetgourmand.fr', 'Service Client Vite & Gourmand');
        
        // Répondre à l'expéditeur
        $mail->addReplyTo($email, $nom);
        
        // Sujet
        $mail->Subject = "Nouveau message de contact - " . htmlspecialchars($nom);
        
        // Corps du message HTML
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                    .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
                    .info { margin-bottom: 10px; }
                    .label { font-weight: bold; color: #28a745; }
                    .message { background-color: white; padding: 15px; margin-top: 15px; border-left: 4px solid #28a745; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Nouveau message de contact</h1>
                    </div>
                    <div class='content'>
                        <div class='info'>
                            <span class='label'>Nom :</span> " . htmlspecialchars($nom) . "
                        </div>
                        <div class='info'>
                            <span class='label'>Email :</span> <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a>
                        </div>
                        <div class='info'>
                            <span class='label'>Téléphone :</span> " . htmlspecialchars($telephone) . "
                        </div>
                        <div class='message'>
                            <p class='label'>Message :</p>
                            <p>" . nl2br(htmlspecialchars($message)) . "</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Version texte alternatif
        $mail->AltBody = "Nouveau message de contact\n\n"
                       . "Nom: $nom\n"
                       . "Email: $email\n"
                       . "Téléphone: $telephone\n\n"
                       . "Message:\n$message";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email contact: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Envoyer un email de bienvenue après inscription
 * @param string $email Email du destinataire
 * @param string $prenom Prénom du destinataire
 * @return bool True si envoyé, False sinon
 */
function sendWelcomeEmail($email, $prenom) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Bienvenue chez Vite & Gourmand !";
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 30px; text-align: center; }
                    .content { background-color: #f9f9f9; padding: 30px; margin-top: 20px; }
                    .button { display: inline-block; padding: 12px 30px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Bienvenue " . htmlspecialchars($prenom) . " !</h1>
                    </div>
                    <div class='content'>
                        <p>Merci de nous avoir rejoint sur <strong>Vite & Gourmand</strong> !</p>
                        <p>Votre compte a été créé avec succès. Vous pouvez maintenant :</p>
                        <ul>
                            <li>Découvrir nos menus du jour</li>
                            <li>Passer vos commandes en ligne</li>
                            <li>Suivre l'état de vos commandes</li>
                            <li>Laisser des avis sur nos plats</li>
                        </ul>
                        <p style='text-align: center;'>
                            <a href='http://localhost:8080/menus.php' class='button'>Découvrir nos menus</a>
                        </p>
                        <p style='margin-top: 30px; color: #666; font-size: 0.9em;'>
                            À bientôt chez Vite & Gourmand !<br>
                            L'équipe Vite & Gourmand
                        </p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Bienvenue $prenom !\n\n"
                       . "Merci de nous avoir rejoint sur Vite & Gourmand !\n\n"
                       . "Votre compte a été créé avec succès.\n\n"
                       . "À bientôt chez Vite & Gourmand !\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email bienvenue: " . $mail->ErrorInfo);
        return false;
    }
}
