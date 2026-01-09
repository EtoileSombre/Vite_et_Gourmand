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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" 
                                   placeholder="06 12 34 56 78" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse_postale" class="form-label">Adresse postale *</label>
                            <input type="text" class="form-control" id="adresse_postale" name="adresse_postale" 
                                   value="<?= htmlspecialchars($user['adresse_postale'] ?? '') ?>" 
                                   placeholder="Numéro et nom de rue" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="code_postal" class="form-label">Code postal *</label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" 
                                       value="<?= htmlspecialchars($user['code_postal'] ?? '') ?>" 
                                       pattern="[0-9]{5}" placeholder="33000" required>
                            </div>
                            
                            <div class="col-md-8 mb-3">
                                <label for="ville" class="form-label">Ville *</label>
                                <input type="text" class="form-control" id="ville" name="ville" 
                                       value="<?= htmlspecialchars($user['ville'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Changer le mot de passe</h5>
                        <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="10">
                            <small class="form-text text-muted">Minimum 10 caractères : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="10">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <?php 
                            $userRole = $_SESSION['user_role'] ?? '';
                            $returnUrl = '/';
                            if ($userRole === 'employé') {
                                $returnUrl = '/employe';
                            } elseif ($userRole === 'administrateur') {
                                $returnUrl = '/admin/dashboard';
                            }
                            ?>
                            <a href="<?= $returnUrl ?>" class="btn btn-vg-bordeaux">
                                <i class="bi bi-arrow-left me-2"></i>Retour Dashboard
                            </a>
                            <button type="submit" class="btn btn-vg-gold">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5>Informations du compte</h5>
                    <p class="mb-1"><strong>Date d'inscription :</strong> 
                        <?= date('d/m/Y', strtotime($user['created_at'])) ?>
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
