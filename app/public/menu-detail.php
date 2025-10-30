<?php
require_once __DIR__ . '/../config/db.php';

// Récupérer l'ID du menu
$menuId = $_GET['id'] ?? 0;

if (empty($menuId) || !is_numeric($menuId)) {
    header('Location: /menus.php');
    exit;
}

// Récupérer les détails du menu avec les plats
try {
    $stmt = $pdo->prepare("
        SELECT 
            m.menu_id,
            m.titre,
            m.prix_par_personne,
            m.nombre_personne_minimum,
            m.description,
            m.conditions,
            m.quantite_restante,
            r.libelle as regime_libelle,
            t.libelle as theme_libelle,
            m.created_at
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE m.menu_id = ?
    ");
    $stmt->execute([$menuId]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$menu) {
        header('Location: /menus.php');
        exit;
    }
    
    // Récupérer les plats du menu (si la table existe)
    $plats = [];
    try {
        $stmtPlats = $pdo->prepare("
            SELECT p.nom, p.description, p.type
            FROM plat p
            INNER JOIN composer c ON p.plat_id = c.plat_id
            WHERE c.menu_id = ?
            ORDER BY 
                CASE p.type
                    WHEN 'entree' THEN 1
                    WHEN 'plat' THEN 2
                    WHEN 'dessert' THEN 3
                    ELSE 4
                END
        ");
        $stmtPlats->execute([$menuId]);
        $plats = $stmtPlats->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Table composer ou plat n'existe peut-être pas encore
        error_log("Erreur récupération plats: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    error_log("Erreur menu-detail.php: " . $e->getMessage());
    header('Location: /menus.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="flex-grow-1 bg-light">
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Accueil</a></li>
                <li class="breadcrumb-item"><a href="/menus.php">Menus</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= htmlspecialchars($menu['titre']) ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Détails du menu -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <!-- En-tête -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h2 text-success fw-bold mb-2">
                                    <?= htmlspecialchars($menu['titre']) ?>
                                </h1>
                                <?php if ($menu['theme_libelle']): ?>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-tag"></i> 
                                        <?= htmlspecialchars($menu['theme_libelle']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if ($menu['regime_libelle']): ?>
                                <span class="badge bg-success fs-6 p-2">
                                    <?= htmlspecialchars($menu['regime_libelle']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <!-- Description -->
                        <div class="mb-4">
                            <h5 class="text-success">
                                <i class="bi bi-info-circle"></i> Description
                            </h5>
                            <p class="text-muted">
                                <?= htmlspecialchars($menu['description'] ?: 'Aucune description disponible.') ?>
                            </p>
                        </div>

                        <!-- Composition du menu -->
                        <?php if (!empty($plats)): ?>
                            <div class="mb-4">
                                <h5 class="text-success">
                                    <i class="bi bi-card-checklist"></i> Composition
                                </h5>
                                <div class="list-group">
                                    <?php foreach ($plats as $plat): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    <span class="badge bg-light text-dark me-2">
                                                        <?= ucfirst($plat['type']) ?>
                                                    </span>
                                                    <?= htmlspecialchars($plat['nom']) ?>
                                                </h6>
                                            </div>
                                            <?php if ($plat['description']): ?>
                                                <p class="mb-0 small text-muted">
                                                    <?= htmlspecialchars($plat['description']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Conditions -->
                        <?php if ($menu['conditions']): ?>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="bi bi-exclamation-triangle"></i> Conditions
                                </h6>
                                <p class="mb-0"><?= htmlspecialchars($menu['conditions']) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Informations complémentaires -->
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people text-success fs-3"></i>
                                        <h6 class="mt-2 mb-0">Nombre de personnes</h6>
                                        <p class="text-muted mb-0">
                                            Minimum <?= $menu['nombre_personne_minimum'] ?> personne<?= $menu['nombre_personne_minimum'] > 1 ? 's' : '' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-box-seam text-success fs-3"></i>
                                        <h6 class="mt-2 mb-0">Disponibilité</h6>
                                        <p class="text-muted mb-0">
                                            <?= $menu['quantite_restante'] > 0 ? 
                                                $menu['quantite_restante'] . ' restant(s)' : 
                                                'Sur commande' 
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Commande -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title text-success mb-3">Commander ce menu</h5>
                        
                        <!-- Prix -->
                        <div class="text-center mb-4">
                            <div class="display-4 text-success fw-bold">
                                <?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €
                            </div>
                            <small class="text-muted">par personne</small>
                        </div>

                        <div class="d-grid gap-2">
                            <a 
                                href="/commander.php?menu_id=<?= $menu['menu_id'] ?>" 
                                class="btn btn-success btn-lg"
                            >
                                <i class="bi bi-cart-plus"></i> Commander maintenant
                            </a>
                            <a href="/menus.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Retour aux menus
                            </a>
                        </div>

                        <hr class="my-4">

                        <!-- Avantages -->
                        <div class="small">
                            <div class="d-flex mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Ingrédients frais et locaux</span>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Préparation le jour même</span>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Livraison ou retrait possible</span>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Commande 24h à l'avance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
