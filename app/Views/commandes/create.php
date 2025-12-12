<?php 
// Récupérer les paramètres depuis l'URL
$menuIdFromUrl = $_GET['menu_id'] ?? null;
$boissonsFromUrl = $_GET['boissons'] ?? '';
$materielsFromUrl = $_GET['materiels'] ?? '';
$boissonsIds = $boissonsFromUrl ? explode(',', $boissonsFromUrl) : [];
$materielsIds = $materielsFromUrl ? explode(',', $materielsFromUrl) : [];
?>
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
                                    <option value="<?= $menu['menu_id'] ?>" 
                                            <?= ($menuIdFromUrl == $menu['menu_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($menu['titre']) ?> - <?= number_format($menu['prix_par_personne'], 2) ?> € /pers
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Champs cachés pour les boissons -->
                        <?php if (!empty($boissonsIds)): ?>
                            <?php foreach ($boissonsIds as $boissonId): ?>
                                <input type="hidden" name="boissons[]" value="<?= htmlspecialchars($boissonId) ?>">
                            <?php endforeach; ?>
                            
                            <!-- Affichage des boissons sélectionnées -->
                            <?php if (isset($boissonsSelectionnees) && !empty($boissonsSelectionnees)): ?>
                                <div class="alert alert-info mb-3">
                                    <h6><i class="bi bi-cup-straw"></i> Boissons sélectionnées :</h6>
                                    <ul class="mb-0">
                                        <?php 
                                        $totalBoissons = 0;
                                        foreach ($boissonsSelectionnees as $boisson): 
                                            $totalBoissons += $boisson['prix_unitaire'];
                                        ?>
                                            <li>
                                                <?= htmlspecialchars($boisson['nom']) ?> 
                                                (<?= htmlspecialchars($boisson['contenance']) ?>) - 
                                                <strong><?= number_format($boisson['prix_unitaire'], 2) ?> €</strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <hr>
                                    <strong>Total boissons : <?= number_format($totalBoissons, 2) ?> €</strong>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Champs cachés pour le matériel -->
                        <?php if (!empty($materielsIds)): ?>
                            <?php foreach ($materielsIds as $materielId): ?>
                                <input type="hidden" name="materiels[]" value="<?= htmlspecialchars($materielId) ?>">
                            <?php endforeach; ?>
                            
                            <!-- Affichage du matériel sélectionné -->
                            <?php if (isset($materielsSelectionnes) && !empty($materielsSelectionnes)): ?>
                                <div class="alert alert-warning mb-3">
                                    <h6><i class="bi bi-box-seam"></i> Matériel sélectionné :</h6>
                                    <ul class="mb-0">
                                        <?php 
                                        $totalCaution = 0;
                                        foreach ($materielsSelectionnes as $materiel): 
                                            $totalCaution += $materiel['prix_caution'];
                                        ?>
                                            <li>
                                                <?= htmlspecialchars($materiel['nom']) ?> - 
                                                Caution: <strong><?= number_format($materiel['prix_caution'], 2) ?> €</strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <hr>
                                    <strong>Total caution : <?= number_format($totalCaution, 2) ?> €</strong>
                                    <br><small class="text-muted">La caution vous sera restituée après retour du matériel en bon état.</small>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="nombre_personnes" class="form-label">Nombre de personnes</label>
                            <input type="number" class="form-control" id="nombre_personnes" name="nombre_personnes" 
                                   min="1" value="2" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_livraison" class="form-label">Date de livraison souhaitée</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" 
                                   min="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                            <small class="form-text text-muted">Commande minimum 7 jours à l'avance</small>
                        </div>

                        <div class="mb-3">
                            <label for="heure_livraison" class="form-label">Heure de livraison souhaitée</label>
                            <input type="time" class="form-control" id="heure_livraison" name="heure_livraison" 
                                   min="10:00" max="22:00" required>
                            <small class="form-text text-muted">Entre 10h et 22h</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse_livraison" class="form-label">Adresse de livraison</label>
                            <input type="text" class="form-control" id="adresse_livraison" name="adresse_livraison" 
                                   placeholder="12 Rue de la Paix, 33000 Bordeaux" required>
                        </div>

                        <div class="mb-3">
                            <label for="lieu_livraison" class="form-label">Ville de livraison</label>
                            <input type="text" class="form-control" id="lieu_livraison" name="lieu_livraison" 
                                   placeholder="Bordeaux" required>
                            <small class="form-text text-muted">Les frais de livraison seront calculés automatiquement</small>
                        </div>

                        <div class="mb-3">
                            <label for="distance_km" class="form-label">Distance depuis Bordeaux (en km)</label>
                            <input type="number" class="form-control" id="distance_km" name="distance_km" 
                                   min="0" step="0.1" value="0" required>
                            <small class="form-text text-muted">0 km si vous êtes à Bordeaux. Frais : 5€ + 0,59€/km</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="pret_materiel" name="pret_materiel" value="1">
                            <label class="form-check-label" for="pret_materiel">
                                Je souhaite emprunter du matériel (vaisselle, couverts, plateaux)
                            </label>
                            <small class="form-text text-muted d-block">À restituer dans les 10 jours (pénalité de 600€ après ce délai)</small>
                        </div>

                        <div class="alert alert-info">
                            <strong>📋 Récapitulatif des tarifs :</strong>
                            <ul class="mb-0">
                                <li>Prix par personne selon le menu choisi</li>
                                <li><strong>Réduction de 10%</strong> automatique si vous commandez pour 5 personnes de plus que le minimum</li>
                                <li><strong>Frais de livraison :</strong> 5€ à Bordeaux, 5€ + 0,59€/km ailleurs</li>
                            </ul>
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
