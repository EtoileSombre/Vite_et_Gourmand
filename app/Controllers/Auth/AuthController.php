<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\EmailSecurity;
use App\Core\Request;
use App\Core\Session;
use App\Factory\ServiceFactory;
use App\Services\AuthService;
use App\Services\Exceptions\AuthException;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = ServiceFactory::getInstance()->createAuthService();
    }

    public function login()
    {
        $request = new Request();

        if ($request->isPost()) {
            if (!csrf_verify()) {
                $this->render('auth/login', ['errors' => ["Erreur de sécurité. Veuillez réessayer."]]);
                return;
            }

            $email = (string) $request->post('email');
            $password = (string) $request->post('password');
            $redirect = $request->post('redirect') ?: $request->get('redirect');
            $clientIp = EmailSecurity::getClientIp();

            try {
                $user = $this->authService->authenticate($email, $password, $clientIp);
            } catch (AuthException $e) {
                $this->render('auth/login', [
                    'errors' => [$e->getMessage()],
                    'redirect' => $request->get('redirect'),
                ]);
                return;
            }

            // Régénérer l'ID de session pour éviter la fixation de session
            session_regenerate_id(true);
            Session::set('user_id', $user->getUtilisateurId());
            Session::set('user_prenom', $user->getPrenom());
            Session::set('user_email', $user->getEmail());
            Session::set('user_role', $user->getRoleLibelle());

            // Redirection (protection open redirect : doit commencer par / mais pas //)
            if (!empty($redirect) && strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
                $this->redirect($redirect);
            } elseif ($user->getRoleLibelle() === 'administrateur') {
                $this->redirect('/admin');
            } elseif ($user->getRoleLibelle() === 'employé') {
                $this->redirect('/employe');
            } else {
                $this->redirect('/');
            }
            return;
        }

        $this->render('auth/login', [
            'errors' => [],
            'redirect' => $request->get('redirect'),
        ]);
    }

    public function register()
    {
        $request = new Request();

        if ($request->isPost()) {
            if (!csrf_verify()) {
                $this->render('auth/register', ['errors' => ["Erreur de sécurité. Veuillez réessayer."]]);
                return;
            }

            $redirect = $request->post('redirect') ?: $request->get('redirect');

            try {
                $result = $this->authService->register([
                    'nom'              => $request->post('nom'),
                    'prenom'           => $request->post('prenom'),
                    'email'            => $request->post('email'),
                    'telephone'        => $request->post('telephone'),
                    'adresse_postale'  => $request->post('adresse_postale'),
                    'code_postal'      => $request->post('code_postal'),
                    'ville'            => $request->post('ville'),
                    'password'         => $request->post('password'),
                    'password_confirm' => $request->post('password_confirm'),
                ]);
            } catch (AuthException $e) {
                $this->render('auth/register', [
                    'errors' => [$e->getMessage()],
                    'redirect' => $request->get('redirect'),
                ]);
                return;
            }

            // Connexion automatique après inscription
            Session::set('user_id', $result['user_id']);
            Session::set('user_prenom', $result['prenom']);
            Session::set('user_email', $result['email']);
            Session::set('user_role', $result['role']);
            Session::set('flash_success', "Bienvenue {$result['prenom']} ! Votre compte a été créé avec succès.");

            if (!empty($redirect) && strpos($redirect, '/') === 0) {
                $this->redirect($redirect);
            } else {
                $this->redirect('/');
            }
            return;
        }

        $this->render('auth/register', [
            'errors' => [],
            'redirect' => $request->get('redirect'),
        ]);
    }

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
            if (!csrf_verify()) {
                $this->render('auth/forgot-password', ['errors' => ["Erreur de sécurité. Veuillez réessayer."]]);
                return;
            }

            $email = (string) $request->post('email');
            $baseUrl = getenv('APP_URL') ?: 'http://localhost:8082';

            try {
                $this->authService->requestPasswordReset($email, $baseUrl);
                // Message neutre : ne révèle pas si l'email existe
                $success = "Si cette adresse email existe, vous recevrez un lien de réinitialisation.";
            } catch (AuthException $e) {
                $errors[] = $e->getMessage();
            }
        }

        $this->render('auth/forgot-password', [
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function resetPassword()
    {
        $errors = [];
        $success = '';
        $request = new Request();
        $token = (string) $request->get('token');

        if ($token === '') {
            $this->redirect('/forgot-password');
            return;
        }

        try {
            $this->authService->validateResetToken($token);
        } catch (AuthException $e) {
            $this->render('auth/reset-password', [
                'errors' => [$e->getMessage()],
                'tokenValid' => false,
            ]);
            return;
        }

        if ($request->isPost()) {
            if (!csrf_verify()) {
                $this->render('auth/reset-password', [
                    'errors' => ["Erreur de sécurité. Veuillez réessayer."],
                    'token' => $token,
                    'tokenValid' => true,
                ]);
                return;
            }

            try {
                $this->authService->resetPassword(
                    $token,
                    (string) $request->post('password'),
                    (string) $request->post('password_confirm')
                );
                $success = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            } catch (AuthException $e) {
                $errors[] = $e->getMessage();
            }
        }

        $this->render('auth/reset-password', [
            'errors' => $errors,
            'success' => $success,
            'token' => $token,
            'tokenValid' => true,
        ]);
    }
}
