<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier que l'utilisateur est connecté
requireLogin('/commander.php');

$currentUser = getCurrentUser();
$errors = [];
$success = false;

// Récupérer le menu pré-sélectionné si présent
$menuIdPreselected = $_GET['menu_id'] ?? null;
$menuPreselected = null;

if ($menuIdPreselected) {
    try {
        $stmt = $pdo->prepare("
            SELECT m.menu_id, m.titre, m.prix_par_personne, m.nombre_personne_minimum, m.description
            FROM menu m
            WHERE m.menu_id = ?
        ");
        $stmt->execute([$menuIdPreselected]);
        $menuPreselected = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur récupération menu présélectionné: " . $e->getMessage());
    }
}

// Récupérer tous les menus pour le select
try {
    $stmtMenus = $pdo->query("SELECT menu_id, titre, prix_par_personne, nombre_personne_minimum FROM menu ORDER BY titre");
    $menus = $stmtMenus->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur récupération menus: " . $e->getMessage());
    $menus = [];
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse_livraison = trim($_POST['adresse_livraison'] ?? '');
    $ville_livraison = trim($_POST['ville_livraison'] ?? '');
    $date_prestation = $_POST['date_prestation'] ?? '';
    $heure_livraison = $_POST['heure_livraison'] ?? '';
    $menu_id = $_POST['menu_id'] ?? null;
    $nombre_personne = intval($_POST['nombre_personne'] ?? 0);
    
    // Validations
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone)) {
        $errors[] = "Tous les champs personnels sont obligatoires.";
    }
    
    if (empty($adresse_livraison) || empty($ville_livraison)) {
        $errors[] = "L'adresse et la ville de livraison sont obligatoires.";
    }
    
    if (empty($date_prestation) || empty($heure_livraison)) {
        $errors[] = "La date et l'heure de prestation sont obligatoires.";
    }
    
    if (empty($menu_id)) {
        $errors[] = "Veuillez sélectionner un menu.";
    }
    
    if ($nombre_personne < 1) {
        $errors[] = "Le nombre de personnes doit être au moins 1.";
    }
    
    // Récupérer les infos du menu
    $menu = null;
    if ($menu_id) {
        try {
            $stmt = $pdo->prepare("SELECT menu_id, titre, prix_par_personne, nombre_personne_minimum FROM menu WHERE menu_id = ?");
            $stmt->execute([$menu_id]);
            $menu = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$menu) {
                $errors[] = "Menu introuvable.";
            } elseif ($nombre_personne < $menu['nombre_personne_minimum']) {
                $errors[] = "Le nombre de personnes minimum pour ce menu est " . $menu['nombre_personne_minimum'] . ".";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la récupération du menu.";
            error_log("Erreur menu: " . $e->getMessage());
        }
    }
    
    // Si pas d'erreurs, calculer et enregistrer la commande
    if (empty($errors) && $menu) {
        // Calculs
        $prix_menu = $menu['prix_par_personne'] * $nombre_personne;
        
        // Calcul réduction (-10% si +5 personnes par rapport au minimum)
        $reduction_appliquee = 0;
        if ($nombre_personne >= ($menu['nombre_personne_minimum'] + 5)) {
            $reduction_appliquee = $prix_menu * 0.10;
            $prix_menu -= $reduction_appliquee;
        }
        
        // Calcul frais de livraison (5€ + 0.59€/km si hors Bordeaux)
        $frais_livraison = 0;
        $distance_km = 0;
        $ville_livraison_lower = strtolower(trim($ville_livraison));
        
        if ($ville_livraison_lower !== 'bordeaux') {
            // Pour la démo, on simule une distance aléatoire entre 10 et 50 km
            // Dans une vraie app, on utiliserait une API de géolocalisation
            $distance_km = rand(10, 50);
            $frais_livraison = 5 + ($distance_km * 0.59);
        }
        
        $prix_total = $prix_menu + $frais_livraison;
        
        // Générer un numéro de commande unique (format: CMD-YYYYMMDD-XXXXX)
        $numero_commande = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        
        // Vérifier l'unicité du numéro (au cas où)
        try {
            $stmtCheck = $pdo->prepare("SELECT numero_commande FROM commande WHERE numero_commande = ?");
            $stmtCheck->execute([$numero_commande]);
            if ($stmtCheck->fetch()) {
                // Si déjà existant, en générer un nouveau
                $numero_commande = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            }
        } catch (PDOException $e) {
            error_log("Erreur vérification numéro: " . $e->getMessage());
        }
        
        // Insérer la commande
        try {
            $stmt = $pdo->prepare("
                INSERT INTO commande (
                    numero_commande,
                    date_commande,
                    date_prestation,
                    heure_livraison,
                    prix_menu,
                    nombre_personne,
                    lieu_livraison,
                    adresse_livraison,
                    distance_km,
                    frais_livraison,
                    reduction_appliquee,
                    prix_total,
                    statut,
                    utilisateur_id,
                    menu_id
                ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en attente', ?, ?)
            ");
            
            $stmt->execute([
                $numero_commande,
                $date_prestation,
                $heure_livraison,
                $prix_menu,
                $nombre_personne,
                $ville_livraison,
                $adresse_livraison,
                $distance_km,
                $frais_livraison,
                $reduction_appliquee,
                $prix_total,
                $currentUser['utilisateur_id'],
                $menu_id
            ]);
            
            // TODO: Envoyer email de confirmation
            // sendOrderConfirmationEmail($email, $prenom, $numero_commande, $prix_total);
            
            $success = true;
            setFlashMessage('success', "Commande #{$numero_commande} enregistrée avec succès ! Montant total : " . number_format($prix_total, 2, ',', ' ') . " €");
            header('Location: /mes-commandes.php');
            exit;
            
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement de la commande.";
            error_log("Erreur insertion commande: " . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="flex-grow-1 bg-light">
    <div class="container py-5">
        <h1 class="mb-4">
            <i class="bi bi-cart-plus"></i> Commander un menu
        </h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulaire -->
            <div class="col-lg-8">
                <form method="POST" action="/commander.php" id="orderForm">
                    <!-- Informations client -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-person"></i> Vos informations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="prenom" 
                                        name="prenom" 
                                        value="<?= htmlspecialchars($currentUser['prenom'] ?? '') ?>"
                                        required
                                    >
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="nom" 
                                        name="nom" 
                                        value="<?= htmlspecialchars($currentUser['nom'] ?? '') ?>"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        id="email" 
                                        name="email" 
                                        value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>"
                                        required
                                    >
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input 
                                        type="tel" 
                                        class="form-control" 
                                        id="telephone" 
                                        name="telephone" 
                                        value="<?= htmlspecialchars($currentUser['telephone'] ?? '') ?>"
                                        pattern="[0-9]{10}"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Détails de la prestation -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Détails de la prestation</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="adresse_livraison" class="form-label">Adresse de livraison <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="adresse_livraison" 
                                    name="adresse_livraison" 
                                    placeholder="12 rue de la République"
                                    required
                                >
                            </div>
                            <div class="mb-3">
                                <label for="ville_livraison" class="form-label">Ville <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="ville_livraison" 
                                    name="ville_livraison" 
                                    placeholder="Bordeaux"
                                    required
                                >
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> 
                                    Livraison gratuite à Bordeaux. Hors Bordeaux : 5€ + 0,59€/km
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_prestation" class="form-label">Date de la prestation <span class="text-danger">*</span></label>
                                    <input 
                                        type="date" 
                                        class="form-control" 
                                        id="date_prestation" 
                                        name="date_prestation" 
                                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                        required
                                    >
                                    <div class="form-text">Commande 24h à l'avance minimum</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="heure_livraison" class="form-label">Heure souhaitée <span class="text-danger">*</span></label>
                                    <input 
                                        type="time" 
                                        class="form-control" 
                                        id="heure_livraison" 
                                        name="heure_livraison" 
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Choix du menu -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-menu-button-wide"></i> Choix du menu</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                                <select class="form-select" id="menu_id" name="menu_id" required>
                                    <option value="">Sélectionnez un menu</option>
                                    <?php foreach ($menus as $menu): ?>
                                        <option 
                                            value="<?= $menu['menu_id'] ?>"
                                            data-prix="<?= $menu['prix_par_personne'] ?>"
                                            data-min="<?= $menu['nombre_personne_minimum'] ?>"
                                            <?= $menuPreselected && $menuPreselected['menu_id'] == $menu['menu_id'] ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($menu['titre']) ?> 
                                            - <?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?>€/pers 
                                            (min. <?= $menu['nombre_personne_minimum'] ?> pers.)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_personne" class="form-label">Nombre de personnes <span class="text-danger">*</span></label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="nombre_personne" 
                                    name="nombre_personne" 
                                    min="1"
                                    value="<?= $menuPreselected ? $menuPreselected['nombre_personne_minimum'] : 1 ?>"
                                    required
                                >
                                <div class="form-text d-none" id="reductionInfo">
                                    <i class="bi bi-tag-fill text-success"></i> 
                                    <strong>Réduction de 10% appliquée !</strong> (5 personnes ou plus au-dessus du minimum)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Valider la commande
                        </button>
                        <a href="/menus.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Retour aux menus
                        </a>
                    </div>
                </form>
            </div>

            <!-- Résumé -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-sidebar">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Récapitulatif</h5>
                    </div>
                    <div class="card-body">
                        <div id="summary">
                            <p class="text-muted text-center">
                                Sélectionnez un menu et le nombre de personnes pour voir le calcul du prix
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
