<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Nouvelle commande</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/commande/nouvelle">
                        <div class="mb-3">
                            <label for="menu_id" class="form-label">Choisir un menu</label>
                            <select class="form-select" id="menu_id" name="menu_id" required>
                                <option value="">Sélectionnez un menu...</option>
                                <?php foreach ($menus as $menu): ?>
                                    <option value="<?= $menu['menu_id'] ?>">
                                        <?= htmlspecialchars($menu['titre']) ?> - <?= number_format($menu['prix_par_personne'], 2) ?> € /pers
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nombre_personnes" class="form-label">Nombre de personnes</label>
                            <input type="number" class="form-control" id="nombre_personnes" name="nombre_personnes" 
                                   min="1" value="2" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_livraison" class="form-label">Date de livraison souhaitée</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/mes-commandes" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">Valider la commande</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
