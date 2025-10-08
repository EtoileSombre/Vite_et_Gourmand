<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Modifier la commande #<?= htmlspecialchars($commande['numero_commande'] ?? 'N/A') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/commande/modifier">
                        <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($commande['numero_commande']) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Menu</label>
                            <p class="form-control-plaintext"><strong><?= htmlspecialchars($commande['menu_nom'] ?? 'Menu') ?></strong></p>
                            <small class="text-muted">Le menu ne peut pas être modifié. Vous devez annuler et créer une nouvelle commande pour changer de menu.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nombre_personnes" class="form-label">Nombre de personnes</label>
                            <input type="number" class="form-control" id="nombre_personnes" name="nombre_personnes" 
                                   min="1" value="<?= htmlspecialchars($commande['nombre_personne'] ?? 2) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_livraison" class="form-label">Date de prestation souhaitée</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                   value="<?= htmlspecialchars($commande['date_prestation'] ?? '') ?>" required>
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
