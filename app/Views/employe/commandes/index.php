<?php 
$additionalStyles = ['/assets/css/pages/commandes.css'];
require_once __DIR__ . '/../../layouts/header.php'; 
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-basket"></i> Gestion des Commandes</h1>
        <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux rounded-pill">
            <i class="bi bi-arrow-left"></i> Retour Dashboard
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="/employe/commandes" id="filterForm" class="row g-4 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <select name="statut" id="statut" class="form-select">
                        <option value="all" <?= $filterStatut === 'all' ? 'selected' : '' ?>>📋 Tous les statuts</option>
                        <option value="en_attente" <?= $filterStatut === 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                        <option value="acceptee" <?= $filterStatut === 'acceptee' ? 'selected' : '' ?>>✅ Acceptée</option>
                        <option value="en_preparation" <?= $filterStatut === 'en_preparation' ? 'selected' : '' ?>>👨‍🍳 En préparation</option>
                        <option value="en_cours_livraison" <?= $filterStatut === 'en_cours_livraison' ? 'selected' : '' ?>>🚚 En cours de livraison</option>
                        <option value="livree" <?= $filterStatut === 'livree' ? 'selected' : '' ?>>📦 Livrée</option>
                        <option value="attente_retour_materiel" <?= $filterStatut === 'attente_retour_materiel' ? 'selected' : '' ?>>⏰ Attente retour matériel</option>
                        <option value="terminee" <?= $filterStatut === 'terminee' ? 'selected' : '' ?>>✔️ Terminée</option>
                        <option value="annulee" <?= $filterStatut === 'annulee' ? 'selected' : '' ?>>🚫 Annulée</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <input type="text" name="utilisateur" id="utilisateur" class="form-control" 
                           placeholder="🔍 Nom ou email de l'utilisateur..." value="<?= htmlspecialchars($filterUtilisateur) ?>">
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="filter" value="aujourdhui" id="aujourdhui" class="form-check-input"
                               <?= $filterAujourdhui ? 'checked' : '' ?>>
                        <label for="aujourdhui" class="form-check-label">
                            📅 Prestations aujourd'hui
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des commandes -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($commandes)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <p class="text-muted">Aucune commande trouvée avec ces filtres</p>
                </div>
            <?php else: ?>
                <table class="table table-hover table-striped table-sm table-commandes">
                    <thead>
                        <tr>
                            <th class="text-center">N° Commande</th>
                            <th class="text-center">Utilisateur</th>
                            <th class="text-center">Menu</th>
                            <th class="text-center">Date Commande</th>
                            <th class="text-center">Date Prestation</th>
                            <th class="text-center">Nb Pers.</th>
                            <th class="text-center">Total HT</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php 
                            foreach ($commandes as $cmd): 
                                $statut = $cmd['statut'];
                                $statutLabel = $statuts[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
                                $badgeClass = 'badge-statut-' . str_replace('_', '-', $statut);
                            ?>
                                <tr>
                                    <td class="text-center"><strong>#<?= htmlspecialchars($cmd['numero_commande']) ?></strong></td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($cmd['utilisateur_prenom'] ?? 'N/A') ?> 
                                        <?= htmlspecialchars($cmd['utilisateur_nom'] ?? '') ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($cmd['utilisateur_email'] ?? '') ?></small>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($cmd['menu_titre'] ?? 'N/A') ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></td>
                                    <td class="text-center">
                                        <?= date('d/m/Y', strtotime($cmd['date_prestation'])) ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($cmd['heure_livraison'] ?? '') ?></small>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($cmd['totalPersonnes'] ?? 0) ?></td>
                                    <td class="text-center">
                                        <?php $totalHT = ($cmd['total_final'] ?? 0) / 1.10; ?>
                                        <strong><?= number_format($totalHT, 2) ?> €</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $statutLabel ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="/employe/commandes/view?id=<?= $cmd['numero_commande'] ?>" 
                                           class="btn btn-sm btn-vg-bordeaux rounded-pill" title="Gérer la commande">
                                            <i class="bi bi-gear"></i> Gérer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <div class="mt-3">
                    <p class="text-muted mb-0">
                        <strong><?= count($commandes) ?></strong> commande(s) trouvée(s)
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$additionalScripts = ['/assets/js/employe-commandes.js'];
require_once __DIR__ . '/../../layouts/footer.php'; 
?>
