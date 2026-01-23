<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="bi bi-speedometer2"></i> Administration - Dashboard</h1>
            <p class="text-muted">Bonjour <strong><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Administrateur') ?></strong>, bienvenue dans votre espace admin.</p>
        </div>
        <div class="text-end">
            <?php
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
        <!-- Employés -->
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm h-100 border-vg-bordeaux-2">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-1 text-vg-bordeaux"></i>
                    <h2 class="dashboard-number mt-3 mb-1"><?= $totalUsers ?></h2>
                    <p class="text-muted mb-3">Employés</p>
                    <a href="/admin/utilisateurs" class="btn btn-sm btn-vg-bordeaux rounded-pill">
                        <i class="bi bi-person-plus"></i> Créer
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques MongoDB -->
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm h-100 border-vg-bordeaux-2">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up fs-1 text-vg-bordeaux"></i>
                    <h2 class="dashboard-number mt-3 mb-1"><?= $totalCommandes ?></h2>
                    <p class="text-muted mb-3">Statistiques</p>
                    <a href="/admin/stats" class="btn btn-sm btn-vg-bordeaux rounded-pill">
                        <i class="bi bi-bar-chart"></i> Voir
                    </a>
                </div>
            </div>
        </div>

        <!-- Menus -->
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm h-100 border-vg-bordeaux-2">
                <div class="card-body text-center">
                    <i class="bi bi-card-list fs-1 text-vg-bordeaux"></i>
                    <h2 class="dashboard-number mt-3 mb-1"><?= $totalMenus ?></h2>
                    <p class="text-muted mb-3">Menus</p>
                    <a href="/admin/menus" class="btn btn-sm btn-vg-bordeaux rounded-pill">
                        <i class="bi bi-pencil"></i> Gérer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Accès rapides -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Accès Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <a href="/employe/commandes" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-box-seam fs-5 d-block mb-1"></i>
                                <small>Commandes</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="/employe/avis" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-star fs-5 d-block mb-1"></i>
                                <small>Avis</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="/admin/menus" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-card-list fs-5 d-block mb-1"></i>
                                <small>Menus</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="/admin/plats" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-egg-fried fs-5 d-block mb-1"></i>
                                <small>Plats</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="/admin/horaires" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-clock fs-5 d-block mb-1"></i>
                                <small>Horaires</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="/admin/stats" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-graph-up fs-5 d-block mb-1"></i>
                                <small>Statistiques</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="/profil" class="btn btn-vg-cream w-100 py-2 rounded-pill">
                                <i class="bi bi-person-circle fs-5 d-block mb-1"></i>
                                <small>Mon Profil</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
