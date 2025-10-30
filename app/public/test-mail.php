<?php
/**
 * Script de test pour PHPMailer avec MailHog
 * Accès: http://localhost:8080/test-mail.php
 */

require_once __DIR__ . '/../config/mail.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PHPMailer - Vite & Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">🧪 Test PHPMailer avec MailHog</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>ℹ️ Information :</strong> Les emails sont capturés par MailHog.<br>
                            Consultez l'interface web : <a href="http://localhost:8025" target="_blank" class="alert-link">http://localhost:8025</a>
                        </div>

                        <?php
                        $testResults = [];
                        
                        // Test 1: Email de contact
                        echo "<h4>Test 1 : Email de contact</h4>";
                        if (sendContactEmail(
                            'Jean Test', 
                            'jean.test@example.com', 
                            '0612345678',
                            'Ceci est un message de test pour vérifier que PHPMailer fonctionne correctement avec MailHog.'
                        )) {
                            $testResults[] = ['test' => 'Email de contact', 'status' => 'success'];
                            echo "<div class='alert alert-success'><strong>✅ Succès !</strong> Email de contact envoyé.</div>";
                        } else {
                            $testResults[] = ['test' => 'Email de contact', 'status' => 'error'];
                            echo "<div class='alert alert-danger'><strong>❌ Erreur !</strong> Échec de l'envoi de l'email de contact.</div>";
                        }
                        
                        // Test 2: Email de bienvenue
                        echo "<h4 class='mt-4'>Test 2 : Email de bienvenue</h4>";
                        if (sendWelcomeEmail('test@example.com', 'Marie')) {
                            $testResults[] = ['test' => 'Email de bienvenue', 'status' => 'success'];
                            echo "<div class='alert alert-success'><strong>✅ Succès !</strong> Email de bienvenue envoyé.</div>";
                        } else {
                            $testResults[] = ['test' => 'Email de bienvenue', 'status' => 'error'];
                            echo "<div class='alert alert-danger'><strong>❌ Erreur !</strong> Échec de l'envoi de l'email de bienvenue.</div>";
                        }
                        
                        // Résumé
                        $successCount = count(array_filter($testResults, fn($r) => $r['status'] === 'success'));
                        $totalCount = count($testResults);
                        ?>

                        <hr class="my-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>📊 Résumé des tests</h5>
                                <p class="mb-0">
                                    <strong><?= $successCount ?></strong> test(s) réussi(s) sur <strong><?= $totalCount ?></strong>
                                </p>
                                <?php if ($successCount === $totalCount): ?>
                                    <div class="alert alert-success mt-3 mb-0">
                                        <strong>🎉 Tous les tests sont passés !</strong><br>
                                        PHPMailer est correctement configuré avec MailHog.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <strong>⚠️ Certains tests ont échoué.</strong><br>
                                        Vérifiez les logs et la configuration de MailHog.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="text-center">
                            <a href="http://localhost:8025" target="_blank" class="btn btn-primary btn-lg me-2">
                                📧 Voir les emails dans MailHog
                            </a>
                            <a href="/index.php" class="btn btn-secondary btn-lg">
                                🏠 Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
