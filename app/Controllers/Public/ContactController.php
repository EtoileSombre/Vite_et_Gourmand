<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Repository\ContactRepositoryInterface;
use App\Factory\RepositoryFactory;

class ContactController extends Controller
{
    private ContactRepositoryInterface $contactRepository;

    public function __construct()
    {
        $factory = RepositoryFactory::getInstance();
        $this->contactRepository = $factory->createContactRepository();
    }

    /**
     * Affiche le formulaire de contact et traite l'envoi
     * Champs : Email, Titre, Description
     */
    public function index()
    {
        $errors = [];
        $request = new Request();

        if ($request->isPost()) {
            // Vérification CSRF
            if (!csrf_verify()) {
                $errors[] = "Erreur de sécurité. Veuillez réessayer.";
                $this->render('public/contact/index', ['errors' => $errors]);
                return;
            }
            
            $email = trim($request->post('email'));
            $titre = trim($request->post('titre'));
            $description = trim($request->post('description'));

            // Validation ECF
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }

            if (empty($titre)) {
                $errors[] = "Le titre est obligatoire.";
            } elseif (strlen($titre) < 5) {
                $errors[] = "Le titre doit contenir au moins 5 caractères.";
            }

            if (empty($description)) {
                $errors[] = "La description est obligatoire.";
            } elseif (strlen($description) < 10) {
                $errors[] = "La description doit contenir au moins 10 caractères.";
            }

            // Si pas d'erreurs, sauvegarder et envoyer email
            if (empty($errors)) {
                try {
                    // Sauvegarder en base de données
                    $contactId = $this->contactRepository->createContact([
                        'nom' => '', // Optionnel, pas dans l'énoncé ECF
                        'email' => $email,
                        'sujet' => htmlspecialchars($titre),
                        'message' => htmlspecialchars($description)
                    ]);

                    $this->sendEmailToEntreprise($email, $titre, $description, $contactId);

                    Session::set('contact_envoye', true);
                    $this->redirect('/contact');
                    
                } catch (\Exception $e) {
                    error_log("Erreur contact : " . $e->getMessage());
                    $errors[] = "Une erreur est survenue. Veuillez réessayer.";
                }
            } else {
                Session::set('flash_error', implode('<br>', $errors));
            }
        }

        $this->render('public/contact/index', [
            'errors' => $errors
        ]);
    }

    /**
     *Envoie un email à l'entreprise avec la demande de contact
     */
    private function sendEmailToEntreprise(string $email, string $titre, string $description, int $contactId): void
    {
        require_once __DIR__ . '/../../config/mail.php';

        try {
            $mail = getMailer();
            
            // Destinataire : entreprise Vite & Gourmand
            $mail->addAddress('contact@viteetgourmand.com', 'Vite & Gourmand');
            $mail->setFrom('noreply@viteetgourmand.com', 'Formulaire Contact Site Web');
            $mail->addReplyTo($email, $email); // Permet de répondre directement

            $mail->Subject = "📩 Nouveau message de contact - #$contactId : " . htmlspecialchars($titre);
            
            $htmlContent = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; background-color: #f9f9f9; }
                    .info-box { background-color: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px; margin: 15px 0; }
                    .message-box { background-color: #ffffff; border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
                    .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 0.9em; }
                    .badge { display: inline-block; padding: 5px 10px; background-color: #667eea; color: white; border-radius: 3px; font-size: 0.9em; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>📩 Nouveau Message de Contact</h1>
                        <p style='margin: 0; font-size: 1.1em;'>Formulaire Site Web - Demande #$contactId</p>
                    </div>
                    <div class='content'>
                        <div class='info-box'>
                            <p style='margin: 0;'><strong>Email du visiteur :</strong></p>
                            <p style='margin: 5px 0 0 0; font-size: 1.1em;'>
                                <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a>
                            </p>
                        </div>
                        
                        <div class='info-box'>
                            <p style='margin: 0;'><strong>📋 Titre de la demande :</strong></p>
                            <p style='margin: 5px 0 0 0; font-size: 1.1em;'>" . htmlspecialchars($titre) . "</p>
                        </div>
                        
                        <h3 style='margin-top: 30px;'>💬 Description :</h3>
                        <div class='message-box'>
                            " . nl2br(htmlspecialchars($description)) . "
                        </div>
                        
                        <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px;'>
                            <p style='margin: 0;'><strong>⚡ Action requise :</strong></p>
                            <p style='margin: 5px 0 0 0;'>
                                Répondez à ce visiteur en cliquant sur 'Répondre' dans votre client email.
                                <br>L'adresse de réponse est configurée automatiquement : <strong>" . htmlspecialchars($email) . "</strong>
                            </p>
                        </div>
                        
                        <p style='margin-top: 30px; text-align: center; color: #666;'>
                            <small>Message reçu le " . date('d/m/Y à H:i') . "</small>
                        </p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Vite & Gourmand - Notification Automatique</p>
                        <p style='font-size: 0.8em; margin-top: 10px;'>
                            Demande #$contactId enregistrée dans la base de données
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->Body = $htmlContent;
            
            $mail->AltBody = "Nouveau message de contact #$contactId\n\n"
                           . "Email : $email\n"
                           . "Titre : $titre\n\n"
                           . "Description :\n$description\n\n"
                           . "---\n"
                           . "Répondez directement à cet email pour contacter le visiteur.";
            
            $sent = $mail->send();
            
            if ($sent) {
                error_log("Email contact #$contactId envoyé à l'entreprise depuis : $email");
            } else {
                error_log("Échec envoi email contact #$contactId");
            }
            
        } catch (\Exception $e) {
            error_log("Erreur envoi email contact : " . $e->getMessage());
        }
    }
}
