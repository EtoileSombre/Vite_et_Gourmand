<?php 
use App\Core\Session;

// Récupérer les paramètres depuis l'URL
$menuIdFromUrl = $_GET['menu_id'] ?? null;
$boissonsFromUrl = $_GET['boissons'] ?? '';
$materielsFromUrl = $_GET['materiels'] ?? '';
$boissonsIds = $boissonsFromUrl ? explode(',', $boissonsFromUrl) : [];
$materielsIds = $materielsFromUrl ? explode(',', $materielsFromUrl) : [];
?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

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
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-cart-plus"></i> Nouvelle commande
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="/commande/nouvelle" id="formCommande">
                        
                        <!-- ÉTAPE 1 : INFORMATIONS CLIENT (auto-remplies) -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-person-circle"></i> 1. Vos informations
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?= htmlspecialchars($user['nom'] ?? '') ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" 
                                           value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="telephone" class="form-label">Numéro de GSM <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                                           value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" 
                                           readonly style="background-color: #f8f9fa;">
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
                                <i class="bi bi-calendar-event"></i> 2. Détails de la prestation
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
                                       placeholder="12 Rue de la Paix, 33000 Bordeaux" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ville_livraison" class="form-label">Ville de livraison <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ville_livraison" name="ville_livraison" 
                                           placeholder="Bordeaux" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="code_postal_livraison" class="form-label">Code postal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code_postal_livraison" name="code_postal_livraison" 
                                           pattern="[0-9]{5}" placeholder="33000" required>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <strong><i class="bi bi-truck"></i> Frais de livraison :</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>À Bordeaux</strong> : 5,00 € forfaitaire</li>
                                    <li><strong>Hors Bordeaux</strong> : 5,00 € + 0,59 € par kilomètre parcouru</li>
                                </ul>
                            </div>
                            
                            <div class="mb-3" id="distance-group" style="display: none;">
                                <label for="distance_km" class="form-label">Distance depuis Bordeaux (en km) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="distance_km" name="distance_km" 
                                       min="0" step="0.1" value="0">
                                <small class="form-text text-muted">Estimez la distance en km depuis le centre de Bordeaux</small>
                            </div>
                        </div>

                        <!-- ÉTAPE 3 : CHOIX DU MENU -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-card-list"></i> 3. Choix du menu
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

                        <!-- RÉCAPITULATIF PRIX EN TEMPS RÉEL -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-calculator"></i> 4. Récapitulatif détaillé
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
                                </div>
                            </div>
                        </div>

                        <!-- Champs cachés pour les boissons et matériel -->
                        <?php if (!empty($boissonsIds)): ?>
                            <?php foreach ($boissonsIds as $boissonId): ?>
                                <input type="hidden" name="boissons[]" value="<?= htmlspecialchars($boissonId) ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($materielsIds)): ?>
                            <?php foreach ($materielsIds as $materielId): ?>
                                <input type="hidden" name="materiels[]" value="<?= htmlspecialchars($materielId) ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- BOUTONS ACTION -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/menus" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Retour aux menus
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Valider la commande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuSelect = document.getElementById('menu_id');
    const nbPersonnesInput = document.getElementById('nombre_personnes');
    const villeLivraisonInput = document.getElementById('ville_livraison');
    const distanceInput = document.getElementById('distance_km');
    const distanceGroup = document.getElementById('distance-group');
    
    const prixMenuBase = document.getElementById('prix-menu-base');
    const montantReduction = document.getElementById('montant-reduction');
    const fraisLivraison = document.getElementById('frais-livraison');
    const totalFinal = document.getElementById('total-final');
    
    const reductionAlert = document.getElementById('reduction-alert');
    const reductionRow = document.getElementById('reduction-row');
    const minPersonnesInfo = document.getElementById('min-personnes-info');
    
    // Fonction calcul en temps réel
    function calculerPrix() {
        const menuOption = menuSelect.options[menuSelect.selectedIndex];
        if (!menuOption || !menuOption.value) {
            return;
        }
        
        const prixParPersonne = parseFloat(menuOption.dataset.prix) || 0;
        const minPersonnes = parseInt(menuOption.dataset.minPersonnes) || 2;
        const nbPersonnes = parseInt(nbPersonnesInput.value) || 0;
        
        // Validation minimum personnes
        nbPersonnesInput.min = minPersonnes;
        minPersonnesInfo.textContent = `Minimum ${minPersonnes} personnes pour ce menu`;
        
        if (nbPersonnes < minPersonnes) {
            minPersonnesInfo.classList.add('text-danger');
            return;
        } else {
            minPersonnesInfo.classList.remove('text-danger');
        }
        
        // 1. Prix de base
        const prixBase = prixParPersonne * nbPersonnes;
        prixMenuBase.textContent = prixBase.toFixed(2);
        
        // 2. Réduction 10% si ≥ (min + 5)
        let reduction = 0;
        if (nbPersonnes >= (minPersonnes + 5)) {
            reduction = prixBase * 0.10;
            montantReduction.textContent = reduction.toFixed(2);
            reductionAlert.style.display = 'block';
            reductionRow.style.display = 'flex';
        } else {
            reductionAlert.style.display = 'none';
            reductionRow.style.display = 'none';
        }
        
        // 3. Frais livraison
        const ville = villeLivraisonInput.value.toLowerCase().trim();
        let frais = 5.00;
        
        if (ville === 'bordeaux') {
            frais = 5.00;
            distanceGroup.style.display = 'none';
            distanceInput.value = 0;
        } else {
            const distance = parseFloat(distanceInput.value) || 0;
            frais = 5.00 + (distance * 0.59);
            distanceGroup.style.display = 'block';
        }
        
        fraisLivraison.textContent = frais.toFixed(2);
        
        // 4. Total final
        const total = (prixBase - reduction) + frais;
        totalFinal.textContent = total.toFixed(2);
    }
    
    // Événements
    menuSelect.addEventListener('change', calculerPrix);
    nbPersonnesInput.addEventListener('input', calculerPrix);
    villeLivraisonInput.addEventListener('input', calculerPrix);
    distanceInput.addEventListener('input', calculerPrix);
    
    // Calcul initial si menu pré-sélectionné
    if (menuSelect.value) {
        calculerPrix();
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
