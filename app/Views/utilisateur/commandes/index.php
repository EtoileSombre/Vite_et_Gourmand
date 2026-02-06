<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../../layouts/header.php';

// Vérifier si une commande vient d'être créée
$commandeNumero = isset($_SESSION['commande_numero']) ? $_SESSION['commande_numero'] : null;
if ($commandeNumero) {
    unset($_SESSION['commande_numero']);
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Mes commandes</h2>
        <a href="/commande/nouvelle" class="btn btn-vg-gold rounded-pill">
            <i class="bi bi-plus-circle"></i> Nouvelle commande
        </a>
    </div>
    
    <?php if (empty($commandes)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore passé de commande.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Menu</th>
                        <th>Quantité</th>
                        <th>Date de livraison</th>
                        <th>Statut</th>
                        <th>Total TTC</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($commande['numero_commande'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (isset($commande['lignesMenus']) && count($commande['lignesMenus']) > 0): ?>
                                    <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                        <?= htmlspecialchars($ligne['menu_nom']) ?> (<?= $ligne['nombre_personne'] ?> pers.)<br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($commande['totalPersonnes'] ?? 0) ?> pers.</td>
                            <td><?= $commande['date_prestation'] ? date('d/m/Y', strtotime($commande['date_prestation'])) : 'Non définie' ?></td>
                            <td>
                                <?php
                                $statut = $commande['statut'] ?? 'en_attente';
                                $statutLabel = $statuts[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
                                $badgeClass = 'badge-statut-' . str_replace('_', '-', $statut);
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                            </td>
                            <td>
                                <?= number_format($commande['total_final'] ?? 0, 2, ',', ' ') ?> €
                            </td>
                            <td>
                                <div class="d-flex gap-1 align-items-center justify-content-center">
                                    <a href="/commande/details?numero=<?= urlencode($commande['numero_commande']) ?>" 
                                       class="btn btn-sm btn-outline-vg-bordeaux rounded-circle btn-action-circle" 
                                       title="Voir les détails et le suivi">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($statut === 'en_attente'): ?>
                                        <a href="/commande/modifier?numero=<?= urlencode($commande['numero_commande']) ?>" 
                                           class="btn btn-sm btn-outline-vg-bordeaux rounded-circle btn-action-circle" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/commande/annuler?numero=<?= urlencode($commande['numero_commande']) ?>" 
                                           class="btn btn-sm btn-outline-vg-bordeaux btn-annuler-commande rounded-circle btn-action-circle" 
                                           title="Annuler">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php elseif ($statut === 'terminee'): ?>
                                        <a href="/avis/create?commande=<?= urlencode($commande['numero_commande']) ?>" 
                                           class="btn btn-sm btn-outline-vg-bordeaux rounded-circle btn-action-circle" 
                                           title="Donner votre avis">
                                            <i class="bi bi-star-fill"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de création de commande -->
<?php if ($commandeNumero): ?>
<div class="modal fade" id="modalConfirmationCommande" tabindex="-1" aria-labelledby="modalConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-gold text-white">
                <h5 class="modal-title" id="modalConfirmationLabel">
                    Commande enregistrée
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                </div>
                <h5 class="mb-3">Votre commande a bien été envoyée !</h5>
                <p class="text-muted mb-2">
                    Numéro de commande : <strong class="text-dark"><?= htmlspecialchars($commandeNumero) ?></strong>
                </p>
                <p class="text-muted mb-4">
                    Un email de confirmation vous a été envoyé.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-vg-gold rounded-pill" data-bs-dismiss="modal">
                    <i class="bi bi-check2"></i> J'ai compris
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
