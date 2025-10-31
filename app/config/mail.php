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
 * @param string $titre Titre/sujet du message
 * @param string $message Message
 * @return bool True si envoyé, False sinon
 */
function sendContactEmail($nom, $email, $telephone, $titre, $message) {
    $mail = getMailer();
    
    try {
        // Destinataire (email du restaurant)
        $mail->addAddress('contact@viteetgourmand.fr', 'Service Client Vite & Gourmand');
        
        // Répondre à l'expéditeur
        $mail->addReplyTo($email, $nom);
        
        // Sujet
        $mail->Subject = htmlspecialchars($titre) . " - Contact de " . htmlspecialchars($nom);
        
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
                            <span class='label'>Titre :</span> " . htmlspecialchars($titre) . "
                        </div>
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
                       . "Titre: $titre\n"
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

/**
 * Envoyer un email de confirmation de commande
 * @param string $email Email du client
 * @param string $prenom Prénom du client
 * @param string $numeroCommande Numéro de la commande
 * @param array $detailsCommande Détails de la commande (menu, prix, personnes, date)
 * @return bool True si envoyé, False sinon
 */
function sendOrderConfirmationEmail($email, $prenom, $numeroCommande, $detailsCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Confirmation de votre commande #$numeroCommande - Vite & Gourmand";
        
        $menuNom = $detailsCommande['menu_nom'] ?? 'Menu';
        $nbPersonnes = $detailsCommande['nombre_personne'] ?? 0;
        $datePrestation = $detailsCommande['date_prestation'] ?? 'À définir';
        $prixUnitaire = $detailsCommande['prix_par_personne'] ?? 0;
        $prixTotal = $prixUnitaire * $nbPersonnes;
        
        $dateFormatee = date('d/m/Y', strtotime($datePrestation));
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #8B1538 0%, #C5A572 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
                .order-details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; border: 2px solid #8B1538; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; color: #8B1538; }
                .total { font-size: 1.2em; font-weight: bold; color: #8B1538; margin-top: 15px; padding-top: 15px; border-top: 2px solid #8B1538; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 0.9em; }
                .button { display: inline-block; background: #8B1538; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Vite & Gourmand</h1>
                    <p>Confirmation de commande</p>
                </div>
                
                <div class='content'>
                    <h2>Bonjour $prenom,</h2>
                    <p>Nous avons bien reçu votre commande et vous en remercions !</p>
                    
                    <div class='order-details'>
                        <h3>📋 Détails de votre commande</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Numéro de commande :</span>
                            <span>#$numeroCommande</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Menu :</span>
                            <span>$menuNom</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Nombre de personnes :</span>
                            <span>$nbPersonnes personne(s)</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Date de prestation :</span>
                            <span>$dateFormatee</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Prix unitaire :</span>
                            <span>" . number_format($prixUnitaire, 2, ',', ' ') . " €</span>
                        </div>
                        <div class='total'>
                            <div class='detail-row'>
                                <span class='detail-label'>TOTAL :</span>
                                <span>" . number_format($prixTotal, 2, ',', ' ') . " €</span>
                            </div>
                        </div>
                    </div>
                    
                    <p><strong>Statut :</strong> ⏳ En attente de validation</p>
                    <p>Nous traiterons votre commande dans les plus brefs délais et vous tiendrons informé(e) de son évolution.</p>
                    
                    <center>
                        <a href='http://localhost:8080/mes-commandes' class='button'>Voir mes commandes</a>
                    </center>
                </div>
                
                <div class='footer'>
                    <p><strong>Vite & Gourmand</strong></p>
                    <p>📧 contact@viteetgourmand.fr | 📞 05 XX XX XX XX</p>
                    <p>Horaires : Lun-Dim 10h-22h</p>
                    <p style='font-size: 0.8em; margin-top: 15px;'>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Nous avons bien reçu votre commande #$numeroCommande !\n\n"
                       . "Menu : $menuNom\n"
                       . "Nombre de personnes : $nbPersonnes\n"
                       . "Date de prestation : $dateFormatee\n"
                       . "Prix total : " . number_format($prixTotal, 2, ',', ' ') . " €\n\n"
                       . "Statut : En attente de validation\n\n"
                       . "À bientôt chez Vite & Gourmand !\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email confirmation commande: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Envoyer un email de modification de commande
 * @param string $email Email du client
 * @param string $prenom Prénom du client
 * @param string $numeroCommande Numéro de la commande
 * @param array $detailsCommande Détails de la commande (menu, prix, personnes, date)
 * @return bool True si envoyé, False sinon
 */
function sendOrderUpdateEmail($email, $prenom, $numeroCommande, $detailsCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Modification de votre commande #$numeroCommande - Vite & Gourmand";
        
        $menuNom = $detailsCommande['menu_nom'] ?? 'Menu';
        $nbPersonnes = $detailsCommande['nombre_personne'] ?? 0;
        $datePrestation = $detailsCommande['date_prestation'] ?? 'À définir';
        $prixUnitaire = $detailsCommande['prix_par_personne'] ?? 0;
        $prixTotal = $prixUnitaire * $nbPersonnes;
        
        $dateFormatee = date('d/m/Y', strtotime($datePrestation));
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #8B1538 0%, #C5A572 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
                .order-details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; border: 2px solid #C5A572; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; color: #8B1538; }
                .total { font-size: 1.2em; font-weight: bold; color: #8B1538; margin-top: 15px; padding-top: 15px; border-top: 2px solid #8B1538; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 0.9em; }
                .button { display: inline-block; background: #8B1538; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .alert { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; color: #856404; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Vite & Gourmand</h1>
                    <p>✏️ Modification de commande</p>
                </div>
                
                <div class='content'>
                    <h2>Bonjour $prenom,</h2>
                    <p>Votre commande a été modifiée avec succès !</p>
                    
                    <div class='alert'>
                        <strong>ℹ️ Information</strong><br>
                        Les modifications apportées à votre commande ont bien été enregistrées.
                    </div>
                    
                    <div class='order-details'>
                        <h3>📋 Détails mis à jour</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Numéro de commande :</span>
                            <span>#$numeroCommande</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Menu :</span>
                            <span>$menuNom</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Nombre de personnes :</span>
                            <span>$nbPersonnes personne(s)</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Date de prestation :</span>
                            <span>$dateFormatee</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Prix unitaire :</span>
                            <span>" . number_format($prixUnitaire, 2, ',', ' ') . " €</span>
                        </div>
                        <div class='total'>
                            <div class='detail-row'>
                                <span class='detail-label'>TOTAL :</span>
                                <span>" . number_format($prixTotal, 2, ',', ' ') . " €</span>
                            </div>
                        </div>
                    </div>
                    
                    <p>Si vous n'êtes pas à l'origine de cette modification, veuillez nous contacter immédiatement.</p>
                    
                    <center>
                        <a href='http://localhost:8080/mes-commandes' class='button'>Voir mes commandes</a>
                    </center>
                </div>
                
                <div class='footer'>
                    <p><strong>Vite & Gourmand</strong></p>
                    <p>📧 contact@viteetgourmand.fr | 📞 05 XX XX XX XX</p>
                    <p>Horaires : Lun-Dim 10h-22h</p>
                    <p style='font-size: 0.8em; margin-top: 15px;'>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Votre commande #$numeroCommande a été modifiée avec succès !\n\n"
                       . "DÉTAILS MIS À JOUR :\n"
                       . "Menu : $menuNom\n"
                       . "Nombre de personnes : $nbPersonnes\n"
                       . "Date de prestation : $dateFormatee\n"
                       . "Prix total : " . number_format($prixTotal, 2, ',', ' ') . " €\n\n"
                       . "Si vous n'êtes pas à l'origine de cette modification, veuillez nous contacter.\n\n"
                       . "À bientôt chez Vite & Gourmand !\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email confirmation commande: " . $mail->ErrorInfo);
        return false;
    }
}
