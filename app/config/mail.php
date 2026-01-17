<?php
// PHPMailer avec MailHog (capture emails en local)
// Interface: http://localhost:8025

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration serveur SMTP (MailHog)
        $mail->isSMTP();
        $mail->Host = getenv('MAILHOG_HOST') ?: 'mailhog';  // Nom du service Docker
        $mail->Port = 1025;                                  // Port SMTP de MailHog
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

// Email de contact vers le restaurant
function sendContactEmail($nom, $email, $telephone, $titre, $message) {
    $mail = getMailer();
    
    try {
        // Destinataire (email du restaurant)
        $mail->addAddress('contact@viteetgourmand.fr', 'Service Utilisateur Vite & Gourmand');
        
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

/* Envoyer un email de bienvenue après inscription*/
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

/* Envoyer un email de confirmation de commande*/
function sendOrderConfirmationEmail($email, $prenom, $numeroCommande, $detailsCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Confirmation de votre commande #$numeroCommande - Vite & Gourmand";
        
        $lignesMenus = $detailsCommande['lignesMenus'] ?? [];
        $datePrestation = $detailsCommande['date_prestation'] ?? 'À définir';
        $prixTotal = $detailsCommande['prix_total'] ?? 0;
        $fraisLivraison = $detailsCommande['frais_livraison'] ?? 0;
        
        $dateFormatee = date('d/m/Y', strtotime($datePrestation));
        
        // Construire le HTML des lignes de menus
        $htmlMenus = '';
        $totalMenus = 0;
        foreach ($lignesMenus as $ligne) {
            $menuNom = $ligne['menu_nom'] ?? 'Menu';
            $nbPersonnes = $ligne['nombre_personne'] ?? 0;
            $prixUnitaire = $ligne['prix_par_personne'] ?? 0;
            $totalLigne = $ligne['total_ligne'] ?? 0;
            $totalMenus += $totalLigne;
            
            $htmlMenus .= "
                <div class='detail-row'>
                    <span class='detail-label'>Menu :</span>
                    <span>$menuNom</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Nombre de personnes :</span>
                    <span>$nbPersonnes personne(s)</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Prix unitaire :</span>
                    <span>" . number_format($prixUnitaire, 2, ',', ' ') . " €</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Sous-total :</span>
                    <span>" . number_format($totalLigne, 2, ',', ' ') . " €</span>
                </div>
            ";
        }
        
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
                        $htmlMenus
                        <div class='detail-row'>
                            <span class='detail-label'>Date de prestation :</span>
                            <span>$dateFormatee</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Frais de livraison :</span>
                            <span>" . number_format($fraisLivraison, 2, ',', ' ') . " €</span>
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
        
        // Construire texte alternatif avec lignesMenus
        $altBodyMenus = '';
        foreach ($lignesMenus as $ligne) {
            $altBodyMenus .= "Menu : " . ($ligne['menu_nom'] ?? 'Menu') . "\n";
            $altBodyMenus .= "Nombre de personnes : " . ($ligne['nombre_personne'] ?? 0) . "\n";
            $altBodyMenus .= "Prix ligne : " . number_format($ligne['total_ligne'] ?? 0, 2, ',', ' ') . " €\n\n";
        }
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Nous avons bien reçu votre commande #$numeroCommande !\n\n"
                       . $altBodyMenus
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

/*Envoyer un email de modification de commande*/
function sendOrderUpdateEmail($email, $prenom, $numeroCommande, $detailsCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Modification de votre commande #$numeroCommande - Vite & Gourmand";
        
        $lignesMenus = $detailsCommande['lignesMenus'] ?? [];
        $datePrestation = $detailsCommande['date_prestation'] ?? 'À définir';
        
        $dateFormatee = date('d/m/Y', strtotime($datePrestation));
        
        // Construire le HTML des lignes de menus
        $htmlMenus = '';
        foreach ($lignesMenus as $ligne) {
            $menuNom = $ligne['menu_nom'] ?? 'Menu';
            $nbPersonnes = $ligne['nombre_personne'] ?? 0;
            $prixUnitaire = $ligne['prix_par_personne'] ?? 0;
            $totalLigne = $ligne['total_ligne'] ?? 0;
            
            $htmlMenus .= "
                <div class='detail-row'>
                    <span class='detail-label'>Menu :</span>
                    <span>$menuNom</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Nombre de personnes :</span>
                    <span>$nbPersonnes personne(s)</span>
                </div>
            ";
        }
        
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
                        $htmlMenus
                        <div class='detail-row'>
                            <span class='detail-label'>Date de prestation :</span>
                            <span>$dateFormatee</span>
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
        $altBodyMenus = '';
        foreach ($lignesMenus as $ligne) {
            $altBodyMenus .= "Menu : " . ($ligne['menu_nom'] ?? 'Menu') . "\n";
            $altBodyMenus .= "Nombre de personnes : " . ($ligne['nombre_personne'] ?? 0) . "\n";
            $altBodyMenus .= "Prix ligne : " . number_format($ligne['total_ligne'] ?? 0, 2, ',', ' ') . " €\n\n";
        }
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Votre commande #$numeroCommande a été modifiée avec succès !\n\n"
                       . "DÉTAILS MIS À JOUR :\n"
                       . $altBodyMenus
                       . "Date de prestation : $dateFormatee\n\n"
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

/* Envoyer un email de réinitialisation de mot de passe*/
function sendPasswordResetEmail($email, $prenom, $resetLink) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Réinitialisation de votre mot de passe - Vite & Gourmand";
        
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
                .alert { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .button { display: inline-block; background: #8B1538; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 0.9em; }
                .security-note { background: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔒 Vite & Gourmand</h1>
                    <p>Réinitialisation de mot de passe</p>
                </div>
                
                <div class='content'>
                    <h2>Bonjour " . htmlspecialchars($prenom) . ",</h2>
                    
                    <p>Vous avez demandé à réinitialiser votre mot de passe sur Vite & Gourmand.</p>
                    
                    <div class='alert'>
                        ⚠️ <strong>Attention :</strong> Ce lien est valable pendant <strong>1 heure</strong> uniquement.
                    </div>
                    
                    <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                    
                    <div style='text-align: center;'>
                        <a href='$resetLink' class='button'>Réinitialiser mon mot de passe</a>
                    </div>
                    
                    <p style='font-size: 0.9em; color: #666; margin-top: 20px;'>
                        Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                        <a href='$resetLink' style='color: #8B1538; word-break: break-all;'>$resetLink</a>
                    </p>
                    
                    <div class='security-note'>
                        <strong>🔐 Note de sécurité</strong><br>
                        Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. 
                        Votre mot de passe actuel reste inchangé et en sécurité.
                    </div>
                    
                    <p>Cordialement,<br>
                    <strong>L'équipe Vite & Gourmand</strong></p>
                </div>
                
                <div class='footer'>
                    <p>© " . date('Y') . " Vite & Gourmand - Service Traiteur à Bordeaux</p>
                    <p style='font-size: 0.8em; margin-top: 10px;'>
                        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Vous avez demandé à réinitialiser votre mot de passe sur Vite & Gourmand.\n\n"
                       . "IMPORTANT : Ce lien est valable pendant 1 heure uniquement.\n\n"
                       . "Cliquez sur ce lien pour créer un nouveau mot de passe :\n"
                       . "$resetLink\n\n"
                       . "NOTE DE SÉCURITÉ :\n"
                       . "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n"
                       . "Votre mot de passe actuel reste inchangé et en sécurité.\n\n"
                       . "Cordialement,\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email réinitialisation mot de passe: " . $mail->ErrorInfo);
        return false;
    }
}

/*Envoyer un email de confirmation de commande acceptée*/
function sendOrderAcceptedEmail($email, $prenom, $numeroCommande, $datePrestation) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "✅ Votre commande $numeroCommande est confirmée !";
        
        $datePrestationFormatted = date('d/m/Y', strtotime($datePrestation));
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                    .header { background-color: #28a745; color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; background-color: #f9f9f9; }
                    .info-box { background-color: white; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; }
                    .button { display: inline-block; padding: 12px 30px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                    .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 0.9em; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>✅ Commande Confirmée</h1>
                    </div>
                    <div class='content'>
                        <p>Bonjour <strong>" . htmlspecialchars($prenom) . "</strong>,</p>
                        
                        <p>Nous avons le plaisir de vous confirmer que votre commande a été <strong>acceptée</strong> par notre équipe !</p>
                        
                        <div class='info-box'>
                            <p style='margin: 0;'><strong>📋 Numéro de commande :</strong> " . htmlspecialchars($numeroCommande) . "</p>
                            <p style='margin: 10px 0 0 0;'><strong>📅 Date de prestation :</strong> " . htmlspecialchars($datePrestationFormatted) . "</p>
                        </div>
                        
                        <p><strong>Prochaines étapes :</strong></p>
                        <ul>
                            <li>Notre équipe prépare votre commande avec soin</li>
                            <li>Vous serez contacté si nous avons besoin de précisions</li>
                            <li>Nous vous recontacterons avant la prestation pour confirmation finale</li>
                        </ul>
                        
                        <p>Vous pouvez suivre l'état de votre commande en consultant votre espace utilisateur.</p>
                        
                        <div style='text-align: center;'>
                            <a href='http://localhost:8080/mes-commandes' class='button'>Voir mes commandes</a>
                        </div>
                        
                        <p style='margin-top: 30px;'>À très bientôt,<br>
                        <strong>L'équipe Vite & Gourmand</strong></p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Vite & Gourmand - Service Traiteur à Bordeaux</p>
                        <p style='font-size: 0.8em; margin-top: 10px;'>Une question ? Contactez-nous : contact@viteetgourmand.fr</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Votre commande $numeroCommande a été acceptée !\n\n"
                       . "Date de prestation : $datePrestationFormatted\n\n"
                       . "Notre équipe prépare votre commande avec soin.\n\n"
                       . "Cordialement,\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email commande acceptée: " . $mail->ErrorInfo);
        return false;
    }
}
/*Envoyer un email de commande terminée avec invitation à laisser un avis*/
function sendOrderCompletedEmail($email, $prenom, $numeroCommande, $menuTitre) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "⭐ Votre avis compte ! - Commande $numeroCommande";
        
        $avisLink = "http://localhost:8080/donner-avis?commande=" . urlencode($numeroCommande);
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                    .header { background-color: #ffc107; color: #333; padding: 30px; text-align: center; }
                    .content { padding: 30px; background-color: #f9f9f9; }
                    .menu-box { background-color: white; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0; }
                    .button { display: inline-block; padding: 12px 30px; background-color: #ffc107; color: #333; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
                    .stars { font-size: 24px; color: #ffc107; }
                    .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 0.9em; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>⭐ Votre avis nous intéresse !</h1>
                    </div>
                    <div class='content'>
                        <p>Bonjour <strong>" . htmlspecialchars($prenom) . "</strong>,</p>
                        
                        <p>Votre commande <strong>" . htmlspecialchars($numeroCommande) . "</strong> est maintenant terminée !</p>
                        
                        <div class='menu-box'>
                            <p style='margin: 0;'><strong>🍽️ Menu :</strong> " . htmlspecialchars($menuTitre) . "</p>
                        </div>
                        
                        <p>Nous espérons que notre prestation vous a donné entière satisfaction.</p>
                        
                        <p><strong>Votre avis est précieux pour nous !</strong><br>
                        Il nous aide à améliorer constamment notre service et guide d'autres utilisateurs dans leur choix.</p>
                        
                        <p style='text-align: center;'>
                            <span class='stars'>⭐⭐⭐⭐⭐</span>
                        </p>
                        
                        <p>Prenez 2 minutes pour partager votre expérience :</p>
                        
                        <div style='text-align: center;'>
                            <a href='$avisLink' class='button'>Donner mon avis</a>
                        </div>
                        
                        <p style='margin-top: 30px;'>Merci pour votre confiance,<br>
                        <strong>L'équipe Vite & Gourmand</strong></p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Vite & Gourmand - Service Traiteur à Bordeaux</p>
                        <p style='font-size: 0.8em; margin-top: 10px;'>Cet avis restera anonyme si vous le souhaitez</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Votre commande $numeroCommande est terminée !\n\n"
                       . "Menu : $menuTitre\n\n"
                       . "Votre avis nous intéresse ! Partagez votre expérience :\n"
                       . "$avisLink\n\n"
                       . "Merci pour votre confiance,\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email commande terminée: " . $mail->ErrorInfo);
        return false;
    }
}


/*Envoyer un email de rappel pour restitution du matériel - Envoyé 10 jours après la prestation si le matériel n'a pas été restitué*/
function sendMaterialReturnReminderEmail($email, $prenom, $numeroCommande, $datePrestation) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "⚠️ URGENT - Restitution matériel - Commande $numeroCommande";
        
        $datePrestationFormatted = date('d/m/Y', strtotime($datePrestation));
        $dateEcheance = date('d/m/Y', strtotime($datePrestation . ' +10 days'));
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                    .header { background-color: #dc3545; color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; background-color: #f9f9f9; }
                    .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; }
                    .penalty-box { background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 20px; margin: 20px 0; }
                    .button { display: inline-block; padding: 12px 30px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
                    .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 0.9em; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>⚠️ RAPPEL URGENT</h1>
                        <h2>Restitution du matériel</h2>
                    </div>
                    <div class='content'>
                        <p>Bonjour <strong>" . htmlspecialchars($prenom) . "</strong>,</p>
                        
                        <p>Nous constatons que le matériel prêté pour votre prestation n'a pas encore été restitué.</p>
                        
                        <div class='warning-box'>
                            <p style='margin: 0;'><strong>📋 Commande :</strong> " . htmlspecialchars($numeroCommande) . "</p>
                            <p style='margin: 10px 0 0 0;'><strong>📅 Date de prestation :</strong> " . htmlspecialchars($datePrestationFormatted) . "</p>
                            <p style='margin: 10px 0 0 0;'><strong>⏰ Date limite :</strong> <span style='color: #dc3545; font-weight: bold;'>" . htmlspecialchars($dateEcheance) . "</span></p>
                        </div>
                        
                        <p><strong>Action requise IMMÉDIATEMENT :</strong></p>
                        <ul>
                            <li>Contactez-nous au plus vite pour organiser la restitution</li>
                            <li>Ramenez le matériel à nos locaux</li>
                            <li>Ou convenez d'un rendez-vous pour que nous venions le récupérer</li>
                        </ul>
                        
                        <div class='penalty-box'>
                            <p style='margin: 0; font-size: 1.1em;'><strong>⚠️ ATTENTION - Pénalité de retard</strong></p>
                            <p style='margin: 10px 0 0 0;'>Conformément à nos CGV, une pénalité sera appliquée en cas de non-restitution sous 10 jours.</p>
                        </div>
                        
                        <p><strong>Contactez-nous rapidement :</strong></p>
                        <ul>
                            <li>📧 Email : contact@viteetgourmand.fr</li>
                            <li>📞 Téléphone : 05 56 XX XX XX</li>
                        </ul>
                        
                        <div style='text-align: center;'>
                            <a href='http://localhost:8080/contact' class='button'>Nous contacter</a>
                        </div>
                        
                        <p style='margin-top: 30px;'>Nous restons à votre disposition,<br>
                        <strong>L'équipe Vite & Gourmand</strong></p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Vite & Gourmand - Service Traiteur à Bordeaux</p>
                        <p style='font-size: 0.8em; margin-top: 10px;'>Email de rappel automatique - Merci de votre compréhension</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "RAPPEL URGENT - Restitution du matériel\n\n"
                       . "Bonjour $prenom,\n\n"
                       . "Le matériel prêté pour votre prestation n'a pas encore été restitué.\n\n"
                       . "Commande : $numeroCommande\n"
                       . "Date de prestation : $datePrestationFormatted\n"
                       . "Date limite : $dateEcheance\n\n"
                       . "ATTENTION : En cas de non-restitution sous 10 jours, une pénalité sera appliquée.\n\n"
                       . "Contactez-nous rapidement :\n"
                       . "- Email : contact@viteetgourmand.fr\n"
                       . "- Téléphone : 05 56 XX XX XX\n\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email rappel matériel: " . $mail->ErrorInfo);
        return false;
    }
}

 /* Email de bienvenue pour un nouvel employé -Envoie un lien de réinitialisation pour définir le mot de passe*/
function sendEmployeeWelcomeEmail($email, $prenom, $token) {
    $mail = getMailer();
    
    try {
        // Destinataire
        $mail->addAddress($email, $prenom);
        
        // Lien de réinitialisation
        $resetLink = "http://localhost:8080/reset-password?token=" . $token;
        
        // Sujet
        $mail->Subject = '🎉 Bienvenue chez Vite & Gourmand !';
        
        // Corps du message HTML
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background: linear-gradient(135deg, #6B1B3D 0%, #B8985F 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                    border-radius: 10px 10px 0 0;
                }
                .content {
                    background: #f9f9f9;
                    padding: 30px;
                    border-left: 1px solid #ddd;
                    border-right: 1px solid #ddd;
                }
                .cta-button {
                    display: inline-block;
                    background: #6B1B3D;
                    color: white;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    margin: 20px 0;
                }
                .cta-button:hover {
                    background: #8A2550;
                }
                .footer {
                    background: #333;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    font-size: 12px;
                    border-radius: 0 0 10px 10px;
                }
                .info-box {
                    background: #fff;
                    border-left: 4px solid #B8985F;
                    padding: 15px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🎉 Bienvenue dans l'équipe !</h1>
            </div>
            
            <div class='content'>
                <p>Bonjour <strong>{$prenom}</strong>,</p>
                
                <p>Bienvenue chez <strong>Vite & Gourmand</strong> ! 🍽️</p>
                
                <p>Un compte employé vient d'être créé pour vous. Pour commencer à utiliser votre accès, vous devez d'abord <strong>définir votre mot de passe</strong>.</p>
                
                <div class='info-box'>
                    <p><strong>📧 Votre email de connexion :</strong> {$email}</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$resetLink}' class='cta-button'>
                        🔒 Définir mon mot de passe
                    </a>
                </p>
                
                <p><strong>⚠️ Important :</strong></p>
                <ul>
                    <li>Ce lien est valable pendant <strong>24 heures</strong></li>
                    <li>Après avoir défini votre mot de passe, vous pourrez accéder à votre espace employé</li>
                    <li>Vous aurez accès aux fonctionnalités de gestion des commandes et des avis</li>
                </ul>
                
                <div class='info-box'>
                    <p><strong>🔗 Lien de réinitialisation (si le bouton ne fonctionne pas) :</strong></p>
                    <p style='word-break: break-all; font-size: 12px;'>{$resetLink}</p>
                </div>
                
                <p>Si vous n'avez pas demandé ce compte ou si vous avez des questions, contactez votre administrateur.</p>
                
                <p>À bientôt ! 👋</p>
                <p><strong>L'équipe Vite & Gourmand</strong></p>
            </div>
            
            <div class='footer'>
                <p>© 2025 Vite & Gourmand - Traiteur gastronomique</p>
                <p>📍 Bordeaux, France | 📧 contact@viteetgourmand.fr</p>
            </div>
        </body>
        </html>
        ";
        
        // Version texte alternatif
        $mail->AltBody = "Bienvenue chez Vite & Gourmand !\n\n"
                       . "Bonjour {$prenom},\n\n"
                       . "Un compte employé vient d'être créé pour vous.\n"
                       . "Pour définir votre mot de passe, cliquez sur le lien suivant :\n\n"
                       . "{$resetLink}\n\n"
                       . "Ce lien est valable pendant 24 heures.\n\n"
                       . "Votre email de connexion : {$email}\n\n"
                       . "L'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email bienvenue employé: " . $mail->ErrorInfo);
        return false;
    }
}

/*Notification de création de compte employé - le mot de passe n'est PAS communiqué par email - l'employé doit contacter l'administrateur pour l'obtenir */

function sendEmployeeAccountCreatedEmail($email, $prenom, $nom) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, "$prenom $nom");
        $mail->Subject = "🎉 Bienvenue chez Vite & Gourmand - Votre compte employé";
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center; }
                    .content { padding: 40px; background-color: #f9f9f9; }
                    .info-box { background-color: #e3f2fd; border-left: 4px solid #2196F3; padding: 20px; margin: 20px 0; }
                    .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; }
                    .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 0.9em; }
                    .button { display: inline-block; padding: 12px 30px; background-color: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🎉 Bienvenue dans l'équipe !</h1>
                        <p style='font-size: 1.2em; margin-top: 10px;'>Vite & Gourmand</p>
                    </div>
                    <div class='content'>
                        <p>Bonjour <strong>" . htmlspecialchars($prenom) . " " . htmlspecialchars($nom) . "</strong>,</p>
                        
                        <p>Un compte <strong>Employé</strong> a été créé pour vous sur la plateforme Vite & Gourmand.</p>
                        
                        <div class='info-box'>
                            <p style='margin: 0;'><strong>📧 Votre identifiant :</strong></p>
                            <p style='margin: 10px 0 0 0; font-size: 1.1em;'>" . htmlspecialchars($email) . "</p>
                        </div>
                        
                        <div class='warning-box'>
                            <p style='margin: 0;'><strong>🔐 Mot de passe</strong></p>
                            <p style='margin: 10px 0 0 0;'>
                                Pour des raisons de sécurité, votre mot de passe n'est <strong>PAS</strong> communiqué par email.
                                <br><br>
                                ➡️ <strong>Veuillez contacter l'administrateur</strong> (José) pour obtenir votre mot de passe.
                            </p>
                        </div>
                        
                        <p><strong>Vos accès employé vous permettront de :</strong></p>
                        <ul>
                            <li>✅ Gérer les commandes clients</li>
                            <li>✅ Modifier les menus et plats</li>
                            <li>✅ Modérer les avis clients</li>
                            <li>✅ Mettre à jour les horaires</li>
                        </ul>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='http://localhost:8080/login' class='button'>Se connecter à la plateforme</a>
                        </div>
                        
                        <p style='margin-top: 30px;'>À très bientôt,<br>
                        <strong>L'équipe Vite & Gourmand</strong></p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Vite & Gourmand - Service Traiteur à Bordeaux</p>
                        <p style='font-size: 0.8em; margin-top: 10px;'>Cet email a été envoyé automatiquement suite à la création de votre compte.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Bienvenue chez Vite & Gourmand\n\n"
                       . "Bonjour $prenom $nom,\n\n"
                       . "Un compte Employé a été créé pour vous.\n\n"
                       . "Votre identifiant : $email\n\n"
                       . "IMPORTANT : Pour des raisons de sécurité, votre mot de passe n'est PAS communiqué par email.\n"
                       . "Veuillez contacter l'administrateur (José) pour obtenir votre mot de passe.\n\n"
                       . "Connexion : http://localhost:8080/login\n\n"
                       . "À très bientôt,\n"
                       . "L'équipe Vite & Gourmand";
        
        $sent = $mail->send();
        
        if ($sent) {
            error_log("✅ Email création compte employé envoyé à : $email");
        } else {
            error_log("❌ Échec envoi email création compte employé à : $email");
        }
        
        return $sent;
    } catch (\Exception $e) {
        error_log("❌ Erreur envoi email création compte employé : " . $e->getMessage());
        return false;
    }
}

