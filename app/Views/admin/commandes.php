<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5">
    <h2>Gestion des commandes</h2>
    <a href="/admin" class="btn btn-secondary mb-3">← Retour au dashboard</a>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Utilisateur</th>
                    <th>Date de prestation</th>
                    <th>Statut</th>
                    <th>Total TTC</th>
                    <th>Date de commande</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($commande->getNumeroCommande()) ?></td>
                        <td><?= htmlspecialchars($commande->getUtilisateurPrenom() . ' ' . $commande->getUtilisateurNom()) ?></td>
                        <td><?= date('d/m/Y', strtotime($commande->getDatePrestation())) ?></td>
                        <td>
                            <?php
                            $statut = $commande->getStatut();
                            $statutLabel = $statuts[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
                            $badgeClass = 'badge-statut-' . str_replace('_', '-', $statut);
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                        </td>
                        <td><?= number_format($commande->getTotalFinal(), 2, ',', ' ') ?> €</td>
                        <td><?= date('d/m/Y H:i', strtotime($commande->getDateCommande())) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
