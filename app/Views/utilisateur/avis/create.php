<?php include __DIR__ . '/../../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/pages/avis.css">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-star-fill"></i> Donner votre avis<?php if (isset($numeroCommande)): ?> - Commande #<?= htmlspecialchars($numeroCommande) ?><?php endif; ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/avis">
                        <?= csrf_field() ?>
                        <?php if (isset($numeroCommande)): ?>
                            <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($numeroCommande) ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Note <span class="text-danger">*</span></label>
                            <select class="form-select" id="note" name="note" required>
                                <option value="">Sélectionnez une note...</option>
                                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                <option value="3">⭐⭐⭐ (3/5)</option>
                                <option value="2">⭐⭐ (2/5)</option>
                                <option value="1">⭐ (1/5)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="5" required placeholder="Partagez votre expérience avec nous..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary rounded-pill">Annuler</a>
                            <button type="submit" class="btn btn-vg-gold rounded-pill"><i class="bi bi-send"></i> Envoyer mon avis</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'envoi d'avis -->
<?php 
$avisEnvoye = isset($_SESSION['avis_envoye']) ? $_SESSION['avis_envoye'] : false;
if ($avisEnvoye) {
    unset($_SESSION['avis_envoye']);
}
?>
<?php if ($avisEnvoye): ?>
<div class="modal fade" id="modalConfirmationAvis" tabindex="-1" aria-labelledby="modalConfirmationAvisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-gold text-white">
                <h5 class="modal-title" id="modalConfirmationAvisLabel">
                    Avis enregistré
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                </div>
                <h5 class="mb-3">Merci pour votre retour !</h5>
                <p class="text-muted mb-4">
                    Votre avis a été enregistré avec succès et sera publié après validation par notre équipe.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="/" class="btn btn-vg-gold rounded-pill">
                    <i class="bi bi-house"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$additionalScripts = ['/assets/js/avis.js', '/assets/js/modales.js'];
include __DIR__ . '/../../layouts/footer.php'; 
?>
