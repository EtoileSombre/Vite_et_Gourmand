<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\MongoLogger;

class AuthController extends Controller
{
    // Connexion
    public function login()
    {
        $errors = [];
        $request = new Request();
        
        if ($request->isPost()) {
            $email = $request->post('email');
            $password = $request->post('password');
            
            // Validation simple
            if (empty($email) || empty($password)) {
                $errors[] = "Tous les champs sont obligatoires.";
            } else {
                $user = User::findByEmail($email);
                
                if ($user && password_verify($password, $user['password'])) {
                    Session::set('user_id', $user['utilisateur_id']);
                    Session::set('user_prenom', $user['prenom']);
                    Session::set('user_email', $user['email']);
                    Session::set('user_role', $user['role']);
                    
                    // Logger la connexion dans MongoDB
                    MongoLogger::logUserActivity('login', $user['utilisateur_id'], [
                        'role' => $user['role'],
                        'email' => $user['email']
                    ]);
                    
                    if ($user['role'] === 'administrateur') {
                        $this->redirect('/admin');
                    } else {
                        $this->redirect('/');
                    }
                } else {
                    $errors[] = "Identifiants invalides.";
                }
            }
        }
        
        $this->render('auth/login', ['errors' => $errors]);
    }

    // Inscription
    public function register()
    {
        $errors = [];
        $request = new Request();
        
        if ($request->isPost()) {
            $nom = $request->post('nom');
            $email = $request->post('email');
            $password = $request->post('password');
            $passwordConfirm = $request->post('password_confirm');
            
            // Validation simple
            if (empty($nom)) {
                $errors[] = "Le nom est obligatoire.";
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }
            
            if (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            
            if ($password !== $passwordConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
            
            if (User::findByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            // Créer l'utilisateur
            if (empty($errors)) {
                $userModel = new User();
                $userModel->createUser([
                    'prenom' => $nom,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role_id' => 1
                ]);
                
                $this->redirect('/login?registered=1');
            }
        }
        
        $this->render('auth/register', ['errors' => $errors]);
    }

    // Déconnexion
    public function logout()
    {
        Session::destroy();
        $this->redirect('/');
    }

    /**
     * Affiche le formulaire de demande de réinitialisation de mot de passe
     */
    public function forgotPassword()
    {
        $errors = [];
        $success = '';
        $request = new Request();
        
        if ($request->isPost()) {
            $email = $request->post('email');
            
            // Validation
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Veuillez saisir une adresse email valide.";
            } else {
                // Vérifier si l'email existe
                $user = User::findByEmail($email);
                
                if ($user) {
                    // Générer un token unique
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Stocker le token en BDD
                    $db = \App\Core\Database::getInstance();
                    $stmt = $db->prepare("
                        INSERT INTO password_resets (email, token, expires_at)
                        VALUES (:email, :token, :expires_at)
                    ");
                    $stmt->execute([
                        'email' => $email,
                        'token' => $token,
                        'expires_at' => $expiresAt
                    ]);
                    
                    // Envoyer l'email avec le lien de réinitialisation
                    require_once __DIR__ . '/../config/mail.php';
                    $resetLink = "http://localhost:8080/reset-password?token=" . $token;
                    
                    if (sendPasswordResetEmail($email, $user['prenom'], $resetLink)) {
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

    /**
     * Traite la réinitialisation du mot de passe avec le token
     */
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
        
        // Vérifier la validité du token
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM password_resets 
            WHERE token = :token 
            AND used = FALSE 
            AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        $resetRequest = $stmt->fetch();
        
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
                // Mettre à jour le mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("
                    UPDATE utilisateur 
                    SET password = :password 
                    WHERE email = :email
                ");
                $stmt->execute([
                    'password' => $hashedPassword,
                    'email' => $resetRequest['email']
                ]);
                
                // Marquer le token comme utilisé
                $stmt = $db->prepare("
                    UPDATE password_resets 
                    SET used = TRUE 
                    WHERE token = :token
                ");
                $stmt->execute(['token' => $token]);
                
                // Logger dans MongoDB
                $user = User::findByEmail($resetRequest['email']);
                if ($user) {
                    MongoLogger::logUserActivity('password_reset', $user['utilisateur_id'], [
                        'email' => $user['email']
                    ]);
                }
                
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
