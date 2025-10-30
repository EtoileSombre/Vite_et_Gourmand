<?php
require_once __DIR__ . '/../config/db.php';

// Récupérer les filtres depuis l'URL
$regimeFilter = $_GET['regime'] ?? '';
$searchTerm = $_GET['search'] ?? '';

// Construire la requête SQL avec filtres
$sql = "
    SELECT 
        m.menu_id,
        m.titre,
        m.prix_par_personne,
        m.nombre_personne_minimum,
        m.description,
        m.conditions,
        m.quantite_restante,
        r.libelle as regime_libelle,
        t.libelle as theme_libelle
    FROM menu m
    LEFT JOIN regime r ON m.regime_id = r.regime_id
    LEFT JOIN theme t ON m.theme_id = t.theme_id
    WHERE 1=1
";

$params = [];

// Filtre par régime
if (!empty($regimeFilter)) {
    $sql .= " AND r.libelle = ?";
    $params[] = $regimeFilter;
}

// Filtre par recherche
if (!empty($searchTerm)) {
    $sql .= " AND (m.titre LIKE ? OR m.description LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

$sql .= " ORDER BY m.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur menus.php: " . $e->getMessage());
    $menus = [];
}

// Récupérer tous les régimes pour les filtres
try {
    $stmtRegimes = $pdo->query("SELECT libelle FROM regime ORDER BY libelle");
    $regimes = $stmtRegimes->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Erreur récupération régimes: " . $e->getMessage());
    $regimes = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="flex-grow-1 bg-light">
    <!-- Hero Section -->
    <section class="bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="bi bi-menu-button-wide"></i> Nos Menus du Jour
                    </h1>
                    <p class="lead mb-0">
                        Découvrez notre sélection de menus préparés avec des produits frais et de saison
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="badge bg-light text-success fs-5 p-3">
                        <?= count($menus) ?> menu<?= count($menus) > 1 ? 's' : '' ?> disponible<?= count($menus) > 1 ? 's' : '' ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <!-- Filtres et Recherche -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="/menus.php" class="row g-3">
                    <!-- Recherche -->
                    <div class="col-md-6">
                        <label for="search" class="form-label">
                            <i class="bi bi-search"></i> Rechercher
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="search" 
                            name="search" 
                            placeholder="Titre ou description..."
                            value="<?= htmlspecialchars($searchTerm) ?>"
                        >
                    </div>

                    <!-- Filtre Régime -->
                    <div class="col-md-4">
                        <label for="regime" class="form-label">
                            <i class="bi bi-filter"></i> Régime alimentaire
                        </label>
                        <select class="form-select" id="regime" name="regime">
                            <option value="">Tous les régimes</option>
                            <?php foreach ($regimes as $regime): ?>
                                <option value="<?= htmlspecialchars($regime) ?>" 
                                    <?= $regimeFilter === $regime ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($regime) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-funnel"></i> Filtrer
                            </button>
                            <?php if ($regimeFilter || $searchTerm): ?>
                                <a href="/menus.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Réinitialiser
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Menus -->
        <?php if (empty($menus)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle fs-1"></i>
                <h4 class="mt-3">Aucun menu trouvé</h4>
                <p class="mb-0">Essayez de modifier vos critères de recherche ou de filtre.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($menus as $menu): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-shadow transition">
                            <!-- Badge Régime -->
                            <?php if ($menu['regime_libelle']): ?>
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-success">
                                        <?= htmlspecialchars($menu['regime_libelle']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <!-- Titre -->
                                <h5 class="card-title fw-bold text-success">
                                    <a href="/menu-detail.php?id=<?= $menu['menu_id'] ?>" class="text-decoration-none text-success">
                                        <i class="bi bi-card-checklist"></i>
                                        <?= htmlspecialchars($menu['titre']) ?>
                                    </a>
                                </h5>

                                <!-- Thème -->
                                <?php if ($menu['theme_libelle']): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-tag"></i> 
                                        <?= htmlspecialchars($menu['theme_libelle']) ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Description -->
                                <p class="card-text text-muted flex-grow-1">
                                    <?= htmlspecialchars($menu['description'] ?: 'Aucune description disponible.') ?>
                                </p>

                                <!-- Informations -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">
                                            <i class="bi bi-people"></i> 
                                            Min. <?= $menu['nombre_personne_minimum'] ?> pers.
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <?= $menu['quantite_restante'] > 0 ? 
                                                $menu['quantite_restante'] . ' restant(s)' : 
                                                'Sur commande' 
                                            ?>
                                        </span>
                                    </div>

                                    <?php if ($menu['conditions']): ?>
                                        <div class="alert alert-warning small py-2 mb-2">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <?= htmlspecialchars($menu['conditions']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Prix et Commande -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="h4 text-success fw-bold mb-0">
                                            <?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €
                                        </span>
                                        <small class="text-muted d-block">par personne</small>
                                    </div>
                                    <a 
                                        href="/commander.php?menu_id=<?= $menu['menu_id'] ?>" 
                                        class="btn btn-success"
                                    >
                                        <i class="bi bi-cart-plus"></i> Commander
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Informations complémentaires -->
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-success fs-1 mb-3"></i>
                        <h5 class="card-title">Commande à l'avance</h5>
                        <p class="card-text text-muted">
                            Commandez 24h à l'avance pour garantir la disponibilité
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check text-success fs-1 mb-3"></i>
                        <h5 class="card-title">Ingrédients frais</h5>
                        <p class="card-text text-muted">
                            Produits locaux et de saison sélectionnés avec soin
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-truck text-success fs-1 mb-3"></i>
                        <h5 class="card-title">Livraison ou retrait</h5>
                        <p class="card-text text-muted">
                            Choisissez le mode de récupération qui vous convient
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
