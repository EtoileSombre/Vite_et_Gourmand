<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<main role="main" id="main-content">
    <div class="container py-5">
        
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1"><i class="bi bi-egg-fried"></i> Gestion des plats</h1>
                <p class="text-muted mb-0">Créez et gérez votre catalogue de plats</p>
            </div>
            <div>
                <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux me-2 rounded-pill">
                    <i class="bi bi-arrow-left"></i> Retour Dashboard
                </a>
                <a href="/admin/plats/create" class="btn btn-vg-gold rounded-pill">
                    <i class="bi bi-plus-circle"></i> Créer un plat
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <?php if (!empty($stats)): ?>
        <div class="row mb-4">
            <?php foreach ($stats as $stat): ?>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-0"><?= $stat['total'] ?></h3>
                        <small class="text-muted"><?= htmlspecialchars($stat['type_plat']) ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="/admin/plats" class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Filtrer par type</label>
                        <select name="type" id="type" class="form-select" data-auto-submit>
                            <option value="">Tous les types</option>
                            <?php foreach ($typesPlat as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= $typeFiltre === $type ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <?php if ($typeFiltre): ?>
                            <a href="/admin/plats" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des plats -->
        <?php if (empty($plats)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Aucun plat <?= $typeFiltre ? "de type « $typeFiltre »" : '' ?> n'est enregistré.
                <a href="/admin/plats/create">Créez votre premier plat</a>.
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="w-30">Titre</th>
                                    <th scope="col" class="w-40">Description</th>
                                    <th scope="col" class="w-15">Type</th>
                                    <th scope="col" class="w-15 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plats as $plat): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($plat['titre_plat']) ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(substr($plat['description'] ?? '', 0, 100)) ?>
                                            <?= strlen($plat['description'] ?? '') > 100 ? '...' : '' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($plat['type_plat']) {
                                            'Entrée' => 'bg-success',
                                            'Plat' => 'bg-primary',
                                            'Dessert' => 'bg-warning',
                                            'Accompagnement' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($plat['type_plat']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/plats/edit?id=<?= $plat['plat_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary rounded-pill" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                            <span class="visually-hidden">Modifier <?= htmlspecialchars($plat['titre_plat']) ?></span>
                                        </a>
                                        
                                        <form method="POST" 
                                              action="/admin/plats/delete" 
                                              class="d-inline" 
                                              data-confirm="Êtes-vous sûr de vouloir supprimer ce plat ?">
                                            <input type="hidden" name="plat_id" value="<?= $plat['plat_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                                <span class="visually-hidden">Supprimer <?= htmlspecialchars($plat['titre_plat']) ?></span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php 
$additionalScripts = ['/assets/js/admin-plats.js'];
require_once __DIR__ . '/../../layouts/footer.php'; 
?>
