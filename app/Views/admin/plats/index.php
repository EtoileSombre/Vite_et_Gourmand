<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<main role="main" id="main-content">
    <div class="container py-5">
        
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-egg-fried"></i> Gestion des plats</h1>
            <div>
                <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux me-2 rounded-pill">
                    <i class="bi bi-arrow-left"></i> Retour Dashboard
                </a>
                <a href="/admin/plats/create" class="btn btn-vg-gold rounded-pill">
                    <i class="bi bi-plus-circle"></i> Créer un plat
                </a>
            </div>
        </div>

        <!-- Messages flash -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                        <strong><?= htmlspecialchars($plat->getTitrePlat()) ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(substr($plat->getDescription() ?? '', 0, 100)) ?>
                                            <?= strlen($plat->getDescription() ?? '') > 100 ? '...' : '' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($plat->getTypePlat()) {
                                            'Entrée' => 'bg-success',
                                            'Plat' => 'bg-primary',
                                            'Dessert' => 'bg-warning',
                                            'Accompagnement' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($plat->getTypePlat()) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="/admin/plats/edit?id=<?= $plat->getPlatId() ?>" 
                                               class="btn btn-action-circle btn-outline-vg-bordeaux" 
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                                <span class="visually-hidden">Modifier <?= htmlspecialchars($plat->getTitrePlat()) ?></span>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-action-circle btn-outline-vg-bordeaux" 
                                                    title="Supprimer"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deletePlatModal"
                                                    data-plat-id="<?= $plat->getPlatId() ?>"
                                                    data-plat-titre="<?= htmlspecialchars($plat->getTitrePlat()) ?>">
                                                <i class="bi bi-trash"></i>
                                                <span class="visually-hidden">Supprimer <?= htmlspecialchars($plat->getTitrePlat()) ?></span>
                                            </button>
                                        </div>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deletePlatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le plat <strong id="platTitreToDelete"></strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="/admin/plats/delete" id="deletePlatForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="plat_id" id="platIdToDelete">
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion du modal de suppression de plat
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deletePlatModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const platId = button.getAttribute('data-plat-id');
            const platTitre = button.getAttribute('data-plat-titre');
            
            document.getElementById('platIdToDelete').value = platId;
            document.getElementById('platTitreToDelete').textContent = platTitre;
        });
    }
});
</script>

<?php 
$additionalScripts = ['/assets/js/admin-plats.js'];
require_once __DIR__ . '/../../layouts/footer.php'; 
?>
