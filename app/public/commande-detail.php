<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Vérifier l'authentification
requireLogin('/commande-detail.php');
$currentUser = getCurrentUser();

// Récupérer l'ID de la commande
$commandeId = $_GET['id'] ?? null;

if (!$commandeId) {
    $_SESSION['flash_error'] = "Commande introuvable.";
    header('Location: /mes-commandes.php');
    exit;
}

try {
    // Récupérer les détails de la commande avec le menu
    $stmt = $pdo->prepare("
        SELECT c.*, m.titre as menu_titre, m.description as menu_description,
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

    // Récupérer l'historique de suivi de la commande
    $stmtSuivi = $pdo->prepare("
        SELECT * FROM suivi_commande
        WHERE numero_commande = ?
        ORDER BY date_modification DESC
    ");
    $stmtSuivi->execute([$commandeId]);
    $historique = $stmtSuivi->fetchAll();

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération de la commande : " . $e->getMessage());
    $_SESSION['flash_error'] = "Erreur lors de la récupération de la commande.";
    header('Location: /mes-commandes.php');
    exit;
}

// Fonction pour obtenir le badge de statut
function getStatutBadge($statut) {
    $badges = [
        'en attente' => 'warning',
        'accepté' => 'info',
        'en préparation' => 'primary',
        'livré' => 'success',
        'terminée' => 'success',
        'annulée' => 'danger'
    ];
    return $badges[$statut] ?? 'secondary';
}

// Fonction pour obtenir l'icône de statut
function getStatutIcon($statut) {
    $icons = [
        'en attente' => 'bi-clock-history',
        'accepté' => 'bi-check-circle',
        'en préparation' => 'bi-gear',
        'livré' => 'bi-truck',
        'terminée' => 'bi-check-circle-fill',
        'annulée' => 'bi-x-circle'
    ];
    return $icons[$statut] ?? 'bi-question-circle';
}

$pageTitle = "Commande #" . htmlspecialchars($commande['numero_commande']);
include '../includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/mes-commandes.php">Mes commandes</a></li>
            <li class="breadcrumb-item active" aria-current="page">#<?= htmlspecialchars($commande['numero_commande']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- En-tête de la commande -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-2">Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3"></i> Commandé le <?= date('d/m/Y à H:i', strtotime($commande['created_at'])) ?>
                            </p>
                        </div>
                        <span class="badge bg-<?= getStatutBadge($commande['statut']) ?> fs-6">
                            <i class="<?= getStatutIcon($commande['statut']) ?>"></i> <?= ucfirst($commande['statut']) ?>
                        </span>
                    </div>

                    <?php if ($commande['statut'] === 'en attente'): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Votre commande est en attente de validation par notre équipe.
                        </div>
                    <?php elseif ($commande['statut'] === 'accepté'): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Votre commande a été acceptée et sera bientôt en préparation.
                        </div>
                    <?php elseif ($commande['statut'] === 'en préparation'): ?>
                        <div class="alert alert-primary mb-0">
                            <i class="bi bi-gear"></i> Nos chefs préparent votre commande avec soin.
                        </div>
                    <?php elseif ($commande['statut'] === 'livré'): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-truck"></i> Votre commande a été livrée. Bon appétit !
                        </div>
                    <?php elseif ($commande['statut'] === 'annulée'): ?>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle"></i> Cette commande a été annulée.
                            <?php if ($commande['motif_annulation']): ?>
                                <br><strong>Motif :</strong> <?= htmlspecialchars($commande['motif_annulation']) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Détails du menu -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-basket"></i> Détails de la commande</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><?= htmlspecialchars($commande['menu_titre']) ?></h6>
                    <?php if ($commande['menu_description']): ?>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($commande['menu_description'])) ?></p>
                    <?php endif; ?>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-people"></i> Nombre de personnes :</strong><br><?= $commande['nombre_personne'] ?> personnes</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-calendar-event"></i> Date de prestation :</strong><br><?= date('d/m/Y', strtotime($commande['date_prestation'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-clock"></i> Heure de livraison :</strong><br><?= date('H:i', strtotime($commande['heure_livraison'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adresse de livraison -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Adresse de livraison</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><?= htmlspecialchars($commande['prenom_livraison'] . ' ' . $commande['nom_livraison']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($commande['adresse_livraison']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($commande['code_postal_livraison'] . ' ' . $commande['ville_livraison']) ?></p>
                    <p class="mb-0"><i class="bi bi-telephone"></i> <?= htmlspecialchars($commande['telephone_livraison']) ?></p>
                </div>
            </div>

            <?php if ($commande['commentaire']): ?>
                <!-- Commentaire -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Commentaire</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($commande['commentaire'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Historique de suivi -->
            <?php if (!empty($historique)): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> Historique de suivi</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($historique as $suivi): ?>
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-<?= getStatutBadge($suivi['statut']) ?> rounded-pill">
                                                <i class="<?= getStatutIcon($suivi['statut']) ?>"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-1"><strong><?= ucfirst($suivi['statut']) ?></strong></p>
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-clock"></i> <?= date('d/m/Y à H:i', strtotime($suivi['date_modification'])) ?>
                                            </p>
                                            <?php if ($suivi['commentaire']): ?>
                                                <p class="mt-1 mb-0"><em><?= htmlspecialchars($suivi['commentaire']) ?></em></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Récapitulatif des prix -->
            <div class="card shadow-sm sticky-sidebar">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Récapitulatif</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Menu (<?= $commande['nombre_personne'] ?> pers.)</td>
                            <td class="text-end"><?= number_format($commande['prix_menu'], 2, ',', ' ') ?> €</td>
                        </tr>
                        <?php if ($commande['reduction_appliquee'] > 0): ?>
                            <tr class="text-success">
                                <td><i class="bi bi-tag"></i> Réduction (-10%)</td>
                                <td class="text-end">-<?= number_format($commande['reduction_appliquee'], 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($commande['frais_livraison'] > 0): ?>
                            <tr>
                                <td><i class="bi bi-truck"></i> Livraison<?php if ($commande['distance_livraison_km']): ?> (<?= $commande['distance_livraison_km'] ?> km)<?php endif; ?></td>
                                <td class="text-end"><?= number_format($commande['frais_livraison'], 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="fw-bold border-top">
                            <td>Total</td>
                            <td class="text-end fs-5 text-primary"><?= number_format($commande['prix_total'], 2, ',', ' ') ?> €</td>
                        </tr>
                    </table>
                </div>

                <!-- Actions -->
                <div class="card-footer bg-white">
                    <?php if ($commande['statut'] === 'en attente'): ?>
                        <a href="/modifier-commande.php?id=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-pencil"></i> Modifier la commande
                        </a>
                        <a href="/annuler-commande.php?id=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle"></i> Annuler la commande
                        </a>
                    <?php elseif ($commande['statut'] === 'terminée'): ?>
                        <a href="/donner-avis.php?commande=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-primary w-100">
                            <i class="bi bi-star"></i> Donner mon avis
                        </a>
                    <?php endif; ?>
                    <a href="/mes-commandes.php" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left"></i> Retour aux commandes
                    </a>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-question-circle"></i> Besoin d'aide ?</h6>
                    <p class="card-text small">Si vous avez des questions sur votre commande, n'hésitez pas à nous contacter.</p>
                    <a href="/contact.php" class="btn btn-sm btn-outline-primary">Nous contacter</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
