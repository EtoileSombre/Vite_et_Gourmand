<?php
/**
 * Page de test MongoDB
 * Vérifier que la connexion et les logs fonctionnent
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../config/MongoStats.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test MongoDB - Vite & Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">🧪 Test MongoDB</h1>

        <?php
        $mongoStats = new MongoStats();
        $isAvailable = $mongoStats->isAvailable();
        ?>

        <!-- Statut connexion -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>📡 Statut de la connexion</h3>
            </div>
            <div class="card-body">
                <?php if ($isAvailable): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <strong>MongoDB est connecté !</strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        <strong>MongoDB n'est pas disponible.</strong>
                        <p class="mb-0">Vérifiez que le conteneur Docker tourne.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isAvailable): ?>

        <!-- Test 1 : Logger une vue de menu -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>📊 Test 1 : Logger une vue de menu</h3>
            </div>
            <div class="card-body">
                <?php
                $testMenuId = 1;
                $result = $mongoStats->logMenuView($testMenuId, [
                    'titre' => 'Menu Test',
                    'prix' => 25.00
                ]);
                ?>

                <?php if ($result): ?>
                    <div class="alert alert-success">
                        ✅ <strong>Log enregistré avec succès !</strong><br>
                        Menu ID : <?= $testMenuId ?><br>
                        Horodatage : <?= date('Y-m-d H:i:s') ?><br>
                        <small>Vérifiez dans Mongo Express : collection <code>menu_views</code></small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        ❌ <strong>Erreur lors de l'enregistrement du log.</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Test 2 : Logger une activité utilisateur -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>👤 Test 2 : Logger une activité utilisateur</h3>
            </div>
            <div class="card-body">
                <?php
                $result2 = $mongoStats->logUserActivity('test_action', 1, [
                    'message' => 'Ceci est un test'
                ]);
                ?>

                <?php if ($result2): ?>
                    <div class="alert alert-success">
                        ✅ <strong>Activité enregistrée avec succès !</strong><br>
                        Action : test_action<br>
                        Utilisateur ID : 1<br>
                        <small>Vérifiez dans Mongo Express : collection <code>user_activity</code></small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        ❌ <strong>Erreur lors de l'enregistrement de l'activité.</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Test 3 : Récupérer les statistiques -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>📈 Test 3 : Statistiques globales</h3>
            </div>
            <div class="card-body">
                <?php
                $stats = $mongoStats->getGlobalStats();
                ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card text-center bg-primary text-white">
                            <div class="card-body">
                                <h2><?= $stats['total_menu_views'] ?></h2>
                                <p class="mb-0">Vues des menus</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center bg-success text-white">
                            <div class="card-body">
                                <h2><?= $stats['total_user_activities'] ?></h2>
                                <p class="mb-0">Activités utilisateurs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center bg-info text-white">
                            <div class="card-body">
                                <h2><?= $stats['total_commandes'] ?></h2>
                                <p class="mb-0">Commandes tracées</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test 4 : Top des menus -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>🏆 Test 4 : Top 5 des menus</h3>
            </div>
            <div class="card-body">
                <?php
                $topMenus = $mongoStats->getTopMenus(5, 30);
                ?>

                <?php if (empty($topMenus)): ?>
                    <div class="alert alert-info">
                        Pas encore assez de données. Consultez quelques menus sur le site !
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Menu</th>
                                <th>Nombre de vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topMenus as $index => $menu): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($menu['menu_titre'] ?? 'N/A') ?></td>
                                    <td><strong><?= $menu['total_vues'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Liens utiles -->
        <div class="card">
            <div class="card-header">
                <h3>🔗 Liens utiles</h3>
            </div>
            <div class="card-body">
                <ul>
                    <li>
                        <a href="http://localhost:8081" target="_blank" class="btn btn-sm btn-primary">
                            Ouvrir Mongo Express
                        </a>
                        <small class="text-muted">(Login: admin / Password: voir .env)</small>
                    </li>
                    <li class="mt-2">
                        <a href="/menus.php" class="btn btn-sm btn-secondary">
                            Consulter des menus pour générer des logs
                        </a>
                    </li>
                    <li class="mt-2">
                        <a href="/test-mongo.php" class="btn btn-sm btn-success">
                            Recharger cette page (créer de nouveaux logs)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <?php endif; ?>

        <div class="mt-4">
            <a href="/" class="btn btn-secondary">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
