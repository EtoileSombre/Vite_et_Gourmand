<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Core\Request;
use App\Core\Session;

class AuthController extends Controller
{
    public function login()
    {
        $errors = [];
        $request = new Request();
        
        if ($request->isPost()) {
            $email = $request->post('email');
            $password = $request->post('password');
            $redirect = $request->post('redirect') ?: $request->get('redirect');
            
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
                    
                    // Gestion de la redirection
                    if (!empty($redirect) && strpos($redirect, '/') === 0) {
                        // Redirection vers la page demandée (sécurisé : doit commencer par /)
                        $this->redirect($redirect);
                    } elseif ($user['role'] === 'administrateur') {
                        $this->redirect('/admin');
                    } elseif ($user['role'] === 'employé') {
                        $this->redirect('/employe');
                    } else {
                        $this->redirect('/');
                    }
                } else {
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
            
            if (User::findByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            // Créer l'utilisateur
            if (empty($errors)) {
                $userModel = new User();
                $userId = $userModel->createUser([
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
        Session::destroy();
        $this->redirect('/');
    }

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
                    
                    // Stocker le token via le modèle PasswordReset
                    $passwordResetModel = new PasswordReset();
                    $passwordResetModel->createToken($email, $token, $expiresAt);
                    
                    // Envoyer l'email avec le lien de réinitialisation
                    require_once __DIR__ . '/../../config/mail.php';
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
        
        // Vérifier la validité du token via le modèle
        $passwordResetModel = new PasswordReset();
        $resetRequest = $passwordResetModel->findValidToken($token);
        
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
                // Mettre à jour le mot de passe via le modèle User
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user = User::findByEmail($resetRequest['email']);
                
                if ($user) {
                    $userModel = new User();
                    $userModel->update($user['utilisateur_id'], ['password' => $hashedPassword]);
                }
                
                // Marquer le token comme utilisé
                $passwordResetModel->markTokenAsUsed($token);
                
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
