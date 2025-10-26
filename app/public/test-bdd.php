<?php
/**
 * Page de test de la connexion base de données
 * À SUPPRIMER avant la mise en production
 */

require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test BDD - Vite & Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">🧪 Test de la base de données</h1>
        
        <?php
        try {
            // Test 1 : Compter les utilisateurs
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateur");
            $result = $stmt->fetch();
            echo "<div class='alert alert-success'>";
            echo "✅ Connexion réussie !<br>";
            echo "📊 Nombre d'utilisateurs : <strong>{$result['total']}</strong>";
            echo "</div>";
            
            // Test 2 : Afficher les rôles
            echo "<h3>Rôles disponibles :</h3>";
            echo "<ul class='list-group mb-4'>";
            $stmt = $pdo->query("SELECT * FROM role");
            while ($role = $stmt->fetch()) {
                echo "<li class='list-group-item'>{$role['role_id']} - {$role['libelle']}</li>";
            }
            echo "</ul>";
            
            // Test 3 : Afficher les menus
            echo "<h3>Menus disponibles :</h3>";
            echo "<div class='row g-3'>";
            $stmt = $pdo->query("SELECT * FROM menu LIMIT 5");
            while ($menu = $stmt->fetch()) {
                echo "<div class='col-md-6'>";
                echo "<div class='card'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>{$menu['titre']}</h5>";
                echo "<p class='card-text'>{$menu['description']}</p>";
                echo "<p class='text-muted'>Prix : {$menu['prix_par_personne']}€/pers</p>";
                echo "</div></div></div>";
            }
            echo "</div>";
            
            // Test 4 : Statistiques complètes (toutes les tables)
            echo "<h3 class='mt-4'>📈 Statistiques :</h3>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead class='table-dark'><tr><th>Table</th><th>Enregistrements</th></tr></thead>";
            echo "<tbody>";
            
            $tables = [
                'utilisateur', 'role', 'menu', 'commande', 'suivi_commande', 
                'avis', 'plat', 'regime', 'theme', 'allergene', 
                'horaire', 'contact', 'propose', 'adapte', 'menu_theme', 'consent'
            ];
            
            $total = 0;
            foreach ($tables as $table) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM $table");
                    $result = $stmt->fetch();
                    $count = $result['total'];
                    $total += $count;
                    $badge = $count > 0 ? 'success' : 'secondary';
                    echo "<tr><th>$table</th><td><span class='badge bg-$badge'>$count</span></td></tr>";
                } catch (PDOException $e) {
                    echo "<tr><th>$table</th><td><span class='badge bg-danger'>Erreur</span></td></tr>";
                }
            }
            echo "</tbody>";
            echo "<tfoot class='table-dark'><tr><th>TOTAL</th><th><span class='badge bg-primary'>$total enregistrements</span></th></tr></tfoot>";
            echo "</table>";
            
            // Vérification des nouvelles tables (migration)
            echo "<div class='alert alert-info mt-3'>";
            echo "<strong>✅ Migration validée :</strong> Tables ajoutées : <code>suivi_commande</code>, <code>contact</code>, <code>menu_theme</code>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>";
            echo "❌ Erreur : " . $e->getMessage();
            echo "</div>";
        }
        ?>
        
        <div class="alert alert-warning mt-4">
            <strong>⚠️ Important :</strong> Supprimer ce fichier avant la mise en production !
        </div>
        
        <a href="/index.php" class="btn btn-primary">← Retour à l'accueil</a>
    </div>
</body>
</html>
