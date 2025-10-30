<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirectByRole();
    exit;
}

$errors = [];
$success = false;
$formData = [
    'prenom' => '',
    'nom' => '',
    'email' => '',
    'telephone' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Sauvegarder pour réafficher en cas d'erreur
    $formData = [
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone
    ];
    
    // Validations
    if (empty($prenom)) {
        $errors[] = "Le prénom est obligatoire.";
    }
    
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    
    if (empty($telephone)) {
        $errors[] = "Le téléphone est obligatoire.";
    } elseif (!preg_match('/^[0-9]{10}$/', $telephone)) {
        $errors[] = "Le téléphone doit contenir 10 chiffres.";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est obligatoire.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    
    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la vérification de l'email.";
            error_log("Erreur register.php - vérification email: " . $e->getMessage());
        }
    }
    
    // Si pas d'erreurs, créer l'utilisateur
    if (empty($errors)) {
        try {
            // Récupérer l'ID du rôle 'client'
            $stmt = $pdo->prepare("SELECT role_id FROM role WHERE libelle = 'client' LIMIT 1");
            $stmt->execute();
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$role) {
                $errors[] = "Erreur système: rôle client introuvable.";
            } else {
                // Hasher le mot de passe
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insérer l'utilisateur
                $stmt = $pdo->prepare("
                    INSERT INTO utilisateur (prenom, nom, email, telephone, password, role_id, actif)
                    VALUES (?, ?, ?, ?, ?, ?, 1)
                ");
                
                $stmt->execute([
                    $prenom,
                    $nom,
                    $email,
                    $telephone,
                    $passwordHash,
                    $role['role_id']
                ]);
                
                $success = true;
                
                // Auto-connexion après inscription
                $userId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("
                    SELECT u.*, r.libelle as role_libelle 
                    FROM utilisateur u
                    INNER JOIN role r ON u.role_id = r.role_id
                    WHERE u.utilisateur_id = ?
                ");
                $stmt->execute([$userId]);
                $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($newUser) {
                    login($newUser);
                    setFlashMessage('success', 'Bienvenue ' . htmlspecialchars($prenom) . ' ! Votre compte a été créé avec succès.');
                    header('Location: /index.php');
                    exit;
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la création du compte. Veuillez réessayer.";
            error_log("Erreur register.php - insertion: " . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-person-plus"></i> Créer un compte
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            Votre compte a été créé avec succès ! Vous allez être redirigé...
                        </div>
                    <?php else: ?>
                        <form method="POST" action="/register.php">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="prenom" 
                                        name="prenom" 
                                        value="<?= htmlspecialchars($formData['prenom']) ?>"
                                        required
                                        maxlength="50"
                                    >
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="nom" 
                                        name="nom" 
                                        value="<?= htmlspecialchars($formData['nom']) ?>"
                                        required
                                        maxlength="50"
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    value="<?= htmlspecialchars($formData['email']) ?>"
                                    required
                                    maxlength="100"
                                >
                                <div class="form-text">Cet email sera utilisé pour vous connecter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="telephone" 
                                    name="telephone" 
                                    value="<?= htmlspecialchars($formData['telephone']) ?>"
                                    required
                                    pattern="[0-9]{10}"
                                    maxlength="10"
                                    placeholder="0612345678"
                                >
                                <div class="form-text">10 chiffres sans espaces.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    required
                                    minlength="8"
                                >
                                <div class="form-text">Minimum 8 caractères.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    required
                                    minlength="8"
                                >
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-person-check"></i> Créer mon compte
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">Vous avez déjà un compte ?</p>
                            <a href="/login.php" class="btn btn-outline-primary mt-2">
                                <i class="bi bi-box-arrow-in-right"></i> Se connecter
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