// Email d'annulation de commande pour l'utilisateur
function sendCancellationEmailToUser($email, $prenom, $numeroCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "Annulation de votre commande #$numeroCommande";
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #8B0000; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Vite & Gourmand</h1>
                    </div>
                    <div class='content'>
                        <h2>Annulation de commande</h2>
                        <p>Bonjour " . htmlspecialchars($prenom) . ",</p>
                        <p>Votre commande <strong>#" . htmlspecialchars($numeroCommande) . "</strong> a bien été annulée.</p>
                        <p>Si vous avez des questions ou souhaitez passer une nouvelle commande, n'hésitez pas à nous contacter.</p>
                        <p>Cordialement,<br>L'équipe Vite & Gourmand</p>
                    </div>
                    <div class='footer'>
                        <p>Vite & Gourmand - Traiteur à Bordeaux<br>
                        📧 contact@viteetgourmand.fr | 📞 05 56 00 00 00</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Bonjour $prenom,\n\n"
                       . "Votre commande #$numeroCommande a bien été annulée.\n\n"
                       . "Si vous avez des questions ou souhaitez passer une nouvelle commande, n'hésitez pas à nous contacter.\n\n"
                       . "Cordialement,\nL'équipe Vite & Gourmand";
        
        $sent = $mail->send();
        
        if ($sent) {
            error_log("✅ Email annulation commande envoyé à l'utilisateur : $email");
        }
        
        return $sent;
    } catch (\Exception $e) {
        error_log("❌ Erreur envoi email annulation utilisateur : " . $e->getMessage());
        return false;
    }
}

