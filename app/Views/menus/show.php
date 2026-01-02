
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/menu-show.css">

<div class="container">
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb breadcrumb-vg">
            <li class="breadcrumb-item"><a href="/">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/menus">Menus</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($menu['titre']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5 mb-4 menu-carousel-col">
            <?php if (!empty($photos)): ?>
                <div id="menuCarousel" class="carousel slide shadow-lg rounded-3 overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($photos as $index => $photo): ?>
                            <button type="button" data-bs-target="#menuCarousel" data-bs-slide-to="<?= $index ?>" 
                                    <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> 
                                    aria-label="Slide <?= $index + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($photos as $index => $photo): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> carousel-item-clickable">
                                <img src="<?= htmlspecialchars($photo['image_url']) ?>" 
                                     class="d-block w-100 carousel-img-menu" 
                                     alt="<?= htmlspecialchars($photo['legende'] ?? $menu['titre']) ?>">
                                <?php if (!empty($photo['legende'])): ?>
                                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-2">
                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($photo['legende']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon carousel-control-icon-shadow" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon carousel-control-icon-shadow" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
                <p class="text-center mt-3 text-muted">
                    <i class="bi bi-zoom-in fs-5"></i> <strong>Cliquez sur l'image pour l'agrandir</strong>
                </p>
            <?php elseif (!empty($menu['image_url'])): ?>
                <img src="<?= htmlspecialchars($menu['image_url']) ?>" 
                     class="img-fluid rounded-3 shadow-lg menu-img-fallback" 
                     alt="<?= htmlspecialchars($menu['titre']) ?>"
                     onerror="this.src='https://via.placeholder.com/800x600?text=Menu'">
            <?php else: ?>
                <img src="https://via.placeholder.com/800x600?text=Menu" 
                     class="img-fluid rounded-3 shadow-lg menu-img-fallback" 
                     alt="<?= htmlspecialchars($menu['titre']) ?>">
            <?php endif; ?>
        </div>

        <div class="col-md-6 offset-md-1">
            <!-- En-tête avec titre et prix -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h2 class="mb-0"><?= htmlspecialchars($menu['titre']) ?></h2>
                <div class="text-end">
                    <span class="fs-3 fw-bold menu-prix-principal"><?= number_format($menu['prix_par_personne'], 2) ?> €<span class="text-muted menu-prix-par-personne">&nbsp;/&nbsp;personne</span></span>
                </div>
            </div>
            
            <!-- COMPOSITION DU MENU - Liste des plats -->
            <?php if (!empty($menu['plats'])): ?>
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header text-white menu-header-bordeaux">
                        <h5 class="mb-0">
                            <i class="bi bi-card-list"></i> Composition du menu
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $platsByType = [];
                        foreach ($menu['plats'] as $plat) {
                            $platsByType[$plat['type_plat']][] = $plat;
                        }
                        
                        $typeOrder = ['Entree' => 'Entrées', 'Plat' => 'Plats', 'Accompagnement' => 'Accompagnements', 'Dessert' => 'Desserts'];
                        
                        foreach ($typeOrder as $typeKey => $typeLabel):
                            if (!empty($platsByType[$typeKey])):
                        ?>
                            <h6 class="mt-3 mb-2 menu-titre-section">
                                <i class="bi bi-arrow-right-circle-fill"></i> <?= $typeLabel ?>
                            </h6>
                            <ul class="list-unstyled ms-3">
                                <?php foreach ($platsByType[$typeKey] as $plat): ?>
                                    <li class="mb-2">
                                        <strong><?= htmlspecialchars($plat['titre_plat']) ?></strong>
                                        <?php if (!empty($plat['description'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($plat['description']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($plat['allergenes'])): ?>
                                            <br><small class="text-danger menu-allergenes-petit">
                                                <i class="bi bi-exclamation-circle"></i> 
                                                Allergènes : <?= htmlspecialchars($plat['allergenes']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($menu['allergenes'])): ?>
                <div class="alert shadow-sm border-0 menu-allergenes">
                    <strong class="menu-allergenes-titre"><i class="bi bi-exclamation-triangle-fill"></i> Allergènes :</strong><br>
                    <span class="menu-allergenes-contenu"><?= nl2br(htmlspecialchars($menu['allergenes'])) ?></span>
                </div>
            <?php endif; ?>

            <!-- Options du menu -->
            <div class="card mb-4 shadow-lg border-0 menu-options-card">
                <!-- Boissons -->
                <?php if (!empty($boissons)): ?>
                    <div class="card mb-4 shadow-sm border-0 overflow-hidden">
                    <div class="card-header text-white menu-section-header menu-header-gradient">
                            <h6 class="mb-0"><i class="bi bi-cup-straw"></i> Ajouter des boissons</h6>
                            <i class="bi bi-chevron-down menu-chevron-icon"></i>
                        </div>
                    </div>
                    <div class="collapse show" id="collapseBoissons">
                        <div class="card-body menu-card-body-boisson">
                            <div class="row g-3">
                            <?php foreach ($boissons as $type => $listBoissons): ?>
                                <?php foreach ($listBoissons as $boisson): ?>
                                    <div class="col-md-6 d-flex">
                                        <div class="form-check position-relative flex-fill menu-option-box">
                                            <label class="form-check-label w-100 d-block" for="boisson_<?= $boisson['boisson_id'] ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <strong class="menu-option-nom"><?= htmlspecialchars($boisson['nom']) ?></strong>
                                                    <span class="ms-3 flex-shrink-0 menu-option-prix"><?= number_format($boisson['prix_unitaire'], 2) ?> €</span>
                                                </div>
                                                <?php if (!empty($boisson['description'])): ?>
                                                    <div class="mb-1 menu-option-description"><?= htmlspecialchars($boisson['description']) ?></div>
                                                <?php endif; ?>
                                                <small class="text-muted menu-option-contenance"><?= htmlspecialchars($boisson['contenance']) ?></small>
                                            </label>
                                              <input class="form-check-input boisson-checkbox checkbox-boisson" 
                                                  type="checkbox" 
                                                  value="<?= $boisson['boisson_id'] ?>"
                                                  id="boisson_<?= $boisson['boisson_id'] ?>"
                                                  data-nom="<?= htmlspecialchars($boisson['nom']) ?>"
                                                  data-prix="<?= $boisson['prix_unitaire'] ?>"
                                                  data-contenance="<?= htmlspecialchars($boisson['contenance']) ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Récapitulatif boissons -->
                        <div id="recapBoissons" class="alert mt-3 menu-recap d-none">
                            <h6><i class="bi bi-basket"></i> Boissons sélectionnées :</h6>
                            <ul id="listeBoissonsSelectionnees" class="mb-2"></ul>
                            <hr>
                            <strong>Total boissons : <span id="totalBoissons">0.00</span> €</strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Matériel -->
            <?php if (!empty($materiels)): ?>
                <div class="card mb-4 shadow-lg border-0 overflow-hidden">
                    <div class="card-header text-white menu-section-header" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseMateriel" 
                         aria-expanded="false" 
                         aria-controls="collapseMateriel">
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <h6 class="mb-0"><i class="bi bi-box-seam"></i> Louer du matériel</h6>
                            <i class="bi bi-chevron-down menu-chevron-icon"></i>
                        </div>
                    </div>
                    <div class="collapse" id="collapseMateriel">
                        <div class="card-body menu-card-body-materiel">
                            <p class="mb-4 menu-info-box">
                                <i class="bi bi-info-circle-fill menu-info-icon"></i> 
                                <span>Le matériel doit être restitué sous 10 jours</span>
                                <span class="text-muted"> (pénalité de 600€ après ce délai)</span>
                            </p>
                            <div class="row g-3">
                            <?php foreach ($materiels as $categorie => $listMateriels): ?>
                                <?php foreach ($listMateriels as $materiel): ?>
                                    <div class="col-md-6 d-flex">
                                        <div class="form-check position-relative flex-fill menu-option-box">
                                            <label class="form-check-label w-100 d-block" for="materiel_<?= $materiel['materiel_id'] ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <strong class="menu-option-nom"><?= htmlspecialchars($materiel['nom']) ?></strong>
                                                    <small class="ms-3 flex-shrink-0 menu-option-caution">Caution: <?= number_format($materiel['prix_caution'], 2) ?> €</small>
                                                </div>
                                                <?php if (!empty($materiel['description'])): ?>
                                                    <div class="mb-1 menu-option-description"><?= htmlspecialchars($materiel['description']) ?></div>
                                                <?php endif; ?>
                                                <small class="text-muted menu-option-quantite"><?= $materiel['quantite_disponible'] ?> disponible<?= $materiel['quantite_disponible'] > 1 ? 's' : '' ?></small>
                                            </label>
                                              <input class="form-check-input materiel-checkbox checkbox-materiel" 
                                                  type="checkbox" 
                                                  value="<?= $materiel['materiel_id'] ?>"
                                                  id="materiel_<?= $materiel['materiel_id'] ?>"
                                                  data-nom="<?= htmlspecialchars($materiel['nom']) ?>"
                                                  data-caution="<?= $materiel['prix_caution'] ?>"
                                                  data-quantite="<?= $materiel['quantite_disponible'] ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>

                            <!-- Récapitulatif matériel -->
                            <div id="recapMateriel" class="alert mt-3 menu-recap d-none">
                                <h6><i class="bi bi-basket"></i> Matériel sélectionné :</h6>
                                <ul id="listeMaterielSelectionne" class="mb-2"></ul>
                                <hr>
                                <strong>Total caution : <span id="totalCaution">0.00</span> €</strong>
                                <br><small class="text-muted">La caution vous sera restituée après retour du matériel en bon état.</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CONDITIONS DU MENU - Affichage permanent -->
            <div class="card border-start border-4 shadow-sm mb-3 menu-conditions-card">
                <div class="card-body py-2 px-3 menu-conditions-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check-fill me-2 pulse-animation menu-conditions-icone"></i>
                        <div class="flex-grow-1">
                            <strong class="menu-conditions-titre">Commande à prévoir :</strong>
                            <span class="ms-1 menu-conditions-texte">
                                <?php if (!empty($menu['conditions'])): ?>
                                    <?= htmlspecialchars($menu['conditions']) ?>
                                <?php else: ?>
                                    Merci de commander au moins 48h à l'avance pour garantir la disponibilité et la fraîcheur des produits.
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            </div>
            <!-- Fin des options -->
            
            <!-- Bouton commander -->
            <div class="text-center mt-4 py-3 menu-btn-commande-wrap">
                <?php 
                use App\Core\Session;
                $isAuthenticated = Session::get('user_id') !== null;
                ?>
                
                <?php if ($isAuthenticated): ?>
                    <a href="/commande/create?menu_id=<?= $menu['menu_id'] ?>" 
                       id="btnCommander" 
                       class="btn shadow-sm menu-btn-commande">
                        <i class="bi bi-cart-plus"></i> Commander ce menu
                    </a>
                    <p class="text-muted mt-2 small">
                        <i class="bi bi-info-circle"></i> Vous serez redirigé vers le formulaire de commande
                    </p>
                <?php else: ?>
                    <button type="button" 
                            id="btnCommander" 
                            class="btn shadow-sm menu-btn-commande"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalAuthentification">
                        <i class="bi bi-cart-plus"></i> Commander ce menu
                    </button>
                    <p class="text-muted mt-2 small">
                        <i class="bi bi-lock"></i> Connexion requise pour commander
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Bouton retour -->
            <div class="text-center mt-3 py-3 menu-btn-retour-wrap">
                <a href="/menus" class="btn btn-sm menu-btn-retour">
                    <i class="bi bi-arrow-left"></i> Retour aux menus
                </a>
            </div>
        </div>
    </div>

    <!-- Bloc avis utilisateurs (placeholder) -->
    <div class="row mt-5">
        <div class="col-12">
            <h3><i class="bi bi-star-fill text-warning"></i> Avis utilisateurs</h3>
            <p class="text-muted">Les avis utilisateurs seront bientôt disponibles...</p>
        </div>
    </div>
</div>

<!-- Modal demande authentification pour visiteurs non connectés -->
<div class="modal fade" id="modalAuthentification" tabindex="-1" aria-labelledby="modalAuthLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAuthLabel">
                    <i class="bi bi-lock-fill"></i> Connexion requise
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                </div>
                <h5 class="mb-3">Pour commander ce menu, vous devez être connecté</h5>
                <p class="text-muted mb-4">
                    Connectez-vous à votre compte ou créez-en un nouveau pour passer commande.
                </p>
                <div class="d-grid gap-2">
                    <a href="/login?redirect=/menu?id=<?= $menu['menu_id'] ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                    <a href="/register?redirect=/menu?id=<?= $menu['menu_id'] ?>" class="btn btn-outline-success btn-lg">
                        <i class="bi bi-person-plus"></i> Créer un compte
                    </a>
                </div>
                <hr class="my-4">
                <p class="text-muted small mb-0">
                    <i class="bi bi-shield-check"></i> Vos informations sont sécurisées et protégées
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox pour agrandir les photos -->
<div id="lightbox" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightboxTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div id="lightboxCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <?php if (!empty($photos)): ?>
                            <?php foreach ($photos as $index => $photo): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= htmlspecialchars($photo['image_url']) ?>" 
                                         class="d-block w-100 lightbox-img"
                                         alt="<?= htmlspecialchars($photo['legende'] ?? $menu['titre']) ?>">
                                    <?php if (!empty($photo['legende'])): ?>
                                        <div class="carousel-caption">
                                            <p class="bg-dark bg-opacity-75 d-inline-block px-3 py-2 rounded"><?= htmlspecialchars($photo['legende']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (count($photos) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#lightboxCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Précédent</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#lightboxCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Suivant</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
