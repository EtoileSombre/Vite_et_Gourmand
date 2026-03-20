<?php
// PHPMailer avec MailHog (capture emails en local)
// Interface: http://localhost:8025

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Détection environnement
        $isProduction = getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'prod';
        
        // Configuration serveur SMTP
        $mail->isSMTP();
        
        if ($isProduction) {
            // Production - SMTP Hostinger
            $mail->Host = getenv('MAIL_HOST') ?: 'smtp.hostinger.com';
            $mail->Port = (int)(getenv('MAIL_PORT') ?: 587);
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME');
            $mail->Password = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            // Développement - MailHog
            $mail->Host = getenv('MAIL_HOST') ?: 'mailhog';
            $mail->Port = (int)(getenv('MAIL_PORT') ?: 1025);
            $mail->SMTPAuth = false;
            $mail->SMTPAutoTLS = false;
        }
        
        // Configuration par défaut
        $fromAddress = getenv('MAIL_FROM_ADDRESS') ?: 'noreply@localhost';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Vite & Gourmand';
        $mail->setFrom($fromAddress, $fromName);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        
    } catch (Exception $e) {
        error_log("Erreur configuration PHPMailer: " . $e->getMessage());
    }
    
    return $mail;
}

/**
 * Template de base HTML responsive pour tous les emails
 * Design moderne avec charte graphique bordeaux/or
 */
