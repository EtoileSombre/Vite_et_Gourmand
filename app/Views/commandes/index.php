<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Mes commandes</h2>
    <a href="/commande/nouvelle" class="btn btn-success mb-3">Nouvelle commande</a>
    
    <?php if (empty($commandes)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore passé de commande.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Menu</th>
                        <th>Quantité</th>
                        <th>Date de livraison</th>
                        <th>Statut</th>
                        <th>Prix total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($commande['numero_commande'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($commande['menu_nom'] ?? 'Menu supprimé') ?></td>
                            <td><?= htmlspecialchars($commande['nombre_personne'] ?? 0) ?> pers.</td>
                            <td><?= $commande['date_prestation'] ? date('d/m/Y', strtotime($commande['date_prestation'])) : 'Non définie' ?></td>
                            <td>
                                <?php
                                $statut = $commande['statut'] ?? 'en attente';
                                $statutClass = match($statut) {
                                    'en attente' => 'warning',
                                    'validée' => 'success',
                                    'annulée' => 'danger',
                                    'livrée' => 'info',
                                    default => 'secondary'
                                };
                                $statutText = ucfirst($statut);
                                ?>
                                <span class="badge bg-<?= $statutClass ?>"><?= $statutText ?></span>
                            </td>
                            <td>
                                <?php 
                                $prixMenu = $commande['menu_prix'] ?? 0;
                                $nbPersonnes = $commande['nombre_personne'] ?? 0;
                                echo number_format($prixMenu * $nbPersonnes, 2, ',', ' ') . ' €';
                                ?>
                            </td>
                            <td>
                                <?php if ($statut === 'en attente'): ?>
                                    <a href="/commande/modifier?numero=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-sm btn-primary">Modifier</a>
                                    <a href="/commande/annuler?numero=<?= urlencode($commande['numero_commande']) ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">Annuler</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
