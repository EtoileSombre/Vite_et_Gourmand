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
                            <td>#<?= htmlspecialchars($commande['id']) ?></td>
                            <td><?= htmlspecialchars($commande['menu_nom']) ?></td>
                            <td><?= htmlspecialchars($commande['quantite']) ?></td>
                            <td><?= date('d/m/Y', strtotime($commande['date_livraison'])) ?></td>
                            <td>
                                <?php
                                $statutClass = match($commande['statut']) {
                                    'en_attente' => 'warning',
                                    'validee' => 'success',
                                    'annulee' => 'danger',
                                    'livree' => 'info',
                                    default => 'secondary'
                                };
                                $statutText = match($commande['statut']) {
                                    'en_attente' => 'En attente',
                                    'validee' => 'Validée',
                                    'annulee' => 'Annulée',
                                    'livree' => 'Livrée',
                                    default => $commande['statut']
                                };
                                ?>
                                <span class="badge bg-<?= $statutClass ?>"><?= $statutText ?></span>
                            </td>
                            <td><?= number_format($commande['menu_prix'] * $commande['quantite'], 2) ?> €</td>
                            <td>
                                <?php if ($commande['statut'] === 'en_attente'): ?>
                                    <a href="/commande/modifier?id=<?= $commande['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                    <a href="/commande/annuler?id=<?= $commande['id'] ?>" class="btn btn-sm btn-danger" 
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
