<?php include __DIR__ . '/../../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier la commande #<?= htmlspecialchars($commande['numero_commande'] ?? 'N/A') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" action="/commande/modifier">
                        <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($commande['numero_commande']) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Menus commandés</label>
                            <?php if (!empty($commande['lignesMenus'])): ?>
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
                                        <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ligne['menu_nom']) ?></td>
                                                <td><?= htmlspecialchars($ligne['nombre_personne']) ?></td>
                                                <td><?= number_format($ligne['prix_par_personne'], 2) ?> €</td>
                                                <td><?= number_format($ligne['total_ligne'], 2) ?> €</td>
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
                                   min="1" value="<?= htmlspecialchars(!empty($commande['lignesMenus']) ? $commande['lignesMenus'][0]['nombre_personne'] : 2) ?>" required>
                            <small class="text-muted">Pour une modification plus complexe, contactez-nous.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_livraison" class="form-label">Date de prestation souhaitée</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                   value="<?= htmlspecialchars($commande['date_prestation'] ?? '') ?>" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/mes-commandes" class="btn btn-secondary rounded-pill">Annuler</a>
                            <button type="submit" class="btn btn-primary rounded-pill">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
