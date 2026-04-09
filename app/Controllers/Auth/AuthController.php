<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Repository\UserRepositoryInterface;
use App\Repository\PasswordResetRepositoryInterface;
use App\Factory\RepositoryFactory;
use App\Core\Request;
use App\Core\Session;
use App\Core\EmailSecurity;

class AuthController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private PasswordResetRepositoryInterface $passwordResetRepository;

    public function __construct()
    {
        parent::__construct();
        // Utilisation de la Factory pour créer le repository
        $factory = RepositoryFactory::getInstance();
        $this->userRepository = $factory->createUserRepository();
        $this->passwordResetRepository = $factory->createPasswordResetRepository();
    }

    public function login()
    {
        $errors = [];
        $request = new Request();
        
        if ($request->isPost()) {
            // Vérification CSRF
            if (!csrf_verify()) {
                $errors[] = "Erreur de sécurité. Veuillez réessayer.";
                $this->render('auth/login', ['errors' => $errors]);
                return;
            }

            // Rate limiting anti brute-force : 5 tentatives par IP / 15 min
            $clientIp = EmailSecurity::getClientIp();
            if (!EmailSecurity::checkRateLimit($clientIp, 5, 900, 'login')) {
                $errors[] = "Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes.";
                EmailSecurity::logSecurityEvent('login_rate_limit', ['ip' => $clientIp]);
                $this->render('auth/login', ['errors' => $errors]);
                return;
            }
            
            $email = $request->post('email');
            $password = $request->post('password');
            $redirect = $request->post('redirect') ?: $request->get('redirect');
            
            // Validation simple
            if (empty($email) || empty($password)) {
                $errors[] = "Tous les champs sont obligatoires.";
            } else {
                $user = $this->userRepository->findByEmail($email);
                
                if ($user && password_verify($password, $user->getPassword())) {
                    // Régénérer l'ID de session pour éviter la fixation de session
                    session_regenerate_id(true);

                    Session::set('user_id', $user->getUtilisateurId());
                    Session::set('user_prenom', $user->getPrenom());
                    Session::set('user_email', $user->getEmail());
                    Session::set('user_role', $user->getRoleLibelle());

                    error_log("[AUTH] Connexion réussie : user_id={$user->getUtilisateurId()}, email={$email}, role={$user->getRoleLibelle()}");
                    
                    // Gestion de la redirection (protection open redirect : doit commencer par / mais pas //)
                    if (!empty($redirect) && strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
                        $this->redirect($redirect);
                    } elseif ($user->getRoleLibelle() === 'administrateur') {
                        $this->redirect('/admin');
                    } elseif ($user->getRoleLibelle() === 'employé') {
                        $this->redirect('/employe');
                    } else {
                        $this->redirect('/');
                    }
                } else {
                    error_log("[AUTH] Échec connexion : email={$email}, IP={$clientIp}");
                    $errors[] = "Identifiants invalides.";
                }
            }
        }
        
        // Passer le paramètre redirect à la vue
        $redirect = $request->get('redirect');
        $this->render('auth/login', [
            'errors' => $errors,
            'redirect' => $redirect
        ]);
    }

    public function register()
    {
        $errors = [];
        $request = new Request();
        
        if ($request->isPost()) {
            // Vérification CSRF
            if (!csrf_verify()) {
                $errors[] = "Erreur de sécurité. Veuillez réessayer.";
                $this->render('auth/register', ['errors' => $errors]);
                return;
            }
            
            $nom = trim($request->post('nom'));
            $prenom = trim($request->post('prenom'));
            $email = trim($request->post('email'));
            $telephone = trim($request->post('telephone'));
            $adresse_postale = trim($request->post('adresse_postale'));
            $code_postal = trim($request->post('code_postal'));
            $ville = trim($request->post('ville'));
            $password = $request->post('password');
            $passwordConfirm = $request->post('password_confirm');
            $redirect = $request->post('redirect') ?: $request->get('redirect');
            
            // Validation des champs obligatoires
            if (empty($nom)) {
                $errors[] = "Le nom est obligatoire.";
            }
            
            if (empty($prenom)) {
                $errors[] = "Le prénom est obligatoire.";
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }
            
            if (empty($telephone)) {
                $errors[] = "Le numéro de GSM est obligatoire.";
            }
            
            if (empty($adresse_postale)) {
                $errors[] = "L'adresse postale est obligatoire.";
            }
            
            if (empty($code_postal) || !preg_match('/^[0-9]{5}$/', $code_postal)) {
                $errors[] = "Le code postal doit contenir 5 chiffres.";
            }
            
            if (empty($ville)) {
                $errors[] = "La ville est obligatoire.";
            }
            
            // Validation du mot de passe sécurisé (10 caractères minimum)
            if (strlen($password) < 10) {
                $errors[] = "Le mot de passe doit contenir au moins 10 caractères.";
            }
            
            // Vérifier complexité (1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial)
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
            
            if ($password !== $passwordConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
            
            if ($this->userRepository->findByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            // Créer l'utilisateur
            if (empty($errors)) {
                $userId = $this->userRepository->create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'adresse_postale' => $adresse_postale,
                    'code_postal' => $code_postal,
                    'ville' => $ville,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role_id' => 1, // Role "utilisateur"
                    'actif' => 1
                ]);
                
                // Envoyer l'email de bienvenue automatique
                if ($userId) {
                    try {
                        require_once __DIR__ . '/../../config/mail.php';
                        sendWelcomeEmail($email, $prenom);
                    } catch (\Exception $e) {
                        error_log("Erreur envoi email bienvenue: " . $e->getMessage());
                        // On ne bloque pas l'inscription si l'email échoue
                    }
                }
                
                error_log("[AUTH] Inscription réussie : user_id={$userId}, email={$email}");

                // Connecter automatiquement l'utilisateur après inscription
                Session::set('user_id', $userId);
                Session::set('user_prenom', $prenom);
                Session::set('user_email', $email);
                Session::set('user_role', 'utilisateur');
                
                // Message de succès et redirection
                Session::set('flash_success', "Bienvenue $prenom ! Votre compte a été créé avec succès.");
                
                // Gestion de la redirection après inscription
                if (!empty($redirect) && strpos($redirect, '/') === 0) {
                    $this->redirect($redirect);
                } else {
                    $this->redirect('/');
                }
            }
        }
        
        // Passer le paramètre redirect à la vue
        $redirect = $request->get('redirect');
        $this->render('auth/register', [
            'errors' => $errors,
            'redirect' => $redirect
        ]);
    }

    // Déconnexion
    public function logout()
    {
        $userId = Session::get('user_id');
        $email = Session::get('user_email');
        error_log("[AUTH] Déconnexion : user_id={$userId}, email={$email}");
        Session::destroy();
        $this->redirect('/');
    }

    public function forgotPassword()
    {
        $errors = [];
        $success = '';
        $request = new Request();
        
        if ($request->isPost()) {
            // Vérification CSRF
            if (!csrf_verify()) {
                $errors[] = "Erreur de sécurité. Veuillez réessayer.";
                $this->render('auth/forgot-password', ['errors' => $errors]);
                return;
            }
            
            $email = $request->post('email');
            
            // Validation
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Veuillez saisir une adresse email valide.";
            } else {
                // Vérifier si l'email existe
                $user = $this->userRepository->findByEmail($email);
                
                if ($user) {
                    // Générer un token unique
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Stocker le token via le repository PasswordReset
                    $this->passwordResetRepository->createToken($email, $token, $expiresAt);
                    
                    // Envoyer l'email avec le lien de réinitialisation
                    require_once __DIR__ . '/../../config/mail.php';
                    $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';
                    $resetLink = $baseUrl . "/reset-password?token=" . $token;
                    
                    if (sendPasswordResetEmail($email, $user->getPrenom(), $resetLink)) {
                        $success = "Un email contenant les instructions de réinitialisation a été envoyé à votre adresse.";
                    } else {
                        $errors[] = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
                    }
                } else {
                    // Pour des raisons de sécurité, on ne révèle pas si l'email existe ou non
                    $success = "Si cette adresse email existe, vous recevrez un lien de réinitialisation.";
                }
            }
        }
        
        $this->render('auth/forgot-password', [
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function resetPassword()
    {
        $errors = [];
        $success = '';
        $request = new Request();
        $token = $request->get('token');
        
        // Vérifier que le token est fourni
        if (empty($token)) {
            $this->redirect('/forgot-password');
            return;
        }
        
        // Vérifier la validité du token via le repository
        $resetRequest = $this->passwordResetRepository->findValidToken($token);
        
        if (!$resetRequest) {
            $errors[] = "Ce lien de réinitialisation est invalide ou a expiré.";
            $this->render('auth/reset-password', [
                'errors' => $errors,
                'tokenValid' => false
            ]);
            return;
        }
        
        // Si formulaire soumis
        if ($request->isPost()) {
            // Vérification CSRF
            if (!csrf_verify()) {
                $errors[] = "Erreur de sécurité. Veuillez réessayer.";
                $this->render('auth/reset-password', [
                    'errors' => $errors,
                    'token' => $token,
                    'tokenValid' => true
                ]);
                return;
            }
            
            $password = $request->post('password');
            $passwordConfirm = $request->post('password_confirm');
            
            // Validation du mot de passe
            if (empty($password) || strlen($password) < 10) {
                $errors[] = "Le mot de passe doit contenir au moins 10 caractères.";
            }
            
            // Vérifier complexité (1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial)
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
            
            if ($password !== $passwordConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
            
            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user = $this->userRepository->findByEmail($resetRequest->getEmail());
                
                if ($user) {
                    $this->userRepository->update($user->getUtilisateurId(), ['password' => $hashedPassword]);
                }
                
                // Marquer le token comme utilisé
                $this->passwordResetRepository->markTokenAsUsed($token);
                
                $success = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            }
        }
        
        $this->render('auth/reset-password', [
            'errors' => $errors,
            'success' => $success,
            'token' => $token,
            'tokenValid' => true
        ]);
    }
}
