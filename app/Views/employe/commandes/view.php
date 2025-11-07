<?php
/**
 * Vue : Détails d'une commande (Employé)
 */
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt"></i> Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
        <div>
            <?php if (!in_array($commande['statut'], ['terminée', 'refusée', 'annulée'])): ?>
                <a href="/employe/commandes/change-status?id=<?= $commande['numero_commande'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Changer statut
                </a>
            <?php endif; ?>
            <a href="/employe/commandes" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations Client -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Informations Client</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nom :</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($commande['client_prenom'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4">Email :</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:<?= htmlspecialchars($commande['client_email']) ?>">
                                <i class="bi bi-envelope"></i> <?= htmlspecialchars($commande['client_email']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-4">Téléphone :</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($commande['client_telephone'])): ?>
                                <a href="tel:<?= htmlspecialchars($commande['client_telephone']) ?>">
                                    <i class="bi bi-phone"></i> <?= htmlspecialchars($commande['client_telephone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Informations Menu -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-card-list"></i> Menu Commandé</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Menu :</dt>
                        <dd class="col-sm-8"><strong><?= htmlspecialchars($commande['menu_titre'] ?? 'N/A') ?></strong></dd>

                        <dt class="col-sm-4">Description :</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($commande['menu_description'] ?? 'Aucune description') ?></dd>

                        <dt class="col-sm-4">Prix/personne :</dt>
                        <dd class="col-sm-8"><?= number_format($commande['prix_menu'], 2) ?> €</dd>

                        <dt class="col-sm-4">Nb personnes :</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($commande['nombre_personne']) ?></dd>

                        <dt class="col-sm-4">Régime :</dt>
                        <dd class="col-sm-8">
                            <?= !empty($commande['menu_regime']) ? htmlspecialchars($commande['menu_regime']) : '<span class="text-muted">Standard</span>' ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Détails Prestation -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Prestation</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Date commande :</dt>
                        <dd class="col-sm-7"><?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></dd>

                        <dt class="col-sm-5">Date prestation :</dt>
                        <dd class="col-sm-7">
                            <strong><?= date('d/m/Y', strtotime($commande['date_prestation'])) ?></strong>
                        </dd>

                        <dt class="col-sm-5">Heure livraison :</dt>
                        <dd class="col-sm-7"><strong><?= htmlspecialchars($commande['heure_livraison']) ?></strong></dd>

                        <dt class="col-sm-5">Lieu :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['lieu_livraison']) ?></dd>

                        <?php if (!empty($commande['distance_km'])): ?>
                            <dt class="col-sm-5">Distance :</dt>
                            <dd class="col-sm-7"><?= htmlspecialchars($commande['distance_km']) ?> km</dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Matériel et Statut -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Matériel & Statut</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Statut actuel :</dt>
                        <dd class="col-sm-7">
                            <span class="badge <?= match($commande['statut']) {
                                'en attente' => 'bg-warning',
                                'validée' => 'bg-info',
                                'en préparation' => 'bg-primary',
                                'terminée' => 'bg-success',
                                'refusée', 'annulée' => 'bg-danger',
                                default => 'bg-secondary'
                            } ?> fs-6">
                                <?= ucfirst($commande['statut']) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-5">Prêt matériel :</dt>
                        <dd class="col-sm-7">
                            <?php if ($commande['pret_materiel']): ?>
                                <span class="badge bg-info">Oui (600€ caution)</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non</span>
                            <?php endif; ?>
                        </dd>

                        <?php if ($commande['pret_materiel']): ?>
                            <dt class="col-sm-5">Restitution :</dt>
                            <dd class="col-sm-7">
                                <?php if ($commande['restitution_materiel']): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Restitué</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> En attente</span>
                                <?php endif; ?>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Récapitulatif Prix -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Récapitulatif Prix</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-6">Prix menu :</dt>
                                <dd class="col-sm-6"><?= number_format($commande['prix_menu'], 2) ?> € × <?= $commande['nombre_personne'] ?> = 
                                    <strong><?= number_format($commande['prix_menu'] * $commande['nombre_personne'], 2) ?> €</strong>
                                </dd>

                                <?php if (!empty($commande['reduction'])): ?>
                                    <dt class="col-sm-6 text-success">Réduction :</dt>
                                    <dd class="col-sm-6 text-success">- <?= number_format($commande['reduction'], 2) ?> €</dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <?php if (!empty($commande['frais_livraison'])): ?>
                                    <dt class="col-sm-6">Frais livraison :</dt>
                                    <dd class="col-sm-6">+ <?= number_format($commande['frais_livraison'], 2) ?> €</dd>
                                <?php endif; ?>

                                <dt class="col-sm-6"><strong>TOTAL :</strong></dt>
                                <dd class="col-sm-6">
                                    <strong class="fs-4 text-primary">
                                        <?= number_format($commande['prix_total'] ?? 0, 2) ?> €
                                    </strong>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique de contact (si existe) -->
        <?php if (!empty($commande['motif_modification']) || !empty($commande['mode_contact_client'])): ?>
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historique Contact Client</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <?php if (!empty($commande['mode_contact_client'])): ?>
                                <dt class="col-sm-2">Mode contact :</dt>
                                <dd class="col-sm-10">
                                    <span class="badge bg-info"><?= htmlspecialchars($commande['mode_contact_client']) ?></span>
                                </dd>
                            <?php endif; ?>

                            <?php if (!empty($commande['motif_modification'])): ?>
                                <dt class="col-sm-2">Motif :</dt>
                                <dd class="col-sm-10"><?= nl2br(htmlspecialchars($commande['motif_modification'])) ?></dd>
                            <?php endif; ?>

                            <?php if (!empty($commande['date_dernier_contact'])): ?>
                                <dt class="col-sm-2">Date contact :</dt>
                                <dd class="col-sm-10"><?= date('d/m/Y à H:i', strtotime($commande['date_dernier_contact'])) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
