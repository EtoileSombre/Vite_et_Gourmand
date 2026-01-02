<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h3 class="mb-0"><i class="bi bi-person-circle"></i> Mon Espace Utilisateur</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Votre profil a été mis à jour avec succès !
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="/profil">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?= htmlspecialchars($user['nom']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Changer le mot de passe</h5>
                        <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="6">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary">Retour</a>
                            <button type="submit" class="btn btn-vg-gold">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5>Informations du compte</h5>
                    <p class="mb-1"><strong>Date d'inscription :</strong> 
                        <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                    </p>
                    <p class="mb-0"><strong>Rôle :</strong> 
                        <span class="badge bg-vg-gold text-vg-bordeaux"><?= htmlspecialchars($user['role']) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
