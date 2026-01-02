<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt"></i> Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
        <a href="/mes-commandes" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <!-- Informations de la commande -->
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Détails de la Commande</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Date de commande :</dt>
                        <dd class="col-sm-8"><?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></dd>

                        <dt class="col-sm-4">Date de prestation :</dt>
                        <dd class="col-sm-8">
                            <strong><?= date('d/m/Y', strtotime($commande['date_prestation'])) ?></strong>
                            <?php if (!empty($commande['heure_livraison'])): ?>
                                à <?= htmlspecialchars($commande['heure_livraison']) ?>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4">Lieu de livraison :</dt>
                        <dd class="col-sm-8">
                            <?= htmlspecialchars($commande['lieu_livraison'] ?? 'Non renseigné') ?>
                            <?php if (!empty($commande['ville_livraison'])): ?>
                                <br><small class="text-muted">
                                    <?= htmlspecialchars($commande['ville_livraison']) ?>
                                    <?php if (!empty($commande['code_postal_livraison'])): ?>
                                        (<?= htmlspecialchars($commande['code_postal_livraison']) ?>)
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4">Menu(s) commandé(s) :</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($commande['lignesMenus'])): ?>
                                <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                    <div class="mb-2">
                                        <strong><?= htmlspecialchars($ligne['menu_nom']) ?></strong><br>
                                        <small class="text-muted">
                                            <?= $ligne['nombre_personne'] ?> personne(s) × <?= number_format($ligne['prix_par_personne'], 2) ?> € = 
                                            <?= number_format($ligne['total_ligne'] ?? 0, 2) ?> €
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Aucun menu</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4">Nombre total de personnes :</dt>
                        <dd class="col-sm-8"><strong><?= htmlspecialchars($commande['totalPersonnes'] ?? 0) ?></strong></dd>

                        <?php if ($commande['pret_materiel']): ?>
                            <dt class="col-sm-4">Prêt de matériel :</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-info">
                                    <i class="bi bi-box-seam"></i> Oui (Caution: 600 €)
                                </span>
                            </dd>
                        <?php endif; ?>

                        <dt class="col-sm-4">Montant total :</dt>
                        <dd class="col-sm-8">
                            <?php 
                            // Calculer le sous-total des menus
                            $sousTotal = 0;
                            if (!empty($commande['lignesMenus'])) {
                                foreach ($commande['lignesMenus'] as $ligne) {
                                    $sousTotal += $ligne['total_ligne'] ?? 0;
                                }
                            }
                            ?>
                            <div class="mb-2">
                                <small class="text-muted">Sous-total menus :</small>
                                <strong><?= number_format($sousTotal, 2) ?> €</strong>
                            </div>
                            <?php if (isset($commande['prix_livraison']) && $commande['prix_livraison'] > 0): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Frais de livraison 
                                        <?php if (isset($commande['distance_km']) && $commande['distance_km'] > 0): ?>
                                            (<?= number_format($commande['distance_km'], 1) ?> km)
                                        <?php endif; ?>
                                        :
                                    </small>
                                    <strong><?= number_format($commande['prix_livraison'], 2) ?> €</strong>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($commande['reduction_appliquee']) && $commande['reduction_appliquee'] > 0): ?>
                                <div class="mb-2">
                                    <small class="text-success">Réduction appliquée :</small>
                                    <strong class="text-success">-<?= number_format($commande['reduction_appliquee'], 2) ?> €</strong>
                                </div>
                            <?php endif; ?>
                            <hr class="my-2">
                            <h4 class="text-primary mb-0">
                                Total final : <?= number_format($commande['total_final'] ?? 0, 2) ?> €
                            </h4>
                        </dd>

                        <dt class="col-sm-4">Statut actuel :</dt>
                        <dd class="col-sm-8">
                            <?php
                            $statut = $commande['statut'] ?? 'en_attente';
                            $statutClass = match($statut) {
                                'en_attente' => 'warning text-dark',
                                'acceptee' => 'success',
                                'en_preparation' => 'primary',
                                'en_cours_livraison' => 'purple',
                                'livree' => 'orange text-dark',
                                'attente_retour_materiel' => 'brown text-white',
                                'terminee' => 'dark-green text-white',
                                'annulee' => 'danger',
                                default => 'secondary'
                            };
                            $statutText = ucfirst(str_replace('_', ' ', $statut));
                            ?>
                            <h5><span class="badge bg-<?= $statutClass ?>"><?= $statutText ?></span></h5>
                        </dd>
                    </dl>

                    <!-- Actions selon le statut -->
                    <div class="mt-4">
                        <?php if ($statut === 'en_attente'): ?>
                            <a href="/commande/modifier?numero=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Modifier la commande
                            </a>
                            <a href="/commande/annuler?numero=<?= urlencode($commande['numero_commande']) ?>" 
                               class="btn btn-danger btn-annuler-commande">
                                <i class="bi bi-x-circle"></i> Annuler la commande
                            </a>
                        <?php elseif ($statut === 'terminee' && !$avisExistant): ?>
                            <a href="/avis/create?commande=<?= urlencode($commande['numero_commande']) ?>" 
                               class="btn btn-warning">
                                <i class="bi bi-star-fill"></i> Donner votre avis
                            </a>
                        <?php elseif ($statut === 'terminee' && $avisExistant): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-check-circle"></i> Vous avez déjà donné votre avis pour cette commande. Merci !
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suivi de la commande -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header text-dark bg-vg-gold">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Suivi de la Commande</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($historique)): ?>
                        <div class="timeline">
                            <?php foreach ($historique as $index => $suivi): ?>
                                <div class="timeline-item mb-3 pb-3 <?= $index < count($historique) - 1 ? 'border-bottom' : '' ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="badge bg-<?= match($suivi['nouveau_statut']) {
                                                'en_attente' => 'warning',
                                                'acceptee' => 'success',
                                                'en_preparation' => 'primary',
                                                'en_cours_livraison' => 'purple',
                                                'livree' => 'orange',
                                                'attente_retour_materiel' => 'brown',
                                                'terminee' => 'dark-green',
                                                'annulee' => 'danger',
                                                default => 'secondary'
                                            } ?> rounded-circle p-2 timeline-badge">
                                                <i class="bi bi-<?= match($suivi['nouveau_statut']) {
                                                    'en_attente' => 'hourglass-split',
                                                    'acceptee' => 'check-circle',
                                                    'en_preparation' => 'gear',
                                                    'en_cours_livraison' => 'truck',
                                                    'livree' => 'box-seam',
                                                    'attente_retour_materiel' => 'arrow-return-left',
                                                    'terminee' => 'check-all',
                                                    'annulee' => 'x-circle',
                                                    default => 'circle'
                                                } ?>"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= ucfirst(str_replace('_', ' ', $suivi['nouveau_statut'])) ?></h6>
                                            <small class="text-muted">
                                                <?= date('d/m/Y à H:i', strtotime($suivi['date_changement'])) ?>
                                            </small>
                                            <?php if (!empty($suivi['employe_prenom'])): ?>
                                                <br><small class="text-muted">
                                                    Par <?= htmlspecialchars($suivi['employe_prenom']) ?> 
                                                    <?= htmlspecialchars($suivi['employe_nom']) ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if (!empty($suivi['commentaire'])): ?>
                                                <div class="mt-1">
                                                    <small><em><?= htmlspecialchars($suivi['commentaire']) ?></em></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucun suivi disponible pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item:last-child {
    border-bottom: none !important;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
