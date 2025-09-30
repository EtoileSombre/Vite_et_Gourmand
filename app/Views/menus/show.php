<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/menus">Menus</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($menu['titre']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <?php if (!empty($menu['image_url'])): ?>
                <img src="<?= htmlspecialchars($menu['image_url']) ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?= htmlspecialchars($menu['titre']) ?>"
                     onerror="this.src='https://via.placeholder.com/600x400?text=Menu'">
            <?php else: ?>
                <img src="https://via.placeholder.com/600x400?text=Menu" 
                     class="img-fluid rounded shadow" 
                     alt="<?= htmlspecialchars($menu['titre']) ?>">
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h1><?= htmlspecialchars($menu['titre']) ?></h1>
            
            <?php if (!empty($menu['categorie'])): ?>
                <span class="badge bg-secondary mb-3">
                    <?= htmlspecialchars($menu['categorie']) ?>
                </span>
            <?php endif; ?>

            <p class="lead"><?= htmlspecialchars($menu['description']) ?></p>

            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h3 class="text-primary mb-0">
                        <?= number_format($menu['prix_par_personne'], 2) ?> € / personne
                    </h3>
                </div>
            </div>

            <?php if (!empty($menu['ingredients'])): ?>
                <h5>Ingrédients :</h5>
                <p><?= nl2br(htmlspecialchars($menu['ingredients'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($menu['allergenes'])): ?>
                <div class="alert alert-warning">
                    <strong><i class="bi bi-exclamation-triangle"></i> Allergènes :</strong><br>
                    <?= nl2br(htmlspecialchars($menu['allergenes'])) ?>
                </div>
            <?php endif; ?>

            <div class="d-grid gap-2">
                <a href="/commande/nouvelle?menu_id=<?= $menu['menu_id'] ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-cart-plus"></i> Commander ce menu
                </a>
                <a href="/menus" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour aux menus
                </a>
            </div>
        </div>
    </div>

    <!-- Section avis (à implémenter plus tard) -->
    <div class="row mt-5">
        <div class="col-12">
            <h3><i class="bi bi-star-fill text-warning"></i> Avis clients</h3>
            <p class="text-muted">Les avis clients seront bientôt disponibles...</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