// Email d'annulation de commande pour le restaurant
function sendCancellationEmailToRestaurant($numeroCommande, $clientNom, $clientEmail) {
    $mail = getMailer();
    
    try {
        $mail->addAddress('contact@viteetgourmand.fr', 'Vite & Gourmand');
        $mail->Subject = "Annulation commande #$numeroCommande par le client";
        
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #8B0000; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .alert { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 10px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Notification Restaurant</h1>
                    </div>
                    <div class='content'>
                        <div class='alert'>
                            <strong>⚠️ Annulation de commande</strong>
                        </div>
                        <p>La commande <strong>#" . htmlspecialchars($numeroCommande) . "</strong> a été annulée par le client.</p>
                        <p><strong>Informations client :</strong><br>
                        Nom : " . htmlspecialchars($clientNom) . "<br>
                        Email : " . htmlspecialchars($clientEmail) . "</p>
                        <p>Veuillez mettre à jour votre planning de préparation.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Annulation de commande\n\n"
                       . "La commande #$numeroCommande a été annulée par le client.\n\n"
                       . "Client : $clientNom ($clientEmail)\n\n"
                       . "Veuillez mettre à jour votre planning de préparation.";
        
        $sent = $mail->send();
        
        if ($sent) {
            error_log("✅ Email annulation commande envoyé au restaurant");
        }
        
        return $sent;
    } catch (\Exception $e) {
        error_log("❌ Erreur envoi email annulation restaurant : " . $e->getMessage());
        return false;
    }
}

