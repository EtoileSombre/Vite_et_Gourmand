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
                            <th>Titre</th>
                            <th>Prix/personne</th>
                            <th>Nb pers. min</th>
                            <th>Stock</th>
                            <th class="text-end">Actions</th>
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
                                        <strong><?= htmlspecialchars($menu['titre']) ?></strong>
                                        <?php if (!empty($menu['description'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($menu['description'], 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= number_format($menu['prix_par_personne'], 2) ?> €</strong></td>
                                    <td><?= htmlspecialchars($menu['nombre_personne_minimum'] ?? 1) ?> pers.</td>
                                    <td>
                                        <?php
                                        $stock = (int)$menu['quantite_restante'];
                                        if ($stock > 20) {
                                            echo "<span class='badge bg-success'>$stock</span>";
                                        } elseif ($stock > 0) {
                                            echo "<span class='badge bg-warning text-dark'>$stock</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>0</span>";
                                        }
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/menus/edit?id=<?= $menu['menu_id'] ?>" 
                                               class="btn btn-outline-primary rounded-pill" 
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <?php if ($menu['quantite_restante'] > 0): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-danger rounded-pill" 
                                                        title="Désactiver"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteMenuModal"
                                                        data-menu-id="<?= $menu['menu_id'] ?>"
                                                        data-menu-titre="<?= htmlspecialchars($menu['titre']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <form method="POST" action="/admin/menus/activate" class="d-inline">
                                                    <input type="hidden" name="menu_id" value="<?= $menu['menu_id'] ?>">
                                                    <button type="submit" 
                                                            class="btn btn-outline-success rounded-pill" 
                                                            title="Réactiver">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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

<!-- Modal unique de confirmation de désactivation -->
<div class="modal fade" id="deleteMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la désactivation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous vraiment désactiver le menu <strong id="menuTitreToDelete"></strong> ?</p>
                <p class="text-muted small">Le menu ne sera plus visible par les utilisateurs mais restera dans la base de données.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="/admin/menus/delete" id="deleteMenuForm">
                    <input type="hidden" name="menu_id" id="menuIdToDelete">
                    <button type="submit" class="btn btn-danger rounded-pill">Désactiver</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion du modal unique pour désactivation de menu
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
