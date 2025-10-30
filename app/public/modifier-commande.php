<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Vérifier l'authentification
requireLogin('/modifier-commande.php');
$currentUser = getCurrentUser();

// Récupérer l'ID de la commande
$commandeId = $_GET['id'] ?? null;

if (!$commandeId) {
    $_SESSION['flash_error'] = "Commande introuvable.";
    header('Location: /mes-commandes.php');
    exit;
}

try {
    // Récupérer les détails de la commande
    $stmt = $pdo->prepare("
        SELECT c.*, m.titre as menu_titre, m.prix_par_personne, 
               m.nombre_personne_minimum, m.nombre_personne_maximum
        FROM commande c
        LEFT JOIN menu m ON c.menu_id = m.menu_id
        WHERE c.numero_commande = ? AND c.utilisateur_id = ?
    ");
    $stmt->execute([$commandeId, $currentUser['utilisateur_id']]);
    $commande = $stmt->fetch();

    if (!$commande) {
        $_SESSION['flash_error'] = "Commande introuvable ou accès refusé.";
        header('Location: /mes-commandes.php');
        exit;
    }

    // Vérifier que la commande peut être modifiée (statut 'en attente' uniquement)
    if ($commande['statut'] !== 'en attente') {
        $_SESSION['flash_error'] = "Cette commande ne peut plus être modifiée.";
        header('Location: /commande-detail.php?id=' . urlencode($commandeId));
        exit;
    }

    // Récupérer tous les menus disponibles
    $stmtMenus = $pdo->query("SELECT * FROM menu WHERE stock_disponible = 1 ORDER BY titre");
    $menus = $stmtMenus->fetchAll();

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération de la commande : " . $e->getMessage());
    $_SESSION['flash_error'] = "Erreur lors de la récupération de la commande.";
    header('Location: /mes-commandes.php');
    exit;
}

// Traitement du formulaire
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérer et valider les données
        $menu_id = (int)$_POST['menu_id'];
        $nombre_personne = (int)$_POST['nombre_personne'];
        $date_prestation = $_POST['date_prestation'];
        $heure_livraison = $_POST['heure_livraison'];
        $prenom_livraison = trim($_POST['prenom_livraison']);
        $nom_livraison = trim($_POST['nom_livraison']);
        $adresse_livraison = trim($_POST['adresse_livraison']);
        $code_postal_livraison = trim($_POST['code_postal_livraison']);
        $ville_livraison = trim($_POST['ville_livraison']);
        $telephone_livraison = trim($_POST['telephone_livraison']);
        $commentaire = trim($_POST['commentaire'] ?? '');

        // Validation
        if (empty($menu_id)) {
            $errors[] = "Veuillez sélectionner un menu.";
        }
        if ($nombre_personne < 1) {
            $errors[] = "Le nombre de personnes doit être d'au moins 1.";
        }
        if (empty($date_prestation) || strtotime($date_prestation) < strtotime('tomorrow')) {
            $errors[] = "La date de prestation doit être au moins 24h à l'avance.";
        }
        if (empty($heure_livraison)) {
            $errors[] = "Veuillez indiquer l'heure de livraison souhaitée.";
        }
        if (empty($prenom_livraison) || empty($nom_livraison)) {
            $errors[] = "Les nom et prénom de livraison sont obligatoires.";
        }
        if (empty($adresse_livraison)) {
            $errors[] = "L'adresse de livraison est obligatoire.";
        }
        if (empty($code_postal_livraison) || !preg_match('/^[0-9]{5}$/', $code_postal_livraison)) {
            $errors[] = "Le code postal doit contenir 5 chiffres.";
        }
        if (empty($ville_livraison)) {
            $errors[] = "La ville de livraison est obligatoire.";
        }
        if (empty($telephone_livraison) || !preg_match('/^[0-9]{10}$/', $telephone_livraison)) {
            $errors[] = "Le téléphone doit contenir 10 chiffres.";
        }

        if (empty($errors)) {
            // Récupérer les informations du menu sélectionné
            $stmtMenu = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
            $stmtMenu->execute([$menu_id]);
            $menu = $stmtMenu->fetch();

            if (!$menu) {
                $errors[] = "Menu introuvable.";
            } else {
                // Vérifier que le nombre de personnes est valide
                if ($nombre_personne < $menu['nombre_personne_minimum']) {
                    $errors[] = "Le nombre minimum de personnes pour ce menu est de " . $menu['nombre_personne_minimum'] . ".";
                } elseif ($nombre_personne > $menu['nombre_personne_maximum']) {
                    $errors[] = "Le nombre maximum de personnes pour ce menu est de " . $menu['nombre_personne_maximum'] . ".";
                }

                if (empty($errors)) {
                    // Calculer les prix
                    $prix_menu = $menu['prix_par_personne'] * $nombre_personne;
                    $reduction_appliquee = 0;

                    // Appliquer la réduction de 10% si +5 personnes au-delà du minimum
                    if ($nombre_personne >= ($menu['nombre_personne_minimum'] + 5)) {
                        $reduction_appliquee = $prix_menu * 0.10;
                        $prix_menu -= $reduction_appliquee;
                    }

                    // Calculer les frais de livraison
                    $frais_livraison = 0;
                    $distance_km = 0;
                    $ville_livraison_lower = strtolower($ville_livraison);

                    if ($ville_livraison_lower !== 'bordeaux') {
                        // Simuler une distance (en production, utiliser une API de géolocalisation)
                        $distance_km = rand(10, 50);
                        $frais_livraison = 5 + ($distance_km * 0.59);
                    }

                    $prix_total = $prix_menu + $frais_livraison;

                    // Mettre à jour la commande
                    $stmtUpdate = $pdo->prepare("
                        UPDATE commande SET
                            menu_id = ?,
                            nombre_personne = ?,
                            date_prestation = ?,
                            heure_livraison = ?,
                            prenom_livraison = ?,
                            nom_livraison = ?,
                            adresse_livraison = ?,
                            code_postal_livraison = ?,
                            ville_livraison = ?,
                            telephone_livraison = ?,
                            commentaire = ?,
                            prix_menu = ?,
                            reduction_appliquee = ?,
                            frais_livraison = ?,
                            distance_livraison_km = ?,
                            prix_total = ?,
                            updated_at = NOW()
                        WHERE numero_commande = ? AND utilisateur_id = ?
                    ");

                    $stmtUpdate->execute([
                        $menu_id,
                        $nombre_personne,
                        $date_prestation,
                        $heure_livraison,
                        $prenom_livraison,
                        $nom_livraison,
                        $adresse_livraison,
                        $code_postal_livraison,
                        $ville_livraison,
                        $telephone_livraison,
                        $commentaire,
                        $prix_menu,
                        $reduction_appliquee,
                        $frais_livraison,
                        $distance_km > 0 ? $distance_km : null,
                        $prix_total,
                        $commandeId,
                        $currentUser['utilisateur_id']
                    ]);

                    // Enregistrer dans l'historique de suivi
                    $stmtSuivi = $pdo->prepare("
                        INSERT INTO suivi_commande (numero_commande, statut, commentaire, date_modification)
                        VALUES (?, 'en attente', 'Commande modifiée par le client', NOW())
                    ");
                    $stmtSuivi->execute([$commandeId]);

                    $_SESSION['flash_success'] = "Votre commande a été modifiée avec succès.";
                    header('Location: /commande-detail.php?id=' . urlencode($commandeId));
                    exit;
                }
            }
        }

    } catch (PDOException $e) {
        error_log("Erreur lors de la modification de la commande : " . $e->getMessage());
        $errors[] = "Une erreur est survenue lors de la modification de la commande.";
    }
}

$pageTitle = "Modifier la commande #" . htmlspecialchars($commande['numero_commande']);
include '../includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/mes-commandes.php">Mes commandes</a></li>
            <li class="breadcrumb-item"><a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>">#<?= htmlspecialchars($commande['numero_commande']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-pencil"></i> Modifier la commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
                </div>
                <div class="card-body">
                    <!-- Messages d'erreur -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Attention :</strong> Vous pouvez modifier votre commande tant qu'elle est en attente de validation.
                    </div>

                    <form method="POST" action="">
                        <!-- Menu -->
                        <div class="mb-3">
                            <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                            <select class="form-select" id="menu_id" name="menu_id" required>
                                <option value="">Sélectionnez un menu</option>
                                <?php foreach ($menus as $menu): ?>
                                    <option value="<?= $menu['menu_id'] ?>" 
                                            data-prix="<?= $menu['prix_par_personne'] ?>"
                                            data-min="<?= $menu['nombre_personne_minimum'] ?>"
                                            data-max="<?= $menu['nombre_personne_maximum'] ?>"
                                            <?= $menu['menu_id'] == $commande['menu_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($menu['titre']) ?> - <?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €/pers.
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Nombre de personnes -->
                        <div class="mb-3">
                            <label for="nombre_personne" class="form-label">Nombre de personnes <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="nombre_personne" name="nombre_personne" 
                                   value="<?= htmlspecialchars($commande['nombre_personne']) ?>" 
                                   min="1" required>
                            <small class="form-text text-muted" id="personnes-info"></small>
                        </div>

                        <!-- Date et heure -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_prestation" class="form-label">Date de prestation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_prestation" name="date_prestation" 
                                       value="<?= htmlspecialchars($commande['date_prestation']) ?>"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                <small class="form-text text-muted">Commande minimum 24h à l'avance</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="heure_livraison" class="form-label">Heure de livraison <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="heure_livraison" name="heure_livraison" 
                                       value="<?= htmlspecialchars($commande['heure_livraison']) ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Informations de livraison</h5>

                        <!-- Nom et prénom -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prenom_livraison" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom_livraison" name="prenom_livraison" 
                                       value="<?= htmlspecialchars($commande['prenom_livraison']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom_livraison" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom_livraison" name="nom_livraison" 
                                       value="<?= htmlspecialchars($commande['nom_livraison']) ?>" required>
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="mb-3">
                            <label for="adresse_livraison" class="form-label">Adresse <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adresse_livraison" name="adresse_livraison" 
                                   value="<?= htmlspecialchars($commande['adresse_livraison']) ?>" required>
                        </div>

                        <!-- Code postal et ville -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="code_postal_livraison" class="form-label">Code postal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code_postal_livraison" name="code_postal_livraison" 
                                       value="<?= htmlspecialchars($commande['code_postal_livraison']) ?>"
                                       pattern="[0-9]{5}" maxlength="5" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="ville_livraison" class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ville_livraison" name="ville_livraison" 
                                       value="<?= htmlspecialchars($commande['ville_livraison']) ?>" required>
                                <small class="form-text text-muted">Livraison gratuite à Bordeaux, sinon 5€ + 0,59€/km</small>
                            </div>
                        </div>

                        <!-- Téléphone -->
                        <div class="mb-3">
                            <label for="telephone_livraison" class="form-label">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="telephone_livraison" name="telephone_livraison" 
                                   value="<?= htmlspecialchars($commande['telephone_livraison']) ?>"
                                   pattern="[0-9]{10}" maxlength="10" required>
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire ou instructions particulières</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3"><?= htmlspecialchars($commande['commentaire']) ?></textarea>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Enregistrer les modifications
                            </button>
                            <a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
