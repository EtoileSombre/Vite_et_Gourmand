<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt"></i> Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
        <a href="/mes-commandes" class="btn btn-outline-secondary rounded-pill">
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

                        <?php if ($commande['reductionTotale'] > 0): ?>
                            <dt class="col-sm-4">Réduction appliquée :</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-success">
                                    <i class="bi bi-tag-fill"></i> -<?= number_format($commande['reductionTotale'], 2, ',', ' ') ?> €
                                </span>
                                <br>
                            </dd>
                        <?php endif; ?>

                        <?php if ($commande['pret_materiel']): ?>
                            <dt class="col-sm-4">Prêt de matériel :</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-info">
                                    <i class="bi bi-box-seam"></i> Oui
                                </span>
                            </dd>
                        <?php endif; ?>

                        <dt class="col-sm-4">Montant total :</dt>
                        <dd class="col-sm-8">
                            <div class="mb-2">
                                <small class="text-muted">Sous-total menus :</small>
                                <strong><?= number_format($commande['sousTotal'] ?? 0, 2) ?> € HT</strong>
                            </div>
                            <?php if (isset($commande['prix_livraison']) && $commande['prix_livraison'] > 0): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Frais de livraison 
                                        <?php if (isset($commande['distance_km']) && $commande['distance_km'] > 0): ?>
                                            (<?= number_format($commande['distance_km'], 1) ?> km)
                                        <?php endif; ?>
                                        :
                                    </small>
                                    <strong><?= number_format($commande['prix_livraison'], 2) ?> € HT</strong>
                                </div>
                            <?php endif; ?>
                            <hr class="my-2">
                            <h4 class="text-vg-bordeaux mb-0">
                                Total TTC : <?= number_format($commande['total_final'] ?? 0, 2) ?> €
                            </h4>
                        </dd>

                        <dt class="col-sm-4">Statut actuel :</dt>
                        <dd class="col-sm-8">
                            <?php
                            $statut = $commande['statut'] ?? 'en_attente';
                            $statutLabel = $statuts[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
                            $badgeClass = 'badge-statut-' . str_replace('_', '-', $statut);
                            ?>
                            <h5><span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span></h5>
                        </dd>
                    </dl>

                    <!-- Actions selon le statut -->
                    <div class="mt-4">
                        <?php if ($statut === 'en_attente'): ?>
                            <a href="/commande/annuler?numero=<?= urlencode($commande['numero_commande']) ?>" 
                               class="btn btn-danger btn-annuler-commande rounded-pill">
                                <i class="bi bi-x-circle"></i> Annuler la commande
                            </a>
                            <a href="/commande/modifier?numero=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-vg-gold rounded-pill">
                                <i class="bi bi-pencil"></i> Modifier la commande
                            </a>
                        <?php elseif ($statut === 'terminee' && !$avisExistant): ?>
                            <a href="/avis/create?commande=<?= urlencode($commande['numero_commande']) ?>" 
                               class="btn btn-warning rounded-pill">
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
                <div class="card-header text-white bg-vg-gold">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Suivi de la Commande</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($historique)): ?>
                        <div class="timeline">
                            <?php foreach ($historique as $index => $suivi): ?>
                                <div class="timeline-item mb-3 pb-3 <?= $index < count($historique) - 1 ? 'border-bottom' : '' ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <?php
                                            $timelineStatut = $suivi['nouveau_statut'];
                                            $timelineBadgeClass = 'badge-statut-' . str_replace('_', '-', $timelineStatut);
                                            ?>
                                            <div class="rounded-circle p-2 timeline-badge <?= $timelineBadgeClass ?>">
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
                                            <h6 class="mb-1"><?= $statuts[$suivi['nouveau_statut']] ?? ucfirst(str_replace('_', ' ', $suivi['nouveau_statut'])) ?></h6>
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

<!-- Modal de confirmation d'annulation -->
<div class="modal fade" id="modalAnnulerCommande" tabindex="-1" aria-labelledby="modalAnnulerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-bordeaux text-white">
                <h5 class="modal-title" id="modalAnnulerLabel">
                    Confirmer l'annulation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-x-circle fs-1 text-vg-bordeaux"></i>
                </div>
                <h5 class="mb-3">Êtes-vous sûr de vouloir annuler cette commande ?</h5>
                <p class="text-muted mb-4">
                    Cette action est irréversible. Vous devrez créer une nouvelle commande si vous changez d'avis.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="#" id="confirmAnnulerBtn" class="btn btn-vg-bordeaux rounded-pill">
                    <i class="bi bi-x-circle"></i> Oui, annuler
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$additionalScripts = ['/assets/js/modales.js'];
include __DIR__ . '/../../layouts/footer.php'; 
?>
