
<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/pages/menu-show.css">

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
                                            <br><small class="text-secondary menu-allergenes-petit">
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
                <div class="alert shadow-sm border-0 menu-allergenes mb-1">
                    <strong class="menu-allergenes-titre text-secondary"><i class="bi bi-exclamation-triangle-fill"></i> Allergènes :</strong><br>
                    <span class="menu-allergenes-contenu text-secondary"><?= nl2br(htmlspecialchars($menu['allergenes'])) ?></span>
                </div>
            <?php endif; ?>

            <!-- Bouton commander + Info commande côte à côte -->
            <div class="row align-items-center justify-content-center mt-1 mb-2 g-3">
                <!-- Bouton commander -->
                <div class="col-lg-6">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <?php 
                        use App\Core\Session;
                        $isAuthenticated = Session::get('user_id') !== null;
                        ?>
                        
                        <?php if ($isAuthenticated): ?>
                            <a href="/commande/nouvelle?menu_id=<?= $menu['menu_id'] ?>" 
                               id="btnCommander" 
                               class="btn shadow-sm menu-btn-commande rounded-pill px-4 py-2">
                                <i class="bi bi-cart-plus"></i> Commander
                            </a>
                        <?php else: ?>
                            <button type="button" 
                                    id="btnCommander" 
                                    class="btn shadow-sm menu-btn-commande rounded-pill px-4 py-2"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalAuthentification">
                                <i class="bi bi-cart-plus"></i> Commander
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info commande à prévoir (texte simple) -->
                <div class="col-lg-6">
                    <div class="d-flex flex-column justify-content-center h-100 text-center text-lg-start ps-lg-3">
                        <p class="mb-1 small">
                            <i class="bi bi-calendar-event text-vg-bordeaux me-1"></i>
                            <strong class="text-vg-bordeaux">Commande à prévoir :</strong>
                        </p>
                        <p class="mb-0 small text-muted">
                            <?php if (!empty($menu['conditions'])): ?>
                                <?= htmlspecialchars($menu['conditions']) ?>
                            <?php else: ?>
                                Commande 48h avant pour garantir la disponibilité des produits frais.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informations sur les options disponibles -->
            <div class="card shadow-sm border-0 mt-0">
                <div class="card-header bg-vg-bordeaux text-white border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle-fill"></i> Options disponibles à la commande
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if (!empty($boissons)): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cup-straw fs-4 text-vg-bordeaux me-3"></i>
                                    <div>
                                        <strong>Boissons disponibles</strong>
                                        <p class="mb-0 small text-muted">Vous pourrez ajouter des boissons lors de la commande</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($materiels)): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam fs-4 text-success me-3"></i>
                                    <div>
                                        <strong>Prêt de matériel</strong>
                                        <p class="mb-0 small text-muted">Matériel disponible avec caution restituable</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            </div>
            <!-- Fin des options -->
        </div>
    </div>
</div>

<!-- Modal demande authentification pour visiteurs non connectés -->
<div class="modal fade" id="modalAuthentification" tabindex="-1" aria-labelledby="modalAuthLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-bordeaux text-white">
                <h5 class="modal-title" id="modalAuthLabel">
                    <i class="bi bi-lock-fill"></i> Connexion requise
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-person-circle fs-1 text-vg-bordeaux"></i>
                </div>
                <h5 class="mb-3">Vous devez être connecté</h5>
                <p class="text-muted mb-4">
                    Connectez-vous ou créez un compte pour passer commande.
                </p>
                <div class="d-grid gap-2">
                    <a href="/login?redirect=/menu?id=<?= $menu['menu_id'] ?>" class="btn btn-vg-bordeaux btn-lg rounded-pill">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                    <a href="/register?redirect=/menu?id=<?= $menu['menu_id'] ?>" class="btn btn-vg-bordeaux btn-lg rounded-pill">
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

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
