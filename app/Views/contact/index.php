<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Contactez-nous</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/contact">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   required
                                   placeholder="votre.email@example.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
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
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-send"></i> Envoyer le message
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">
                    Temps de réponse : 24h à 48h (jours ouvrés)
                </small>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
