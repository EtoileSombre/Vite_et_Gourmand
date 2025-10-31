<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Request;
use App\Core\Session;

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
}
