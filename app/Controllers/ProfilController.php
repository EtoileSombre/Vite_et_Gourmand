<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Request;
use App\Core\Session;

class ProfilController extends Controller
{
    // Profil utilisateur
    public function index()
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $errors = [];
        $success = false;
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

            $existingUser = User::findByEmail($email);
            if ($existingUser && $existingUser['id'] != $user['id']) {
                $errors[] = "Cet email est déjà utilisé.";
            }

            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
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
                    'email' => $email
                ];

                if (!empty($password)) {
                    $updateData['mot_de_passe'] = password_hash($password, PASSWORD_DEFAULT);
                }

                $userModel->update($user['id'], $updateData);

                Session::set('user', [
                    'id' => $user['id'],
                    'nom' => $nom,
                    'email' => $email,
                    'role' => $user['role']
                ]);

                $success = true;
            }
        }

        $userModel = new User();
        $userData = $userModel->findById($user['id']);

        $this->render('profil/index', [
            'user' => $userData,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
