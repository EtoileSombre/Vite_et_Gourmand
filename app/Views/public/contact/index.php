<?php include __DIR__ . '/../../layouts/header.php'; 

// Vérifier si le message a été envoyé
$contactEnvoye = isset($_SESSION['contact_envoye']) ? $_SESSION['contact_envoye'] : false;
if ($contactEnvoye) {
    unset($_SESSION['contact_envoye']);
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h1 class="mb-0 h3"><i class="bi bi-envelope-fill"></i> Contactez-nous</h1>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php /** @var array<string> $errors */ foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="contactForm" method="POST" action="/contact" data-validate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   required
                                   placeholder="votre.email@example.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <div class="invalid-feedback">Veuillez entrer un email valide.</div>
                        </div>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="titre" 
                                   name="titre" 
                                   required
                                   minlength="5"
                                   maxlength="100"
                                   placeholder="Ex: Demande de devis"
                                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
                            <div class="invalid-feedback">Le titre doit contenir au moins 5 caractères.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="6" 
                                      required
                                      minlength="10"
                                      placeholder="Décrivez votre demande..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div class="invalid-feedback">La description doit contenir au moins 10 caractères.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-vg-gold w-100 rounded-pill">
                            <i class="bi bi-send"></i> Envoyer le message
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Vos données ne seront jamais partagées
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'envoi de message -->
<?php if ($contactEnvoye): ?>
<div class="modal fade" id="modalConfirmationContact" tabindex="-1" aria-labelledby="modalConfirmationContactLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-gold text-white">
                <h5 class="modal-title" id="modalConfirmationContactLabel">
                    Message envoyé
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                </div>
                <h5 class="mb-3">Votre message a bien été envoyé !</h5>
                <p class="text-muted mb-4">
                    Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-vg-gold rounded-pill" data-bs-dismiss="modal">
                    <i class="bi bi-check2"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$additionalScripts = ['/assets/js/modales.js'];
include __DIR__ . '/../../layouts/footer.php'; 
?>