function getEmailTemplate($title, $content, $footerNote = '') {
    $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';
    $year = date('Y');
    
    return "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$title</title>
        <!--[if mso]>
        <style type='text/css'>
            table {border-collapse: collapse;}
        </style>
        <![endif]-->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
            
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
                line-height: 1.6;
                color: #333333;
                background-color: #f5f5f5;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            
            .email-wrapper {
                width: 100%;
                background-color: #f5f5f5;
                padding: 40px 20px;
            }
            
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                background: #ffffff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }
            
            .email-header {
                background: linear-gradient(135deg, #8B1538 0%, #B8985F 100%);
                padding: 40px 30px;
                text-align: center;
            }
            
            .logo {
                font-size: 32px;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 8px;
                letter-spacing: -0.5px;
            }
            
            .tagline {
                font-size: 14px;
                color: rgba(255, 255, 255, 0.9);
                font-weight: 400;
            }
            
            .email-content {
                padding: 40px 30px;
            }
            
            h1 {
                font-size: 24px;
                font-weight: 700;
                color: #8B1538;
                margin-bottom: 20px;
                line-height: 1.3;
            }
            
            h2 {
                font-size: 20px;
                font-weight: 600;
                color: #8B1538;
                margin: 30px 0 15px 0;
            }
            
            h3 {
                font-size: 16px;
                font-weight: 600;
                color: #333;
                margin: 20px 0 10px 0;
            }
            
            p {
                font-size: 15px;
                line-height: 1.7;
                color: #555;
                margin-bottom: 15px;
            }
            
            .greeting {
                font-size: 17px;
                color: #333;
                font-weight: 600;
                margin-bottom: 20px;
            }
            
            .card {
                background: #fafafa;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
                border-left: 4px solid #8B1538;
            }
            
            .card-success {
                background: #f9f4f6;
                border-left-color: #8B1538;
            }
            
            .card-warning {
                background: #fdf9f3;
                border-left-color: #B8985F;
            }
            
            .card-danger {
                background: #fdf5f7;
                border-left-color: #8B1538;
            }
            
            .card-info {
                background: #fdf9f3;
                border-left-color: #B8985F;
            }
            
            .detail-row {
                display: flex;
                justify-content: space-between;
                padding: 12px 0;
                border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            }
            
            .detail-row:last-child {
                border-bottom: none;
            }
            
            .detail-label {
                font-weight: 600;
                color: #8B1538;
            }
            
            .detail-value {
                color: #555;
                text-align: right;
            }
            
            .btn {
                display: inline-block;
                padding: 14px 32px;
                font-size: 15px;
                font-weight: 600;
                text-decoration: none;
                border-radius: 8px;
                text-align: center;
                transition: all 0.3s ease;
                margin: 20px 0;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #8B1538 0%, #a61d45 100%);
                color: #ffffff !important;
                box-shadow: 0 4px 12px rgba(139, 21, 56, 0.3);
            }
            
            .btn-secondary {
                background: #B8985F;
                color: #ffffff !important;
            }
            
            .divider {
                height: 1px;
                background: linear-gradient(to right, transparent, rgba(139, 21, 56, 0.2), transparent);
                margin: 30px 0;
            }
            
            .email-footer {
                background: #2d2d2d;
                color: #ffffff;
                padding: 30px;
                text-align: center;
            }
            
            .email-footer p {
                color: rgba(255, 255, 255, 0.8);
                font-size: 13px;
                margin: 8px 0;
            }
            
            .email-footer a {
                color: #B8985F;
                text-decoration: none;
            }
            
            .social-links {
                margin: 20px 0 10px 0;
            }
            
            .social-links a {
                display: inline-block;
                margin: 0 8px;
                font-size: 20px;
            }
            
            .text-center {
                text-align: center;
            }
            
            .text-muted {
                color: #888 !important;
                font-size: 13px;
            }
            
            .badge {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
            }
            
            .badge-success {
                background: #8B1538;
                color: #ffffff;
            }
            
            .badge-warning {
                background: #B8985F;
                color: #ffffff;
            }
            
            .badge-danger {
                background: #8B1538;
                color: #ffffff;
            }
            
            ul {
                padding-left: 20px;
                margin: 15px 0;
            }
            
            li {
                margin: 8px 0;
                color: #555;
            }
            
            @media only screen and (max-width: 600px) {
                .email-wrapper { padding: 20px 10px; }
                .email-content { padding: 30px 20px; }
                .email-header { padding: 30px 20px; }
                .logo { font-size: 26px; }
                h1 { font-size: 20px; }
                .btn { display: block; width: 100%; }
                .detail-row { flex-direction: column; }
                .detail-value { text-align: left; margin-top: 4px; }
            }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='email-container'>
                <div class='email-header'>
                    <div class='logo'> Vite & Gourmand</div>
                    <div class='tagline'>Traiteur à Bordeaux</div>
                </div>
                
                <div class='email-content'>
                    $content
                </div>
                
                <div class='email-footer'>
                    <p style='font-size: 16px; font-weight: 600; margin-bottom: 15px;'>Vite & Gourmand</p>
                    <p>42 Rue des Gourmets, 33000 Bordeaux</p>
                    <p>
                        <a href='mailto:contact@viteetgourmand.com'>contact@viteetgourmand.com</a> · 
                        📞 05 56 00 00 00
                    </p>
                    <p>Ouvert du lundi au dimanche, 10h - 22h</p>
                    
                    <div class='divider' style='background: rgba(255, 255, 255, 0.2);'></div>
                    
                    <p class='text-muted' style='font-size: 11px;'>
                        © $year Vite & Gourmand. Tous droits réservés.
                        " . ($footerNote ? "<br>$footerNote" : "") . "
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Email de confirmation de commande
 */
function sendOrderConfirmationEmail($email, $prenom, $numeroCommande, $detailsCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "✅ Commande confirmée # $numeroCommande - Vite & Gourmand";
        
        $lignesMenus = $detailsCommande['lignesMenus'] ?? [];
        $datePrestation = $detailsCommande['date_prestation'] ?? 'À définir';
        $prixTotal = $detailsCommande['prix_total'] ?? 0;
        $fraisLivraison = $detailsCommande['frais_livraison'] ?? 0;
        
        $dateFormatee = date('d/m/Y', strtotime($datePrestation));
        
        // Construction des lignes de menus
        $htmlMenus = '';
        foreach ($lignesMenus as $ligne) {
            $menuNom = htmlspecialchars($ligne['menu_nom'] ?? 'Menu');
            $nbPersonnes = $ligne['nombre_personne'] ?? 0;
            $prixUnitaire = $ligne['prix_par_personne'] ?? 0;
            $totalLigne = $ligne['total_ligne'] ?? 0;
            
            $htmlMenus .= "
                <div class='detail-row'>
                    <span class='detail-label'>$menuNom</span>
                    <span class='detail-value'>$nbPersonnes pers. × " . number_format($prixUnitaire, 2) . " €</span>
                </div>
            ";
        }
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Commande confirmée</h1>
            
            <p>Nous avons bien reçu votre commande.</p>
            
            <div class='card'>
                <div class='detail-row'>
                    <span class='detail-label'>Numéro</span>
                    <span class='detail-value'><strong># $numeroCommande</strong></span>
                </div>
                $htmlMenus
                <div class='detail-row'>
                    <span class='detail-label'>Date de prestation</span>
                    <span class='detail-value'>$dateFormatee</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Frais de livraison</span>
                    <span class='detail-value'>" . number_format($fraisLivraison, 2) . " €</span>
                </div>
                <div class='divider'></div>
                <div class='detail-row' style='font-size: 18px; font-weight: 700;'>
                    <span class='detail-label' style='color: #8B1538;'>TOTAL TTC</span>
                    <span class='detail-value' style='color: #8B1538;'>" . number_format($prixTotal, 2) . " €</span>
                </div>
            </div>
            
            <div class='card card-warning'>
                <strong>Statut :</strong> <span class='badge badge-warning'>En attente de validation</span>
                <p style='margin: 10px 0 0 0; font-size: 14px;'>Notre équipe va examiner votre commande.</p>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/mes-commandes' class='btn btn-primary'>Suivre ma commande</a>
            </div>
            
            <p style='margin-top: 30px;'>Cordialement,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Confirmation de commande', $content);
        
        // Version texte
        $mail->AltBody = "Bonjour $prenom,\n\nNous avons bien reçu votre commande # $numeroCommande !\n\nDate de prestation : $dateFormatee\nTotal : " . number_format($prixTotal, 2) . " €\n\nStatut : En attente de validation\n\nCordialement,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email confirmation : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de bienvenue après inscription
 */
function sendWelcomeEmail($email, $prenom) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "🎉 Bienvenue chez Vite & Gourmand !";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Bienvenue chez Vite & Gourmand</h1>
            
            <p>Merci d'avoir créé votre compte. Vous pouvez maintenant commander en ligne.</p>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/menus' class='btn btn-primary'>Découvrir nos menus</a>
            </div>
            
            <p style='margin-top: 30px;'>À bientôt,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Bienvenue', $content);
        
        $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';
        $mail->AltBody = "Bienvenue $prenom !\n\nMerci d'avoir créé votre compte sur Vite & Gourmand.\n\nVotre compte est maintenant actif.\n\nDécouvrez nos menus : $baseUrl/menus\n\nÀ bientôt,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email bienvenue : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de modification de commande
 */
function sendOrderUpdateEmail($email, $prenom, $numeroCommande, $detailsCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "✏️ Modification de commande # $numeroCommande";
        
        $lignesMenus = $detailsCommande['lignesMenus'] ?? [];
        $datePrestation = $detailsCommande['date_prestation'] ?? 'À définir';
        $dateFormatee = date('d/m/Y', strtotime($datePrestation));
        
        $htmlMenus = '';
        foreach ($lignesMenus as $ligne) {
            $menuNom = htmlspecialchars($ligne['menu_nom'] ?? 'Menu');
            $nbPersonnes = $ligne['nombre_personne'] ?? 0;
            $htmlMenus .= "
                <div class='detail-row'>
                    <span class='detail-label'>$menuNom</span>
                    <span class='detail-value'>$nbPersonnes personnes</span>
                </div>
            ";
        }
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Commande modifiée</h1>
            
            <p>Votre commande a été mise à jour avec succès.</p>
            
            <div class='card'>
                <h3 style='margin-top: 0;'>📋 Détails mis à jour</h3>
                <div class='detail-row'>
                    <span class='detail-label'>Numéro</span>
                    <span class='detail-value'><strong># $numeroCommande</strong></span>
                </div>
                $htmlMenus
                <div class='detail-row'>
                    <span class='detail-label'>Date de prestation</span>
                    <span class='detail-value'>$dateFormatee</span>
                </div>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/mes-commandes' class='btn btn-primary'>Voir mes commandes</a>
            </div>
            
            <p style='margin-top: 30px;'>Cordialement,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Modification de commande', $content);
        
        $mail->AltBody = "Bonjour $prenom,\n\nVotre commande # $numeroCommande a été modifiée.\n\nDate de prestation : $dateFormatee\n\nSi vous n'êtes pas à l'origine de cette modification, contactez-nous.\n\nCordialement,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email modification : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de réinitialisation de mot de passe
 */
function sendPasswordResetEmail($email, $prenom, $resetLink) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "🔒 Réinitialisation de mot de passe - Vite & Gourmand";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Réinitialisation mot de passe</h1>
            
            <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.</p>
            
            <div class='card card-warning'>
                <strong>Lien valable 1 heure</strong>
            </div>
            
            <div class='text-center'>
                <a href='$resetLink' class='btn btn-primary'>Réinitialiser mon mot de passe</a>
            </div>
            
            <p style='margin-top: 30px;'>Cordialement,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Réinitialisation mot de passe', $content, "Ne partagez jamais ce lien avec quiconque.");
        
        $mail->AltBody = "Bonjour $prenom,\n\nVous avez demandé à réinitialiser votre mot de passe.\n\nLien (valable 1h) : $resetLink\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\nCordialement,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email reset password : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email envoyé quand une commande est acceptée
 */
function sendOrderAcceptedEmail($email, $prenom, $numeroCommande, $datePrestation) {
    return sendOrderStatusChangeEmail($email, $prenom, $numeroCommande, 'acceptee', $datePrestation);
}

/**
 * Email envoyé quand une commande est terminée (avec demande d'avis)
 */
function sendOrderCompletedEmail($email, $prenom, $numeroCommande, $commandeId) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "⭐ Votre commande est terminée - Donnez votre avis !";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Commande terminée</h1>
            
            <p>Votre commande <strong># $numeroCommande</strong> est terminée. Merci pour votre confiance !</p>
            
            <div class='card'>
                <p style='margin: 0;'>Votre avis nous intéresse pour améliorer nos services.</p>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/avis/nouveau?commande=$commandeId' class='btn btn-primary'>Laisser un avis</a>
            </div>
            
            <p style='margin-top: 30px;'>Merci encore,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Commande terminée', $content);
        
        $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';
        $mail->AltBody = "Bonjour $prenom,\n\nVotre commande # $numeroCommande est terminée.\n\nNous aimerions connaître votre avis :\n$baseUrl/avis/nouveau?commande=$commandeId\n\nMerci,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email commande terminée : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de rappel de retour de matériel
 */
function sendMaterialReturnReminderEmail($email, $prenom, $numeroCommande, $materiels, $dateRetour) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "⏰ Rappel - Retour du matériel # $numeroCommande";
        
        $dateRetourFormatee = date('d/m/Y', strtotime($dateRetour));
        
        $htmlMateriels = '';
        foreach ($materiels as $materiel) {
            $nom = htmlspecialchars($materiel['nom'] ?? 'Matériel');
            $quantite = $materiel['quantite'] ?? 0;
            $htmlMateriels .= "
                <div class='detail-row'>
                    <span class='detail-label'>$nom</span>
                    <span class='detail-value'>$quantite pièce(s)</span>
                </div>
            ";
        }
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Rappel retour matériel</h1>
            
            <p>Merci de nous retourner le matériel emprunté.</p>
            
            <div class='card card-warning'>
                <strong>Retour avant le " . date('d/m/Y', strtotime($dateRetour)) . "</strong>
            </div>
            
            <div class='card'>
                <h3 style='margin-top: 0;'>📦 Matériel à retourner</h3>
                <div class='detail-row'>
                    <span class='detail-label'>Commande</span>
                    <span class='detail-value'><strong># $numeroCommande</strong></span>
                </div>
                $htmlMateriels
            </div>
            
            <p style='margin-top: 30px;'>Merci,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Rappel retour matériel', $content);
        
        $mail->AltBody = "Bonjour $prenom,\n\nRappel : Merci de retourner le matériel emprunté (commande # $numeroCommande) avant le $dateRetourFormatee.\n\nCordialement,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email rappel matériel : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de bienvenue employé (si utilisé)
 */
function sendEmployeeWelcomeEmail($email, $prenom, $nom, $role) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, "$prenom $nom");
        $mail->Subject = "👔 Bienvenue dans l'équipe - Vite & Gourmand";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom . ' ' . $nom) . ",</p>
            
            <h1>Bienvenue dans l'équipe</h1>
            
            <p>Nous sommes heureux de vous accueillir en tant que <strong>" . htmlspecialchars($role) . "</strong>.</p>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/login' class='btn btn-primary'>Se connecter</a>
            </div>
            
            <p style='margin-top: 30px;'>Bienvenue,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Bienvenue employé', $content);
        
        $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';
        $mail->AltBody = "Bonjour $prenom $nom,\n\nBienvenue dans l'équipe en tant que $role !\n\nConnectez-vous : $baseUrl/login\n\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email bienvenue employé : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email création compte employé
 */
function sendEmployeeAccountCreatedEmail($email, $prenom, $nom) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, "$prenom $nom");
        $mail->Subject = "👔 Votre compte employé - Vite & Gourmand";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom . ' ' . $nom) . ",</p>
            
            <h1>Compte employé créé</h1>
            
            <p>Un compte a été créé pour vous sur notre plateforme.</p>
            
            <div class='card'>
                <div class='detail-row'>
                    <span class='detail-label'>Email de connexion</span>
                    <span class='detail-value'>" . htmlspecialchars($email) . "</span>
                </div>
            </div>
            
            <div class='card card-warning'>
                <strong>Contactez l'administrateur pour obtenir votre mot de passe</strong>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/login' class='btn btn-primary'>Se connecter</a>
            </div>
            
            <p style='margin-top: 30px;'>Cordialement,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Compte employé créé', $content);
        
        $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';
        $mail->AltBody = "Bonjour $prenom $nom,\n\nUn compte Employé a été créé pour vous.\n\nEmail : $email\n\nContactez l'administrateur pour obtenir votre mot de passe.\n\nConnexion : $baseUrl/login\n\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email compte employé : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de contact vers le restaurant
 */
