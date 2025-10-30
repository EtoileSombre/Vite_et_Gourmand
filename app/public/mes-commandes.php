<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

// Vérifier que l'utilisateur est connecté
requireLogin('/mes-commandes.php');

$currentUser = getCurrentUser();
$userId = getUserId();

// Récupérer toutes les commandes de l'utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            m.titre as menu_titre,
            m.prix_par_personne
        FROM commande c
        LEFT JOIN menu m ON c.menu_id = m.menu_id
        WHERE c.utilisateur_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$userId]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur récupération commandes: " . $e->getMessage());
    $commandes = [];
}

// Fonction pour obtenir le badge de statut
function getStatutBadge($statut) {
    $badges = [
        'en attente' => 'bg-warning text-dark',
        'accepté' => 'bg-info',
        'en préparation' => 'bg-primary',
        'en cours de livraison' => 'bg-primary',
        'livré' => 'bg-success',
        'en attente du retour de matériel' => 'bg-warning text-dark',
        'terminée' => 'bg-success',
        'annulée' => 'bg-danger'
    ];
    
    $class = $badges[$statut] ?? 'bg-secondary';
    return "<span class='badge {$class}'>" . ucfirst(htmlspecialchars($statut)) . "</span>";
}

// Fonction pour obtenir l'icône de statut
function getStatutIcon($statut) {
    $icons = [
        'en attente' => 'hourglass-split',
        'accepté' => 'check-circle',
        'en préparation' => 'gear',
        'en cours de livraison' => 'truck',
        'livré' => 'check-circle-fill',
        'en attente du retour de matériel' => 'box-arrow-in-left',
        'terminée' => 'check-circle-fill',
        'annulée' => 'x-circle'
    ];
    
    return $icons[$statut] ?? 'circle';
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="flex-grow-1 bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="bi bi-bag-check"></i> Mes commandes
            </h1>
            <a href="/commander.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nouvelle commande
            </a>
        </div>

        <?php
        $flashMessage = getFlashMessage('success');
        if ($flashMessage):
        ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i>
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Aucune commande</h3>
                    <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
                    <a href="/menus.php" class="btn btn-success mt-3">
                        <i class="bi bi-menu-button-wide"></i> Découvrir nos menus
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Liste des commandes -->
            <div class="row g-4">
                <?php foreach ($commandes as $commande): ?>
                    <div class="col-12">
                        <div class="card shadow-sm hover-shadow">
                            <div class="card-header bg-white">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1">
                                            <i class="bi bi-receipt"></i>
                                            Commande #<?= htmlspecialchars($commande['numero_commande']) ?>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> 
                                            Commandé le <?= date('d/m/Y', strtotime($commande['date_commande'])) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                        <?= getStatutBadge($commande['statut']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Détails menu -->
                                    <div class="col-md-6">
                                        <h6 class="text-success">
                                            <i class="bi bi-menu-button-wide"></i> Menu
                                        </h6>
                                        <p class="mb-2">
                                            <strong><?= htmlspecialchars($commande['menu_titre']) ?></strong>
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-people"></i> 
                                            <?= $commande['nombre_personne'] ?> personne<?= $commande['nombre_personne'] > 1 ? 's' : '' ?>
                                        </p>
                                    </div>

                                    <!-- Détails prestation -->
                                    <div class="col-md-6 mt-3 mt-md-0">
                                        <h6 class="text-success">
                                            <i class="bi bi-calendar-event"></i> Prestation
                                        </h6>
                                        <p class="mb-1 small">
                                            <i class="bi bi-calendar3"></i> 
                                            <?= date('d/m/Y', strtotime($commande['date_prestation'])) ?> 
                                            à <?= htmlspecialchars($commande['heure_livraison']) ?>
                                        </p>
                                        <p class="mb-0 small">
                                            <i class="bi bi-geo-alt"></i> 
                                            <?= htmlspecialchars($commande['lieu_livraison']) ?>
                                        </p>
                                        <?php if ($commande['adresse_livraison']): ?>
                                            <p class="mb-0 small text-muted">
                                                <?= htmlspecialchars($commande['adresse_livraison']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <hr>

                                <!-- Prix -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="small">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Prix menu:</span>
                                                <span><?= number_format($commande['prix_menu'], 2, ',', ' ') ?> €</span>
                                            </div>
                                            <?php if ($commande['reduction_appliquee'] > 0): ?>
                                                <div class="d-flex justify-content-between mb-1 text-success">
                                                    <span><i class="bi bi-tag-fill"></i> Réduction -10%:</span>
                                                    <span>-<?= number_format($commande['reduction_appliquee'], 2, ',', ' ') ?> €</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($commande['frais_livraison'] > 0): ?>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>
                                                        <i class="bi bi-truck"></i> Frais de livraison
                                                        <?php if ($commande['distance_km'] > 0): ?>
                                                            (<?= number_format($commande['distance_km'], 0) ?> km)
                                                        <?php endif; ?>:
                                                    </span>
                                                    <span><?= number_format($commande['frais_livraison'], 2, ',', ' ') ?> €</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <h4 class="text-success mb-0">
                                            <?= number_format($commande['prix_total'], 2, ',', ' ') ?> €
                                        </h4>
                                        <small class="text-muted">Total TTC</small>
                                    </div>
                                </div>

                                <!-- Annulation si motif -->
                                <?php if ($commande['statut'] === 'annulée' && $commande['motif_annulation']): ?>
                                    <div class="alert alert-danger mt-3 mb-0 small">
                                        <strong><i class="bi bi-exclamation-triangle"></i> Motif d'annulation:</strong>
                                        <p class="mb-1"><?= nl2br(htmlspecialchars($commande['motif_annulation'])) ?></p>
                                        <?php if ($commande['mode_contact_annulation']): ?>
                                            <small class="text-muted">
                                                Contact: <?= htmlspecialchars($commande['mode_contact_annulation']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Actions -->
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    <a href="/commande-detail.php?id=<?= $commande['numero_commande'] ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i> Voir le détail
                                    </a>
                                    
                                    <?php if ($commande['statut'] === 'en attente'): ?>
                                        <a href="/modifier-commande.php?id=<?= $commande['numero_commande'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </a>
                                        <button 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="if(confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) window.location.href='/annuler-commande.php?id=<?= $commande['numero_commande'] ?>'"
                                        >
                                            <i class="bi bi-x-circle"></i> Annuler
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($commande['statut'] === 'terminée'): ?>
                                        <a href="/donner-avis.php?commande=<?= $commande['numero_commande'] ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-star"></i> Donner mon avis
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Statistiques -->
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card bg-primary text-white text-center">
                        <div class="card-body">
                            <h2 class="mb-0"><?= count($commandes) ?></h2>
                            <small>Commande<?= count($commandes) > 1 ? 's' : '' ?> total<?= count($commandes) > 1 ? 'es' : 'e' ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white text-center">
                        <div class="card-body">
                            <h2 class="mb-0">
                                <?= count(array_filter($commandes, fn($c) => in_array($c['statut'], ['livré', 'terminée']))) ?>
                            </h2>
                            <small>Commande<?= count(array_filter($commandes, fn($c) => in_array($c['statut'], ['livré', 'terminée']))) > 1 ? 's' : '' ?> terminée<?= count(array_filter($commandes, fn($c) => in_array($c['statut'], ['livré', 'terminée']))) > 1 ? 's' : '' ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white text-center">
                        <div class="card-body">
                            <h2 class="mb-0">
                                <?= count(array_filter($commandes, fn($c) => !in_array($c['statut'], ['livré', 'terminée', 'annulée']))) ?>
                            </h2>
                            <small>En cours</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
