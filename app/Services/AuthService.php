<?php

namespace App\Services;

use App\Core\EmailSecurity;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repository\PasswordResetRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Services\Exceptions\AuthException;

class AuthService extends AbstractService
{
    public const ROLE_UTILISATEUR_ID = 1;
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_WINDOW_SECONDS = 900;
    private const PASSWORD_MIN_LENGTH = 10;
    private const RESET_TOKEN_TTL = '+1 hour';

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
    ) {
    }

    /**
     * @throws AuthException si rate limit dépassé ou identifiants invalides
     */
    public function authenticate(string $email, string $password, string $clientIp): User
    {
        if (!EmailSecurity::checkRateLimit(
            $clientIp,
            self::LOGIN_MAX_ATTEMPTS,
            self::LOGIN_WINDOW_SECONDS,
            'login'
        )) {
            EmailSecurity::logSecurityEvent('login_rate_limit', ['ip' => $clientIp]);
            throw new AuthException(
                "Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes."
            );
        }

        if ($email === '' || $password === '') {
            throw new AuthException("Tous les champs sont obligatoires.");
        }

        $user = $this->userRepository->findByEmail($email);
        if (!$user || !password_verify($password, $user->getPassword())) {
            error_log("[AUTH] Échec connexion : email={$email}, IP={$clientIp}");
            throw new AuthException("Identifiants invalides.");
        }

        error_log(sprintf(
            "[AUTH] Connexion réussie : user_id=%d, email=%s, role=%s",
            $user->getUtilisateurId(),
            $email,
            $user->getRoleLibelle()
        ));

        return $user;
    }

    /**
     * @throws AuthException en cas de validation ou d'email déjà utilisé
     */
    public function register(array $data): array
    {
        $nom = trim((string) ($data['nom'] ?? ''));
        $prenom = trim((string) ($data['prenom'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $telephone = trim((string) ($data['telephone'] ?? ''));
        $adressePostale = trim((string) ($data['adresse_postale'] ?? ''));
        $codePostal = trim((string) ($data['code_postal'] ?? ''));
        $ville = trim((string) ($data['ville'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');

        $errors = [];
        if ($nom === '')     $errors[] = "Le nom est obligatoire.";
        if ($prenom === '')  $errors[] = "Le prénom est obligatoire.";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        }
        if ($telephone === '')      $errors[] = "Le numéro de GSM est obligatoire.";
        if ($adressePostale === '') $errors[] = "L'adresse postale est obligatoire.";
        if ($codePostal === '' || !preg_match('/^[0-9]{5}$/', $codePostal)) {
            $errors[] = "Le code postal doit contenir 5 chiffres.";
        }
        if ($ville === '') $errors[] = "La ville est obligatoire.";

        $errors = array_merge($errors, $this->validatePasswordComplexity($password));
        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if ($errors === [] && $this->userRepository->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        if (!empty($errors)) {
            throw new AuthException(implode(' ', $errors));
        }

        $userId = $this->userRepository->create([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse_postale' => $adressePostale,
            'code_postal' => $codePostal,
            'ville' => $ville,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id' => self::ROLE_UTILISATEUR_ID,
            'actif' => 1,
        ]);

        if (!$userId) {
            throw new AuthException("Impossible de créer le compte. Veuillez réessayer.");
        }

        try {
            require_once __DIR__ . '/../config/mail.php';
            sendWelcomeEmail($email, $prenom);
        } catch (\Exception $e) {
            error_log("Erreur envoi email bienvenue: " . $e->getMessage());
        }

        error_log("[AUTH] Inscription réussie : user_id={$userId}, email={$email}");

        return [
            'user_id' => (int) $userId,
            'prenom'  => $prenom,
            'email'   => $email,
            'role'    => 'utilisateur',
        ];
    }

    /**
     * Réponse neutre : retourne false si l'email est inconnu (ne révèle pas).
     *
     * @throws AuthException si l'email est invalide ou si l'envoi échoue
     */
    public function requestPasswordReset(string $email, string $baseUrl): bool
    {
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthException("Veuillez saisir une adresse email valide.");
        }

        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime(self::RESET_TOKEN_TTL));
        $this->passwordResetRepository->createToken($email, $token, $expiresAt);

        require_once __DIR__ . '/../config/mail.php';
        $resetLink = rtrim($baseUrl, '/') . '/reset-password?token=' . $token;

        if (!sendPasswordResetEmail($email, $user->getPrenom(), $resetLink)) {
            throw new AuthException("Erreur lors de l'envoi de l'email. Veuillez réessayer.");
        }

        return true;
    }

    /**
     * @throws AuthException si le token est invalide
     */
    public function validateResetToken(string $token): PasswordReset
    {
        if ($token === '') {
            throw new AuthException("Ce lien de réinitialisation est invalide ou a expiré.");
        }

        $resetRequest = $this->passwordResetRepository->findValidToken($token);
        if (!$resetRequest) {
            throw new AuthException("Ce lien de réinitialisation est invalide ou a expiré.");
        }

        return $resetRequest;
    }

    /**
     * @throws AuthException
     */
    public function resetPassword(string $token, string $password, string $passwordConfirm): void
    {
        $resetRequest = $this->validateResetToken($token);

        $errors = $this->validatePasswordComplexity($password);
        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!empty($errors)) {
            throw new AuthException(implode(' ', $errors));
        }

        $user = $this->userRepository->findByEmail($resetRequest->getEmail());
        if (!$user) {
            throw new AuthException("Utilisateur introuvable pour ce token.");
        }

        $this->userRepository->update(
            $user->getUtilisateurId(),
            ['password' => password_hash($password, PASSWORD_DEFAULT)]
        );
        $this->passwordResetRepository->markTokenAsUsed($token);
    }

    /**
     * 10+ caractères avec majuscule, minuscule, chiffre et caractère spécial.
     *
     * @return string[]
     */
    private function validatePasswordComplexity(string $password): array
    {
        $errors = [];
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "Le mot de passe doit contenir au moins "
                . self::PASSWORD_MIN_LENGTH . " caractères.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
        }
        return $errors;
    }
}
