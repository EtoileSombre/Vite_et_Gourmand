<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier la commande #<?= htmlspecialchars($commande->getNumeroCommande() ?? 'N/A') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/commande/modifier">
                        <?= csrf_field() ?>
                        <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($commande->getNumeroCommande()) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Menus commandés</label>
                            <?php if (!empty($commande->getLignesMenus())): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th>Nb Personnes</th>
                                            <th>Prix/pers.</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($commande->getLignesMenus() as $ligne): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ligne->getMenuNom()) ?></td>
                                                <td><?= htmlspecialchars($ligne->getNombrePersonne()) ?></td>
                                                <td><?= number_format($ligne->getPrixParPersonne(), 2) ?> €</td>
                                                <td><?= number_format($ligne->getTotalLigne(), 2) ?> €</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <small class="text-muted">Les menus ne peuvent pas être modifiés via ce formulaire. Pour changer les menus, veuillez annuler cette commande et en créer une nouvelle.</small>
                            <?php else: ?>
                                <p class="text-muted">Aucun menu dans cette commande</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nombre_personnes" class="form-label">Modifier le nombre de personnes</label>
                            <input type="number" class="form-control" id="nombre_personnes" name="nombre_personnes" 
                                   min="1" value="<?= htmlspecialchars(!empty($commande->getLignesMenus()) ? $commande->getLignesMenus()[0]->getNombrePersonne() : 2) ?>" required>
                            <small class="text-muted">Pour une modification plus complexe, contactez-nous.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_livraison" class="form-label">Date de prestation souhaitée</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                   value="<?= htmlspecialchars($commande->getDatePrestation() ?? '') ?>" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/mes-commandes" class="btn btn-outline-vg-bordeaux rounded-pill">Annuler</a>
                            <button type="submit" class="btn btn-vg-gold rounded-pill">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de modification de commande -->
<?php 
$commandeModifiee = isset($_SESSION['commande_modifiee']) ? $_SESSION['commande_modifiee'] : false;
if ($commandeModifiee) {
    unset($_SESSION['commande_modifiee']);
}
?>
<?php if ($commandeModifiee): ?>
<div class="modal fade" id="modalConfirmationModification" tabindex="-1" aria-labelledby="modalConfirmationModificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-gold text-white">
                <h5 class="modal-title" id="modalConfirmationModificationLabel">
                    Commande modifiée
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                </div>
                <h5 class="mb-3">Vos modifications ont bien été enregistrées !</h5>
                <p class="text-muted mb-4">
                    Un email de confirmation vous a été envoyé.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="/mes-commandes" class="btn btn-vg-gold rounded-pill">
                    <i class="bi bi-arrow-left"></i> Retour à mes commandes
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$additionalScripts = ['/assets/js/modales.js'];
include __DIR__ . '/../../layouts/footer.php'; 
?>
