<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Contact;

class ContactController extends Controller
{
    private Contact $contactModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
    }

    /**
     * Affiche le formulaire de contact et traite l'envoi
     */
    public function index()
    {
        $errors = [];
        $success = false;
        $request = new Request();

        if ($request->isPost()) {
            $nom = trim($request->post('nom'));
            $email = trim($request->post('email'));
            $sujet = trim($request->post('sujet', 'Demande de contact'));
            $message = trim($request->post('message'));

            // Validation
            if (empty($nom)) {
                $errors[] = "Le nom est obligatoire.";
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }

            if (empty($message)) {
                $errors[] = "Le message est obligatoire.";
            }

            if (strlen($message) < 10) {
                $errors[] = "Le message doit contenir au moins 10 caractères.";
            }

            // Si pas d'erreurs, sauvegarder et envoyer email
            if (empty($errors)) {
                try {
                    // Sauvegarder en base de données
                    $contactId = $this->contactModel->createContact([
                        'nom' => htmlspecialchars($nom),
                        'email' => $email,
                        'sujet' => htmlspecialchars($sujet),
                        'message' => htmlspecialchars($message)
                    ]);

                    // Envoyer un email à l'administrateur
                    $this->sendEmailToAdmin($nom, $email, $sujet, $message, $contactId);

                    Session::set('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
                    $this->redirect('/contact');
                    
                } catch (\Exception $e) {
                    error_log("Erreur contact : " . $e->getMessage());
                    $errors[] = "Une erreur est survenue. Veuillez réessayer.";
                }
            }
        }

        $this->render('contact/index', [
            'errors' => $errors,
            'success' => $success
        ]);
    }

    /**
     * Envoie un email à l'administrateur
     */
    private function sendEmailToAdmin(string $nom, string $email, string $sujet, string $message, int $contactId): void
    {
        require_once __DIR__ . '/../config/mail.php';

        try {
            $mail = getMailer();
            
            // Destinataire : admin
            $mail->addAddress('admin@viteetgourmand.fr', 'Administration Vite & Gourmand');
            $mail->setFrom('noreply@viteetgourmand.fr', 'Vite & Gourmand');
            $mail->addReplyTo($email, $nom);

            $mail->Subject = "Nouveau message de contact - #$contactId";
            
            $htmlContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #7B1E1E 0%, #6a1919 100%); color: white; padding: 20px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 24px;'>📬 Nouveau Message de Contact</h1>
                </div>
                
                <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;'>
                    <div style='background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                        <h2 style='color: #7B1E1E; margin-top: 0;'>Informations du contact</h2>
                        
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; width: 120px;'>Nom :</td>
                                <td style='padding: 10px 0;'>" . htmlspecialchars($nom) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold;'>Email :</td>
                                <td style='padding: 10px 0;'><a href='mailto:$email'>$email</a></td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold;'>Sujet :</td>
                                <td style='padding: 10px 0;'>" . htmlspecialchars($sujet) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold;'>Référence :</td>
                                <td style='padding: 10px 0;'>#$contactId</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style='background: white; padding: 20px; border-radius: 8px;'>
                        <h3 style='color: #7B1E1E; margin-top: 0;'>Message :</h3>
                        <p style='line-height: 1.6; white-space: pre-wrap;'>" . nl2br(htmlspecialchars($message)) . "</p>
                    </div>
                    
                    <div style='text-align: center; margin-top: 20px;'>
                        <a href='http://localhost:8080/admin/contacts' 
                           style='display: inline-block; background: #7B1E1E; color: white; padding: 12px 30px; 
                                  text-decoration: none; border-radius: 5px; font-weight: bold;'>
                            Voir tous les messages
                        </a>
                    </div>
                    
                    <p style='margin-top: 30px; color: #666; font-size: 12px; text-align: center;'>
                        Vous recevez cet email car vous êtes administrateur de Vite & Gourmand
                    </p>
                </div>
            </div>
            ";
            
            $mail->Body = $htmlContent;
            $mail->AltBody = "Nouveau message de contact de $nom ($email)\n\nSujet: $sujet\n\n$message";
            
            $mail->send();
        } catch (\Exception $e) {
            error_log("Erreur envoi email contact : " . $e->getMessage());
        }
    }
}
