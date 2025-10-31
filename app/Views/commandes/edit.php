<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Modifier la commande #<?= htmlspecialchars($commande['id']) ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/commande/modifier">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($commande['id']) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Menu</label>
                            <p class="form-control-plaintext"><strong><?= htmlspecialchars($commande['menu_nom']) ?></strong></p>
                            <small class="text-muted">Le menu ne peut pas être modifié. Vous devez annuler et créer une nouvelle commande pour changer de menu.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" 
                                   min="1" value="<?= htmlspecialchars($commande['quantite']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_livraison" class="form-label">Date de livraison souhaitée</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                   value="<?= htmlspecialchars($commande['date_livraison']) ?>" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/mes-commandes" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
