<?php
/**
 * Dashboard Employé
 * Vue d'ensemble et accès rapide aux fonctions
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="bi bi-clipboard-check"></i> Dashboard Employé</h1>
            <p class="text-muted">Bonjour <strong><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Employé') ?></strong>, bienvenue dans votre espace de travail.</p>
        </div>
        <div class="text-end">
            <?php
            // Remplacer strftime() dépréciée par date() avec traduction manuelle
            $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
            $jourSemaine = $jours[date('w')];
            $numeroJour = date('d');
            $nomMois = $mois[date('n') - 1];
            $annee = date('Y');
            ?>
            <small class="text-muted d-block"><i class="bi bi-calendar3"></i> <?= "$jourSemaine $numeroJour $nomMois $annee" ?></small>
            <small class="text-muted"><i class="bi bi-clock"></i> <?= date('H:i') ?></small>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row g-4 mb-4">
        <!-- Commandes en attente -->
        <div class="col-md-4">
            <div class="card border-warning shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split text-warning fs-1"></i>
                    <h2 class="mt-3 mb-1"><?= $stats['commandes_en_attente'] ?></h2>
                    <p class="text-muted mb-3">Commandes à traiter</p>
                    <a href="/employe/commandes" class="btn btn-warning btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Gérer
                    </a>
                </div>
            </div>
        </div>

        <!-- Commandes du jour -->
        <div class="col-md-4">
            <div class="card border-primary shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check text-primary fs-1"></i>
                    <h2 class="mt-3 mb-1"><?= $stats['commandes_aujourdhui'] ?></h2>
                    <p class="text-muted mb-3">Prestations aujourd'hui</p>
                    <a href="/employe/commandes?filter=aujourdhui" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye"></i> Voir
                    </a>
                </div>
            </div>
        </div>

        <!-- Avis à modérer -->
        <div class="col-md-4">
            <div class="card border-info shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-star-half text-info fs-1"></i>
                    <h2 class="mt-3 mb-1"><?= $stats['avis_a_moderer'] ?></h2>
                    <p class="text-muted mb-3">Avis en attente</p>
                    <a href="/employe/avis" class="btn btn-info btn-sm text-white">
                        <i class="bi bi-check2-square"></i> Modérer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Accès rapides -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Accès Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="/employe/commandes" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-box-seam fs-4 d-block mb-2"></i>
                                Gérer les Commandes
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/employe/avis" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-star fs-4 d-block mb-2"></i>
                                Modérer les Avis
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/menus" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-card-list fs-4 d-block mb-2"></i>
                                Gérer les Menus
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/profil" class="btn btn-outline-secondary w-100 py-3">
                                <i class="bi bi-person-circle fs-4 d-block mb-2"></i>
                                Mon Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières commandes en attente -->
    <?php if (!empty($commandesEnAttente)): ?>
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Commandes en Attente (<?= count($commandesEnAttente) ?> premières)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Commande</th>
                                    <th>Client</th>
                                    <th>Menu</th>
                                    <th>Pers.</th>
                                    <th>Date prestation</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commandesEnAttente as $cmd): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cmd['numero_commande']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($cmd['client_prenom'] ?? 'N/A') ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($cmd['client_email'] ?? '') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($cmd['menu_nom'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($cmd['nombre_personne'] ?? 0) ?></td>
                                    <td>
                                        <?php 
                                        $datePresta = $cmd['date_prestation'] ? date('d/m/Y', strtotime($cmd['date_prestation'])) : 'N/A';
                                        echo $datePresta;
                                        ?>
                                        <?php if ($cmd['heure_livraison']): ?>
                                            <br><small class="text-muted"><i class="bi bi-clock"></i> <?= htmlspecialchars($cmd['heure_livraison']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'en attente' => 'warning',
                                            'validée' => 'success',
                                            'en préparation' => 'info'
                                        ];
                                        $statut = $cmd['statut'] ?? 'en attente';
                                        $badgeClass = $badges[$statut] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars($statut) ?></span>
                                    </td>
                                    <td>
                                        <a href="/employe/commandes?id=<?= $cmd['numero_commande'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i> Traiter
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($stats['commandes_en_attente'] > 5): ?>
                        <div class="text-center mt-3">
                            <a href="/employe/commandes" class="btn btn-warning">
                                Voir toutes les commandes en attente (<?= $stats['commandes_en_attente'] ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Derniers avis à modérer -->
    <?php if (!empty($avisEnAttente)): ?>
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text"></i> Avis en Attente de Modération (<?= count($avisEnAttente) ?> premiers)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($avisEnAttente as $avis): ?>
                        <div class="col-md-4">
                            <div class="card h-100 border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong><?= htmlspecialchars($avis['client_prenom'] ?? 'Client') ?></strong>
                                        <div>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star-fill <?= $i <= ($avis['note'] ?? 0) ? 'text-warning' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-envelope"></i> <?= htmlspecialchars($avis['client_email'] ?? '') ?>
                                    </p>
                                    <p class="card-text"><?= htmlspecialchars($avis['description'] ?? 'Aucun commentaire') ?></p>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> 
                                        <?= $avis['created_at'] ? date('d/m/Y H:i', strtotime($avis['created_at'])) : 'N/A' ?>
                                    </small>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="/employe/avis?id=<?= $avis['avis_id'] ?>" class="btn btn-sm btn-info w-100">
                                        <i class="bi bi-check-circle"></i> Modérer
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($stats['avis_a_moderer'] > 3): ?>
                        <div class="text-center mt-3">
                            <a href="/employe/avis" class="btn btn-info text-white">
                                Voir tous les avis (<?= $stats['avis_a_moderer'] ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Message si aucune tâche -->
    <?php if (empty($commandesEnAttente) && empty($avisEnAttente)): ?>
    <div class="alert alert-success text-center py-5" role="alert">
        <i class="bi bi-check-circle fs-1 d-block mb-3"></i>
        <h4>Tout est à jour ! 🎉</h4>
        <p class="mb-0">Aucune commande ni avis en attente pour le moment.</p>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
