<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-card-list"></i> Nos Menus</h1>

    <?php if (empty($menus)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Aucun menu disponible pour le moment.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($menus as $menu): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($menu['image_url'])): ?>
                            <img src="<?= htmlspecialchars($menu['image_url']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($menu['titre']) ?>"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=Menu'">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x300?text=Menu" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($menu['titre']) ?>">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($menu['titre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($menu['description']) ?></p>
                            
                            <?php if (!empty($menu['categorie'])): ?>
                                <span class="badge bg-secondary mb-2">
                                    <?= htmlspecialchars($menu['categorie']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 mb-0 text-primary">
                                    <?= number_format($menu['prix_par_personne'], 2) ?> €
                                </span>
                                <a href="/index_mvc.php?url=menu&id=<?= $menu['menu_id'] ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i> Détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
