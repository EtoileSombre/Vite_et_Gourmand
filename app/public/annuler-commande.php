<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Vérifier l'authentification
requireLogin('/annuler-commande.php');
$currentUser = getCurrentUser();

// Récupérer l'ID de la commande
$commandeId = $_GET['id'] ?? null;

if (!$commandeId) {
    $_SESSION['flash_error'] = "Commande introuvable.";
    header('Location: /mes-commandes.php');
    exit;
}

try {
    // Récupérer les détails de la commande
    $stmt = $pdo->prepare("
        SELECT c.*, m.titre as menu_titre
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

    // Vérifier que la commande peut être annulée
    if ($commande['statut'] === 'annulée') {
        $_SESSION['flash_error'] = "Cette commande est déjà annulée.";
        header('Location: /commande-detail.php?id=' . urlencode($commandeId));
        exit;
    }

    if ($commande['statut'] === 'terminée') {
        $_SESSION['flash_error'] = "Une commande terminée ne peut pas être annulée.";
        header('Location: /commande-detail.php?id=' . urlencode($commandeId));
        exit;
    }

    // Vérifier le délai d'annulation (24h avant la prestation)
    $delai_annulation = strtotime($commande['date_prestation'] . ' ' . $commande['heure_livraison']) - (24 * 3600);
    $peut_annuler = time() < $delai_annulation;

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
    $motif = trim($_POST['motif_annulation'] ?? '');
    $confirmation = $_POST['confirmation'] ?? '';

    // Validation
    if (empty($motif)) {
        $errors[] = "Veuillez indiquer le motif de l'annulation.";
    }

    if ($confirmation !== 'OUI') {
        $errors[] = "Veuillez confirmer l'annulation en cochant la case.";
    }

    if (!$peut_annuler && $commande['statut'] !== 'en attente') {
        $errors[] = "Le délai d'annulation est dépassé (24h avant la prestation).";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Mettre à jour le statut de la commande
            $stmtUpdate = $pdo->prepare("
                UPDATE commande SET
                    statut = 'annulée',
                    motif_annulation = ?,
                    updated_at = NOW()
                WHERE numero_commande = ? AND utilisateur_id = ?
            ");
            $stmtUpdate->execute([$motif, $commandeId, $currentUser['utilisateur_id']]);

            // Enregistrer dans l'historique de suivi
            $stmtSuivi = $pdo->prepare("
                INSERT INTO suivi_commande (numero_commande, statut, commentaire, date_modification)
                VALUES (?, 'annulée', ?, NOW())
            ");
            $stmtSuivi->execute([$commandeId, "Annulation client : " . $motif]);

            $pdo->commit();

            // TODO: Envoyer un email de confirmation d'annulation

            $_SESSION['flash_success'] = "Votre commande a été annulée avec succès.";
            header('Location: /commande-detail.php?id=' . urlencode($commandeId));
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur lors de l'annulation de la commande : " . $e->getMessage());
            $errors[] = "Une erreur est survenue lors de l'annulation de la commande.";
        }
    }
}

$pageTitle = "Annuler la commande #" . htmlspecialchars($commande['numero_commande']);
include '../includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/mes-commandes.php">Mes commandes</a></li>
            <li class="breadcrumb-item"><a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>">#<?= htmlspecialchars($commande['numero_commande']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Annuler</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-x-circle"></i> Annuler la commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
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

                    <!-- Vérification du délai d'annulation -->
                    <?php if (!$peut_annuler && $commande['statut'] !== 'en attente'): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Délai dépassé :</strong> 
                            L'annulation doit être effectuée au moins 24h avant la date de prestation.
                            <br><br>
                            <strong>Date de prestation :</strong> <?= date('d/m/Y à H:i', strtotime($commande['date_prestation'] . ' ' . $commande['heure_livraison'])) ?>
                            <br>
                            <strong>Délai limite d'annulation :</strong> <?= date('d/m/Y à H:i', $delai_annulation) ?>
                        </div>

                        <p class="mb-3">Pour toute demande d'annulation hors délai, veuillez nous contacter directement.</p>

                        <div class="d-flex gap-2">
                            <a href="/contact.php" class="btn btn-primary">
                                <i class="bi bi-envelope"></i> Nous contacter
                            </a>
                            <a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Retour à la commande
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Récapitulatif de la commande -->
                        <div class="alert alert-warning">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Attention</h5>
                            <p>Vous êtes sur le point d'annuler la commande suivante :</p>
                            <hr>
                            <ul class="mb-0">
                                <li><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></li>
                                <li><strong>Nombre de personnes :</strong> <?= $commande['nombre_personne'] ?></li>
                                <li><strong>Date de prestation :</strong> <?= date('d/m/Y à H:i', strtotime($commande['date_prestation'] . ' ' . $commande['heure_livraison'])) ?></li>
                                <li><strong>Montant total :</strong> <?= number_format($commande['prix_total'], 2, ',', ' ') ?> €</li>
                            </ul>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Politique d'annulation :</strong>
                            <ul class="mb-0 mt-2">
                                <li>L'annulation est gratuite jusqu'à 24h avant la prestation</li>
                                <?php if ($commande['statut'] === 'en attente'): ?>
                                    <li>Votre commande est en attente, vous pouvez l'annuler sans frais</li>
                                <?php elseif ($commande['statut'] === 'accepté' || $commande['statut'] === 'en préparation'): ?>
                                    <li>Votre commande est <?= $commande['statut'] ?>, contactez-nous pour l'annulation</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Formulaire d'annulation -->
                        <form method="POST" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.');">
                            <div class="mb-3">
                                <label for="motif_annulation" class="form-label">Motif de l'annulation <span class="text-danger">*</span></label>
                                <select class="form-select mb-2" id="motif_annulation_select" onchange="updateMotif()">
                                    <option value="">Sélectionnez un motif</option>
                                    <option value="Changement de plans">Changement de plans</option>
                                    <option value="Problème de disponibilité">Problème de disponibilité</option>
                                    <option value="Budget insuffisant">Budget insuffisant</option>
                                    <option value="Commande en double">Commande en double</option>
                                    <option value="Erreur dans la commande">Erreur dans la commande</option>
                                    <option value="Autre">Autre (précisez ci-dessous)</option>
                                </select>
                                <textarea class="form-control" id="motif_annulation" name="motif_annulation" rows="4" 
                                          placeholder="Veuillez préciser le motif de votre annulation..." required></textarea>
                                <small class="form-text text-muted">Cette information nous aide à améliorer nos services.</small>
                            </div>

                            <!-- Confirmation -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" value="OUI" required>
                                    <label class="form-check-label" for="confirmation">
                                        <strong>Je confirme vouloir annuler définitivement cette commande</strong>
                                    </label>
                                </div>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Confirmer l'annulation
                                </button>
                                <a href="/commande-detail.php?id=<?= urlencode($commandeId) ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour à la commande
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-question-circle"></i> Questions sur l'annulation ?</h6>
                    <p class="card-text small mb-2">
                        Si vous avez des questions ou souhaitez modifier votre commande plutôt que l'annuler, 
                        n'hésitez pas à nous contacter.
                    </p>
                    <a href="/contact.php" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-envelope"></i> Nous contacter
                    </a>
                    <?php if ($commande['statut'] === 'en attente'): ?>
                        <a href="/modifier-commande.php?id=<?= urlencode($commandeId) ?>" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-pencil"></i> Modifier la commande
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateMotif() {
    const select = document.getElementById('motif_annulation_select');
    const textarea = document.getElementById('motif_annulation');
    
    if (select.value && select.value !== 'Autre') {
        textarea.value = select.value;
    } else if (select.value === 'Autre') {
        textarea.value = '';
        textarea.focus();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