function sendContactEmail($nom, $email, $telephone, $titre, $message) {
    $mail = getMailer();
    
    try {
        require_once __DIR__ . '/../Core/EmailSecurity.php';
        
        $cleanEmail = \App\Core\EmailSecurity::sanitizeEmail($email);
        if ($cleanEmail === false) {
            error_log("Tentative d'injection d'email détectée dans sendContactEmail : " . substr($email, 0, 50));
            \App\Core\EmailSecurity::logSecurityEvent('email_injection_blocked', [
                'function' => 'sendContactEmail',
                'original_email' => substr($email, 0, 50)
            ]);
            return false; // Bloquer l'envoi
        }
        $email = $cleanEmail;
        
        $nom = \App\Core\EmailSecurity::sanitizeName($nom);
        
        $titre = \App\Core\EmailSecurity::sanitizeSubject($titre);
        
        $mail->addAddress('contact@viteetgourmand.fr', 'Service Client');
        
        
        $mail->Subject = "Contact - " . $titre;
        
        $content = "
            <h1>Nouveau message de contact</h1>
            
            <div class='card'>
                <div class='detail-row'>
                    <span class='detail-label'>Nom</span>
                    <span class='detail-value'>" . htmlspecialchars($nom) . "</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Email</span>
                    <span class='detail-value'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Téléphone</span>
                    <span class='detail-value'>" . htmlspecialchars($telephone) . "</span>
                </div>
            </div>
            
            <div class='card'>
                <strong>" . htmlspecialchars($titre) . "</strong>
            </div>
            
            <div class='card'>
                <p style='margin: 0; white-space: pre-wrap;'>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            
            <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px;'>
                <p style='margin: 0;'><strong>Pour répondre :</strong></p>
                <p style='margin: 5px 0 0 0;'>
                    Copiez l'email du visiteur : <strong>" . htmlspecialchars($email) . "</strong>
                </p>
            </div>
        ";
        
        $mail->Body = getEmailTemplate('Nouveau contact', $content);
        
        $mail->AltBody = "Nouveau message de contact\n\nNom : $nom\nEmail : $email\nTéléphone : $telephone\n\nSujet : $titre\n\nMessage :\n$message\n\nPour répondre : $email";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email contact : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email d'annulation de commande
 */
function sendCancellationEmailToUser($email, $prenom, $numeroCommande) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        $mail->Subject = "❌ Annulation de commande # $numeroCommande";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>Commande annulée</h1>
            
            <p>Votre commande a bien été annulée.</p>
            
            <div class='card'>
                <div class='detail-row' style='border: none;'>
                    <span class='detail-label'>Commande</span>
                    <span class='detail-value'><strong># $numeroCommande</strong></span>
                </div>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/menus' class='btn btn-primary'>Voir nos menus</a>
            </div>
            
            <p style='margin-top: 30px;'>Cordialement,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate('Annulation de commande', $content);
        
        $mail->AltBody = "Bonjour $prenom,\n\nVotre commande # $numeroCommande a été annulée.\n\nPour toute question, contactez-nous.\n\nCordialement,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email annulation : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Notification annulation au restaurant
 */
function sendCancellationEmailToRestaurant($numeroCommande, $clientNom, $clientEmail) {
    $mail = getMailer();
    
    try {
        $mail->addAddress('contact@viteetgourmand.fr', 'Service Gestion');
        $mail->Subject = "⚠️ Annulation commande # $numeroCommande";
        
        $content = "
            <h1>Annulation de commande</h1>
            
            <p>Une commande a été annulée par le client.</p>
            
            <div class='card'>
                <div class='detail-row'>
                    <span class='detail-label'>Numéro de commande</span>
                    <span class='detail-value'><strong># $numeroCommande</strong></span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Client</span>
                    <span class='detail-value'>" . htmlspecialchars($clientNom) . "</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Email</span>
                    <span class='detail-value'><a href='mailto:" . htmlspecialchars($clientEmail) . "'>" . htmlspecialchars($clientEmail) . "</a></span>
                </div>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/admin/commandes' class='btn btn-primary'>Voir les commandes</a>
            </div>
        ";
        
        $mail->Body = getEmailTemplate('Annulation commande', $content);
        
        $mail->AltBody = "Annulation de commande\n\nCommande : # $numeroCommande\nClient : $clientNom ($clientEmail)\n\nMettre à jour le planning.";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email annulation restaurant : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Email de changement de statut de commande
 */
function sendOrderStatusChangeEmail($email, $prenom, $numeroCommande, $nouveauStatut, $datePrestation = null) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $prenom);
        
        // Configuration selon le statut
        $config = [
            'acceptee' => [
                'sujet' => '✅ Commande acceptée',
                'titre' => 'Votre commande est confirmée !',
                'message' => 'Excellente nouvelle ! Notre équipe a validé votre commande. Nous la préparons avec le plus grand soin.',
                'badge' => 'badge-success',
                'badgeText' => 'Confirmée',
                'card' => 'card-success'
            ],
            'en_preparation' => [
                'sujet' => '👨‍🍳 Commande en préparation',
                'titre' => 'Préparation en cours',
                'message' => 'Nos équipes sont au travail ! Votre commande est en cours de préparation dans nos cuisines.',
                'badge' => 'badge-warning',
                'badgeText' => 'En préparation',
                'card' => 'card-warning'
            ],
            'en_cours_livraison' => [
                'sujet' => '🚚 Commande en livraison',
                'titre' => 'C\'est parti pour la livraison !',
                'message' => 'Votre commande est en route. Notre équipe arrive bientôt chez vous.',
                'badge' => 'badge-warning',
                'badgeText' => 'En livraison',
                'card' => 'card-info'
            ],
            'livree' => [
                'sujet' => '📦 Commande livrée',
                'titre' => 'Livraison effectuée',
                'message' => 'Votre commande vient d\'être livrée. Bon appétit ! 🍽️',
                'badge' => 'badge-success',
                'badgeText' => 'Livrée',
                'card' => 'card-success'
            ],
            'terminee' => [
                'sujet' => '⭐ Commande terminée',
                'titre' => 'Merci pour votre confiance !',
                'message' => 'Votre commande est terminée. Nous espérons que tout s\'est bien passé !',
                'badge' => 'badge-success',
                'badgeText' => 'Terminée',
                'card' => 'card-success'
            ],
            'annulee' => [
                'sujet' => '❌ Commande annulée',
                'titre' => 'Commande annulée',
                'message' => 'Votre commande a été annulée. Si vous avez des questions, n\'hésitez pas à nous contacter.',
                'badge' => 'badge-danger',
                'badgeText' => 'Annulée',
                'card' => 'card-danger'
            ]
        ];
        
        $conf = $config[$nouveauStatut] ?? $config['acceptee'];
        
        $mail->Subject = $conf['sujet'] . " # $numeroCommande";
        
        $dateInfo = $datePrestation ? "
            <div class='detail-row'>
                <span class='detail-label'>Date de prestation</span>
                <span class='detail-value'>" . date('d/m/Y', strtotime($datePrestation)) . "</span>
            </div>
        " : "";
        
        $content = "
            <p class='greeting'>Bonjour " . htmlspecialchars($prenom) . ",</p>
            
            <h1>{$conf['titre']}</h1>
            
            <p>{$conf['message']}</p>
            
            <div class='card {$conf['card']}'>
                <div class='detail-row'>
                    <span class='detail-label'>Numéro de commande</span>
                    <span class='detail-value'><strong># $numeroCommande</strong></span>
                </div>
                $dateInfo
                <div class='detail-row' style='border: none;'>
                    <span class='detail-label'>Statut</span>
                    <span class='detail-value'><span class='badge {$conf['badge']}'>{$conf['badgeText']}</span></span>
                </div>
            </div>
            
            <div class='text-center'>
                <a href='" . (getenv('APP_URL') ?: 'http://localhost:8082') . "/mes-commandes' class='btn btn-primary'>Suivre ma commande</a>
            </div>
            
            <p style='margin-top: 30px;'>Cordialement,<br><strong>L'équipe Vite & Gourmand</strong></p>
        ";
        
        $mail->Body = getEmailTemplate($conf['sujet'], $content);
        
        $mail->AltBody = "Bonjour $prenom,\n\n{$conf['titre']}\n\nCommande # $numeroCommande\nStatut : {$conf['badgeText']}\n\nCordialement,\nL'équipe Vite & Gourmand";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email changement statut : " . $mail->ErrorInfo);
        return false;
    }
}
