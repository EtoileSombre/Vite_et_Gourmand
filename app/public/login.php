<?php
/**
 * Page de connexion
 * Authentification avec email + password
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Si déjà connecté, rediriger selon le rôle
if (isLoggedIn()) {
    redirectByRole();
}

$error = null;
$success = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation basique
    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Récupération de l'utilisateur avec son rôle
            $stmt = $pdo->prepare("
                SELECT u.utilisateur_id, u.email, u.password, u.prenom, u.nom, u.telephone, u.actif, r.libelle as role_libelle
                FROM utilisateur u
                INNER JOIN role r ON u.role_id = r.role_id
                WHERE u.email = :email
                LIMIT 1
            ");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Vérifier si le compte est actif
                if (!$user['actif']) {
                    $error = "Votre compte est désactivé. Contactez l'administrateur.";
                } else {
                    // Connexion réussie
                    login($user);
                    setFlashMessage('success', "Bienvenue, {$user['prenom']} !");
                    redirectByRole();
                }
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            error_log("Erreur login: " . $e->getMessage());
            $error = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
}

// Récupérer les messages flash
$success = getFlashMessage('success');
$error = $error ?? getFlashMessage('error');

include __DIR__ . "/../includes/header.php";
?>

<main class="flex-grow-1">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow">
          <div class="card-body p-4">
            <h1 class="h3 mb-3 text-center">Connexion</h1>
            <p class="text-muted text-center mb-4">
              Accédez à votre espace personnel
            </p>

            <?php if ($success): ?>
              <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success) ?>
              </div>
            <?php endif; ?>

            <?php if ($error): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form method="POST" action="/login.php">
              <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  name="email" 
                  value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                  required 
                  autofocus
                >
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  name="password" 
                  required
                >
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger btn-lg">
                  Se connecter
                </button>
              </div>
            </form>

            <hr class="my-4">

            <div class="text-center">
              <p class="text-muted mb-2">Pas encore de compte ?</p>
              <a href="/register.php" class="btn btn-outline-secondary">
                Créer un compte
              </a>
            </div>

            <!-- Aide pour les tests -->
            <div class="mt-4">
              <details>
                <summary class="text-muted small" style="cursor: pointer;">
                  Comptes de test disponibles
                </summary>
                <div class="mt-2 small">
                  <p class="mb-1"><strong>Client :</strong> client@test.fr / password123</p>
                  <p class="mb-1"><strong>Employé :</strong> employe@viteetgourmand.fr / password123</p>
                  <p class="mb-0"><strong>Admin :</strong> admin@viteetgourmand.fr / password123</p>
                </div>
              </details>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . "/../includes/footer.php"; ?>

