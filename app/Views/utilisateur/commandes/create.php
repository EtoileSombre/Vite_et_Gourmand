<?php 
use App\Core\Session;
?>
<?php include __DIR__ . '/../../layouts/header.php'; ?>

<link rel="stylesheet" href="/assets/css/pages/commandes.css">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="/menus">Menus</a></li>
                    <li class="breadcrumb-item active">Nouvelle commande</li>
                </ol>
            </nav>

            <div class="card shadow-lg">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0">
                        <i class="bi bi-cart-plus"></i> Nouvelle commande
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="/commande/nouvelle" id="formCommande">
                        
                        <!-- ÉTAPE 1 : INFORMATIONS CLIENT (auto-remplies) -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-person-circle"></i>Vos informations
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-readonly" id="nom" name="nom" 
                                           value="<?= htmlspecialchars($user['nom'] ?? '') ?>" 
                                           readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-readonly" id="prenom" name="prenom" 
                                           value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" 
                                           readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control bg-readonly" id="email" name="email" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                           readonly>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="telephone" class="form-label">Numéro de GSM <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control bg-readonly" id="telephone" name="telephone" 
                                           value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" 
                                           readonly>
                                </div>
                            </div>
                            
                            <p class="text-muted small">
                                <i class="bi bi-info-circle"></i> Ces informations proviennent de votre compte. 
                                <a href="/profil">Modifier mes informations</a>
                            </p>
                        </div>

                        <!-- ÉTAPE 2 : INFORMATIONS PRESTATION -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-calendar-event"></i>Détails de la prestation
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_prestation" class="form-label">Date de la prestation <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_prestation" name="date_prestation" 
                                           min="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                                    <small class="form-text text-muted">Commande minimum 7 jours à l'avance</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="heure_livraison" class="form-label">Heure de livraison souhaitée <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="heure_livraison" name="heure_livraison" 
                                           min="10:00" max="22:00" required>
                                    <small class="form-text text-muted">Entre 10h et 22h</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adresse_livraison" class="form-label">Adresse complète de livraison <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="adresse_livraison" name="adresse_livraison" 
                                       value="<?= htmlspecialchars($user['adresse_postale'] ?? '') ?>"
                                       placeholder="Numéro et rue" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ville_livraison" class="form-label">Ville <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ville_livraison" name="ville_livraison" 
                                           value="Bordeaux" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="code_postal_livraison" class="form-label">Code postal</label>
                                    <input type="text" class="form-control" id="code_postal_livraison" name="code_postal_livraison" 
                                           value="<?= htmlspecialchars($user['code_postal'] ?? '') ?>"
                                           placeholder="33000">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="distance_km" class="form-label">Distance en km depuis Bordeaux</label>
                                <input type="number" step="0.1" class="form-control" id="distance_km" name="distance_km" 
                                       value="0" min="0">
                                <small class="form-text text-muted">
                                    Frais de livraison : 5€ forfait + 0,59€/km
                                </small>
                            </div>
                        </div>

                        <!-- ÉTAPE 3 : CHOIX DU MENU -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-card-list"></i>Choix du menu
                            </h5>
                            
                            <?php if ($menuPreselectionne): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill"></i> 
                                    Menu pré-sélectionné : <strong><?= htmlspecialchars($menuPreselectionne['titre']) ?></strong>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                                <select class="form-select" id="menu_id" name="menu_id" required>
                                    <option value="">Sélectionnez un menu...</option>
                                    <?php foreach ($menus as $menu): ?>
                                        <option value="<?= $menu['menu_id'] ?>" 
                                                data-prix="<?= $menu['prix_par_personne'] ?>"
                                                data-min-personnes="<?= $menu['nombre_personne_minimum'] ?? 2 ?>"
                                                <?= ($menuIdFromUrl == $menu['menu_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($menu['titre']) ?> - 
                                            <?= number_format($menu['prix_par_personne'], 2) ?> € /pers 
                                            (Min. <?= $menu['nombre_personne_minimum'] ?? 2 ?> pers.)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nombre_personnes" class="form-label">
                                    Nombre de personnes <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="nombre_personnes" name="nombre_personnes" 
                                       min="2" value="2" required>
                                <small class="form-text" id="min-personnes-info"></small>
                            </div>

                            <div class="alert alert-warning" id="reduction-alert" style="display: none;">
                                <i class="bi bi-percent"></i> <strong>Réduction de 10% appliquée !</strong><br>
                                Vous commandez pour 5 personnes ou plus au-dessus du minimum requis.
                            </div>
                        </div>

                        <!-- ÉTAPE 4 : BOISSONS -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-cup-straw"></i>Boissons (optionnel)
                            </h5>
                            
                            <div class="mb-3">
                                <label for="boisson_select" class="form-label">Ajouter des boissons</label>
                                <div class="input-group">
                                    <select class="form-select" id="boisson_select">
                                        <option value="">Choisir une boisson...</option>
                                        <?php if (!empty($boissons)): ?>
                                            <?php foreach ($boissons as $type => $listBoissons): ?>
                                                <optgroup label="<?= htmlspecialchars($type) ?>">
                                                    <?php foreach ($listBoissons as $boisson): ?>
                                                        <option value="<?= $boisson['boisson_id'] ?>" 
                                                                data-nom="<?= htmlspecialchars($boisson['nom']) ?>"
                                                                data-prix="<?= $boisson['prix_unitaire'] ?>"
                                                                data-contenance="<?= htmlspecialchars($boisson['contenance'] ?? '') ?>">
                                                            <?= htmlspecialchars($boisson['nom']) ?> 
                                                            (<?= htmlspecialchars($boisson['contenance'] ?? 'N/A') ?>) - 
                                                            <?= number_format($boisson['prix_unitaire'], 2) ?> €
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button type="button" class="btn btn-primary rounded-pill" id="btn_ajouter_boisson">
                                        <i class="bi bi-plus-lg"></i> Ajouter
                                    </button>
                                </div>
                            </div>
                            
                            <div id="liste_boissons" class="mt-3">
                                <!-- Les boissons ajoutées apparaîtront ici -->
                            </div>
                            
                            <div class="card bg-light" id="recap_boissons" style="display: none;">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>Total boissons :</strong>
                                        <span id="total_boissons_display">0,00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ÉTAPE 5 : MATÉRIEL -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-box-seam"></i>Prêt de matériel (optionnel)
                            </h5>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill"></i>
                                <strong>Important :</strong> Le matériel doit être restitué sous 10 jours 
                                (pénalité de 600€ après ce délai). La caution est restituable.
                            </div>
                            
                            <div class="mb-3">
                                <label for="materiel_select" class="form-label">Ajouter du matériel</label>
                                <div class="input-group">
                                    <select class="form-select" id="materiel_select">
                                        <option value="">Choisir du matériel...</option>
                                        <?php if (!empty($materiels)): ?>
                                            <?php foreach ($materiels as $categorie => $listMateriels): ?>
                                                <optgroup label="<?= htmlspecialchars($categorie) ?>">
                                                    <?php foreach ($listMateriels as $materiel): ?>
                                                        <option value="<?= $materiel['materiel_id'] ?>" 
                                                                data-nom="<?= htmlspecialchars($materiel['nom']) ?>"
                                                                data-caution="<?= $materiel['prix_caution'] ?>"
                                                                data-quantite-dispo="<?= $materiel['quantite_disponible'] ?>"
                                                                data-description="<?= htmlspecialchars($materiel['description'] ?? '') ?>">
                                                            <?= htmlspecialchars($materiel['nom']) ?> - 
                                                            Caution: <?= number_format($materiel['prix_caution'], 2) ?> € 
                                                            (<?= $materiel['quantite_disponible'] ?> dispo)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button type="button" class="btn btn-success rounded-pill" id="btn_ajouter_materiel">
                                        <i class="bi bi-plus-lg"></i> Ajouter
                                    </button>
                                </div>
                            </div>
                            
                            <div id="liste_materiel" class="mt-3">
                                <!-- Le matériel ajouté apparaîtra ici -->
                            </div>
                            
                            <div class="card bg-warning bg-opacity-10" id="recap_materiel" style="display: none;">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>Total caution à verser :</strong>
                                        <span id="total_caution_display">0,00 €</span>
                                    </div>
                                    <small class="text-muted">Restituable après retour du matériel en bon état</small>
                                </div>
                            </div>
                            
                            <input type="hidden" name="pret_materiel" id="pret_materiel" value="0">
                        </div>

                        <!-- RÉCAPITULATIF PRIX -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-calculator"></i>Récapitulatif de la commande
                            </h5>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-8"><strong>Prix du menu</strong></div>
                                        <div class="col-4 text-end">
                                            <span id="prix-menu-base">0,00</span> €
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2" id="reduction-row" style="display: none;">
                                        <div class="col-8 text-success">
                                            <i class="bi bi-tag-fill"></i> <strong>Réduction 10%</strong>
                                        </div>
                                        <div class="col-4 text-end text-success">
                                            - <span id="montant-reduction">0,00</span> €
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2" id="row-boissons" style="display: none;">
                                        <div class="col-8"><strong>Boissons</strong></div>
                                        <div class="col-4 text-end">
                                            <span id="montant-boissons">0,00</span> €
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-8"><strong>Frais de livraison</strong></div>
                                        <div class="col-4 text-end">
                                            <span id="frais-livraison">5,00</span> €
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-8"><h5 class="mb-0">TOTAL TTC</h5></div>
                                        <div class="col-4 text-end">
                                            <h5 class="mb-0 text-primary">
                                                <strong><span id="total-final">5,00</span> €</strong>
                                            </h5>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3 pt-3 border-top" id="row-caution" style="display: none;">
                                        <div class="col-8">
                                            <strong class="text-warning">Caution matériel</strong>
                                            <br><small class="text-muted">À verser séparément (restituable)</small>
                                        </div>
                                        <div class="col-4 text-end">
                                            <strong class="text-warning"><span id="montant-caution-final">0,00</span> €</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- BOUTONS ACTION -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/menus" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-arrow-left"></i> Retour aux menus
                            </a>
                            <button type="submit" class="btn btn-success btn-lg rounded-pill">
                                <i class="bi bi-check-circle"></i> Valider la commande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/commandes.js"></script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
