<?php
/*Liste et gestion des menus par employés/administrateurs*/
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-card-list"></i> Gestion des Menus</h1>
        <div>
            <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux me-2 rounded-pill">
                <i class="bi bi-arrow-left"></i> Retour Dashboard
            </a>
            <a href="/admin/menus/create" class="btn btn-vg-gold rounded-pill">
                <i class="bi bi-plus-circle"></i> Créer un Menu
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Titre</th>
                            <th scope="col">Prix/personne</th>
                            <th scope="col">Nb pers. min</th>
                            <th scope="col">Stock</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($menus)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Aucun menu trouvé
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($menus as $menu): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($menu->getTitre()) ?></strong>
                                        <?php if (!empty($menu->getDescription())): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($menu->getDescription(), 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= number_format($menu->getPrixParPersonne(), 2) ?> €</strong></td>
                                    <td><?= htmlspecialchars($menu->getNombrePersonneMinimum() ?? 1) ?> pers.</td>
                                    <td>
                                        <?php
                                        $stock = (int)$menu->getQuantiteRestante();
                                        if ($stock > 20) {
                                            echo "<span class='badge bg-success'>$stock</span>";
                                        } elseif ($stock > 0) {
                                            echo "<span class='badge bg-warning text-dark'>$stock</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>0</span>";
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="/admin/menus/edit?id=<?= $menu->getMenuId() ?>" 
                                               class="btn btn-action-circle btn-outline-vg-bordeaux" 
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-action-circle btn-outline-vg-bordeaux" 
                                                    title="Supprimer"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteMenuModal"
                                                    data-menu-id="<?= $menu->getMenuId() ?>"
                                                    data-menu-titre="<?= htmlspecialchars($menu->getTitre()) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal unique de confirmation de suppression -->
<div class="modal fade" id="deleteMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le menu <strong id="menuTitreToDelete"></strong> ?</p>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="/admin/menus/delete" id="deleteMenuForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="menu_id" id="menuIdToDelete">
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion du modal unique pour suppression de menu
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteMenuModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const menuId = button.getAttribute('data-menu-id');
            const menuTitre = button.getAttribute('data-menu-titre');
            
            document.getElementById('menuIdToDelete').value = menuId;
            document.getElementById('menuTitreToDelete').textContent = menuTitre;
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
