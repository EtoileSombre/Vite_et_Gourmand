<?php

namespace App\Controllers\Utilisateur;

use App\Core\Controller;
use App\Models\User;
use App\Core\Request;
use App\Core\Session;

class ProfilController extends Controller
{
    // Profil utilisateur
    public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $errors = [];
        $success = false;
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

            // Validation
            if (empty($nom)) {
                $errors[] = "Le nom est obligatoire.";
            }
            
            if (empty($prenom)) {
                $errors[] = "Le prénom est obligatoire.";
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }

            $userModel = new User();
            $existingUser = $userModel->findByEmail($email);
            
            if ($existingUser && $existingUser['utilisateur_id'] != $userId) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            if (empty($telephone)) {
                $errors[] = "Le numéro de téléphone est obligatoire.";
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

            if (!empty($password)) {
                if (strlen($password) < 10) {
                    $errors[] = "Le mot de passe doit contenir au moins 10 caractères.";
                }
                if ($password !== $passwordConfirm) {
                    $errors[] = "Les mots de passe ne correspondent pas.";
                }
            }

            // Mise à jour
            if (empty($errors)) {
                $userModel = new User();
                $updateData = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'adresse_postale' => $adresse_postale,
                    'code_postal' => $code_postal,
                    'ville' => $ville
                ];

                if (!empty($password)) {
                    $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                $userModel->update($userId, $updateData);

                Session::set('user_email', $email);
                Session::set('user_prenom', $prenom);

                $success = true;
            }
        }

        $userModel = new User();
        $userData = $userModel->findById($userId);

        $this->render('utilisateur/profil/index', [
            'user' => $userData,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
