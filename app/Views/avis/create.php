<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-star-fill"></i> Donner votre avis<?php if (isset($numeroCommande)): ?> - Commande #<?= htmlspecialchars($numeroCommande) ?><?php endif; ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/avis">
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
                            <a href="/" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-vg-gold"><i class="bi bi-send"></i> Envoyer mon avis</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
