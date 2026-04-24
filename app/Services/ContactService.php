<?php

namespace App\Services;

use App\Core\EmailSecurity;
use App\Repository\ContactRepositoryInterface;
use App\Services\Exceptions\ContactException;

class ContactService extends AbstractService
{
    private const RATE_LIMIT_MAX = 5;
    private const RATE_LIMIT_WINDOW = 3600;
    private const TITRE_MIN_LENGTH = 5;
    private const DESCRIPTION_MIN_LENGTH = 10;
    private const DESTINATAIRE_EMAIL = 'contact@viteetgourmand.com';
    private const DESTINATAIRE_NOM = 'Vite & Gourmand';
    private const FROM_EMAIL = 'noreply@viteetgourmand.com';
    private const FROM_NOM = 'Formulaire Contact Site Web';

    public function __construct(
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    /**
     * Retourne ['honeypot' => bool, 'contact_id' => ?int]. Si honeypot=true,
     * le contrôleur doit feindre le succès.
     *
     * @throws ContactException
     */
    public function submit(array $data, string $clientIp): array
    {
        if (!empty($data['website'] ?? '')) {
            EmailSecurity::logSecurityEvent('honeypot_triggered', ['ip' => $clientIp]);
            return ['honeypot' => true, 'contact_id' => null];
        }

        if (!EmailSecurity::checkRateLimit($clientIp, self::RATE_LIMIT_MAX, self::RATE_LIMIT_WINDOW)) {
            EmailSecurity::logSecurityEvent('rate_limit_exceeded', [
                'form' => 'contact',
                'ip'   => $clientIp,
            ]);
            throw new ContactException("Trop de messages envoyés. Veuillez réessayer dans une heure.");
        }

        $email = trim((string) ($data['email'] ?? ''));
        $titre = trim((string) ($data['titre'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        $errors = [];

        $cleanEmail = EmailSecurity::sanitizeEmail($email);
        if ($cleanEmail === false) {
            $errors[] = "L'email n'est pas valide.";
            EmailSecurity::logSecurityEvent('invalid_email_attempt', [
                'email' => substr($email, 0, 50),
                'ip'    => $clientIp,
            ]);
        } else {
            $email = $cleanEmail;
        }

        if ($titre === '') {
            $errors[] = "Le titre est obligatoire.";
        } elseif (strlen($titre) < self::TITRE_MIN_LENGTH) {
            $errors[] = "Le titre doit contenir au moins " . self::TITRE_MIN_LENGTH . " caractères.";
        }
        $titre = EmailSecurity::sanitizeSubject($titre);

        if ($description === '') {
            $errors[] = "La description est obligatoire.";
        } elseif (strlen($description) < self::DESCRIPTION_MIN_LENGTH) {
            $errors[] = "La description doit contenir au moins "
                . self::DESCRIPTION_MIN_LENGTH . " caractères.";
        }

        if (!empty($errors)) {
            throw new ContactException(implode('<br>', $errors));
        }

        try {
            $contactId = $this->contactRepository->createContact([
                'nom'     => '',
                'email'   => $email,
                'sujet'   => $titre,
                'message' => $description,
            ]);
        } catch (\Exception $e) {
            error_log("Erreur contact : " . $e->getMessage());
            throw new ContactException("Une erreur est survenue. Veuillez réessayer.");
        }

        $this->sendEmailToEntreprise($email, $titre, $description, (int) $contactId);

        return ['honeypot' => false, 'contact_id' => (int) $contactId];
    }

    private function sendEmailToEntreprise(
        string $email,
        string $titre,
        string $description,
        int $contactId
    ): void {
        require_once __DIR__ . '/../config/mail.php';

        try {
            $cleanEmail = EmailSecurity::sanitizeEmail($email);
            if ($cleanEmail === false) {
                error_log("Tentative d'injection d'email détectée : " . substr($email, 0, 50));
                EmailSecurity::logSecurityEvent('email_injection_blocked', [
                    'original_email' => substr($email, 0, 50),
                    'contact_id'     => $contactId,
                ]);
                return;
            }
            $email = $cleanEmail;
            $titre = EmailSecurity::sanitizeSubject($titre);

            $mail = getMailer();
            $mail->addAddress(self::DESTINATAIRE_EMAIL, self::DESTINATAIRE_NOM);
            $mail->setFrom(self::FROM_EMAIL, self::FROM_NOM);
            $mail->Subject = "📩 Nouveau message de contact - #{$contactId} : " . $titre;
            $mail->Body = $this->buildHtmlBody($email, $titre, $description, $contactId);
            $mail->AltBody = $this->buildTextBody($email, $titre, $description, $contactId);

            if ($mail->send()) {
                error_log("Email contact #{$contactId} envoyé à l'entreprise depuis : {$email}");
            } else {
                error_log("Échec envoi email contact #{$contactId}");
            }
        } catch (\Exception $e) {
            error_log("Erreur envoi email contact : " . $e->getMessage());
        }
    }

    private function buildHtmlBody(string $email, string $titre, string $description, int $contactId): string
    {
        $safeEmail = htmlspecialchars($email);
        $safeTitre = htmlspecialchars($titre);
        $safeDescription = nl2br(htmlspecialchars($description));
        $date = date('d/m/Y à H:i');
        $year = date('Y');

        return <<<HTML
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📩 Nouveau Message de Contact</h1>
            <p style="margin: 0; font-size: 1.1em;">Formulaire Site Web - Demande #{$contactId}</p>
        </div>
        <div class="content">
            <div class="info-box">
                <p style="margin: 0;"><strong>Email du visiteur :</strong></p>
                <p style="margin: 5px 0 0 0; font-size: 1.1em;">
                    <a href="mailto:{$safeEmail}">{$safeEmail}</a>
                </p>
            </div>
            <div class="info-box">
                <p style="margin: 0;"><strong>📋 Titre de la demande :</strong></p>
                <p style="margin: 5px 0 0 0; font-size: 1.1em;">{$safeTitre}</p>
            </div>
            <h3 style="margin-top: 30px;">💬 Description :</h3>
            <div class="message-box">{$safeDescription}</div>
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px;">
                <p style="margin: 0;"><strong>⚡ Action requise :</strong></p>
                <p style="margin: 5px 0 0 0;">
                    Pour répondre au visiteur, copiez son email : <strong>{$safeEmail}</strong>
                </p>
            </div>
            <p style="margin-top: 30px; text-align: center; color: #666;">
                <small>Message reçu le {$date}</small>
            </p>
        </div>
        <div class="footer">
            <p>© {$year} Vite & Gourmand - Notification Automatique</p>
            <p style="font-size: 0.8em; margin-top: 10px;">
                Demande #{$contactId} enregistrée dans la base de données
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function buildTextBody(string $email, string $titre, string $description, int $contactId): string
    {
        return "Nouveau message de contact #{$contactId}\n\n"
            . "Email : {$email}\n"
            . "Titre : {$titre}\n\n"
            . "Description :\n{$description}\n\n"
            . "---\n"
            . "Pour répondre, utilisez l'email : {$email}";
    }
}
