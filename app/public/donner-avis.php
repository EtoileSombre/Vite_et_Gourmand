<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Vérifier l'authentification
requireLogin('/donner-avis.php');
$currentUser = getCurrentUser();

// Récupérer l'ID de la commande
$commandeId = $_GET['commande'] ?? null;

if (!$commandeId) {
    $_SESSION['flash_error'] = "Commande introuvable.";
    header('Location: /mes-commandes.php');
    exit;
}

try {
    // Récupérer les détails de la commande
    $stmt = $pdo->prepare("
        SELECT c.*, m.titre as menu_titre, m.menu_id
        FROM commande c
        LEFT JOIN menu m ON c.menu_id = m.menu_id
        WHERE c.numero_commande = ? AND c.utilisateur_id = ?
    ");
    $stmt->execute([$commandeId, $currentUser['utilisateur_id']]);
    $commande = $stmt->fetch();

    if (!$commande) {
        $_SESSION['flash_error'] = "Commande introuvable ou accès refusé.";
        header('Location: /mes-commandes.php');
        exit;
    }

    // Vérifier que la commande est terminée
    if ($commande['statut'] !== 'terminée' && $commande['statut'] !== 'livré') {
        $_SESSION['flash_error'] = "Vous ne pouvez donner un avis que sur une commande terminée.";
        header('Location: /commande-detail.php?id=' . urlencode($commandeId));
        exit;
    }

    // Vérifier si un avis existe déjà pour cette commande
    $stmtCheckAvis = $pdo->prepare("
        SELECT avis_id FROM avis WHERE numero_commande = ?
    ");
    $stmtCheckAvis->execute([$commandeId]);
    $avisExistant = $stmtCheckAvis->fetch();

    if ($avisExistant) {
        $_SESSION['flash_info'] = "Vous avez déjà donné un avis pour cette commande.";
        header('Location: /commande-detail.php?id=' . urlencode($commandeId));
        exit;
    }

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération de la commande : " . $e->getMessage());
    $_SESSION['flash_error'] = "Erreur lors de la récupération de la commande.";
    header('Location: /mes-commandes.php');
    exit;
}

// Traitement du formulaire
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = (int)$_POST['note'] ?? 0;
    $commentaire = trim($_POST['commentaire'] ?? '');

    // Validation
    if ($note < 1 || $note > 5) {
        $errors[] = "Veuillez sélectionner une note entre 1 et 5 étoiles.";
    }

    if (empty($commentaire)) {
        $errors[] = "Veuillez laisser un commentaire sur votre expérience.";
    } elseif (strlen($commentaire) < 10) {
        $errors[] = "Votre commentaire doit contenir au moins 10 caractères.";
    } elseif (strlen($commentaire) > 1000) {
        $errors[] = "Votre commentaire ne peut pas dépasser 1000 caractères.";
    }

    if (empty($errors)) {
        try {
            // Insérer l'avis avec statut 'en attente' pour validation par un employé
            $stmtInsert = $pdo->prepare("
                INSERT INTO avis (
                    utilisateur_id, 
                    menu_id, 
                    numero_commande, 
                    note, 
                    commentaire, 
                    statut,
                    date_creation
                ) VALUES (?, ?, ?, ?, ?, 'en attente', NOW())
            ");

            $stmtInsert->execute([
                $currentUser['utilisateur_id'],
                $commande['menu_id'],
                $commandeId,
                $note,
                $commentaire
            ]);

            $_SESSION['flash_success'] = "Merci pour votre avis ! Il sera publié après validation par notre équipe.";
            header('Location: /commande-detail.php?id=' . urlencode($commandeId));
            exit;

        } catch (PDOException $e) {
            error_log("Erreur lors de l'enregistrement de l'avis : " . $e->getMessage());
            $errors[] = "Une erreur est survenue lors de l'enregistrement de votre avis.";
        }
    }
}

$pageTitle = "Donner mon avis - Commande #" . htmlspecialchars($commande['numero_commande']);
include '../includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/mes-commandes.php">Mes commandes</a></li>
            <li class="breadcrumb-item"><a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>">#<?= htmlspecialchars($commande['numero_commande']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Donner mon avis</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-star"></i> Donner mon avis</h1>
                </div>
                <div class="card-body">
                    <!-- Messages d'erreur -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Récapitulatif de la commande -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Commande concernée</h5>
                        <ul class="mb-0">
                            <li><strong>Commande :</strong> #<?= htmlspecialchars($commande['numero_commande']) ?></li>
                            <li><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></li>
                            <li><strong>Date de prestation :</strong> <?= date('d/m/Y', strtotime($commande['date_prestation'])) ?></li>
                        </ul>
                    </div>

                    <div class="alert alert-light">
                        <i class="bi bi-lightbulb"></i> <strong>Votre avis compte !</strong>
                        <p class="mb-0 mt-2 small">
                            Partagez votre expérience pour aider d'autres clients à faire leur choix 
                            et nous permettre d'améliorer continuellement nos services.
                        </p>
                    </div>

                    <!-- Formulaire d'avis -->
                    <form method="POST" action="" id="avisForm">
                        <!-- Note par étoiles -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Votre note <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="star-rating" id="starRating">
                                    <input type="radio" id="star5" name="note" value="5" required>
                                    <label for="star5" title="5 étoiles - Excellent"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" id="star4" name="note" value="4">
                                    <label for="star4" title="4 étoiles - Très bien"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" id="star3" name="note" value="3">
                                    <label for="star3" title="3 étoiles - Bien"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" id="star2" name="note" value="2">
                                    <label for="star2" title="2 étoiles - Moyen"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" id="star1" name="note" value="1">
                                    <label for="star1" title="1 étoile - Insuffisant"><i class="bi bi-star-fill"></i></label>
                                </div>
                                <span id="ratingText" class="text-muted"></span>
                            </div>
                            <small class="form-text text-muted">Cliquez sur les étoiles pour noter votre expérience</small>
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="commentaire" class="form-label fw-bold">Votre commentaire <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="6" 
                                      placeholder="Partagez votre expérience : qualité des plats, présentation, livraison, service client..." 
                                      minlength="10" maxlength="1000" required></textarea>
                            <small class="form-text text-muted">
                                <span id="charCount">0</span> / 1000 caractères (minimum 10)
                            </small>
                        </div>

                        <!-- Conseils pour un bon avis -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-lightbulb-fill text-warning"></i> Conseils pour un bon avis</h6>
                                <ul class="small mb-0">
                                    <li>Soyez précis sur ce que vous avez aimé ou moins aimé</li>
                                    <li>Mentionnez la qualité des plats, la présentation, la ponctualité de la livraison</li>
                                    <li>Restez courtois et constructif dans vos remarques</li>
                                    <li>Évitez les informations personnelles sensibles</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Modération -->
                        <div class="alert alert-warning mb-3">
                            <small>
                                <i class="bi bi-shield-check"></i> <strong>Modération :</strong>
                                Votre avis sera vérifié par notre équipe avant publication pour garantir qu'il respecte 
                                nos conditions d'utilisation. Merci de votre compréhension.
                            </small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="bi bi-send"></i> Publier mon avis
                            </button>
                            <a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-question-circle"></i> Que devient mon avis ?</h6>
                    <p class="card-text small">
                        Après validation par notre équipe (sous 48h maximum), votre avis sera publié 
                        sur notre site et pourra aider d'autres clients dans leur choix. 
                        Vous recevrez une notification par email une fois votre avis publié.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Système de notation par étoiles */
.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    font-size: 2rem;
}

.star-rating input {
    display: none;
}

.star-rating label {
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #ffc107;
}

.star-rating:hover label {
    color: #ffc107;
}

.star-rating label:hover ~ label {
    color: #ddd;
}
</style>

<?php include '../includes/footer.php'; ?>
