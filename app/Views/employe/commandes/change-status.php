<?php
//Contact utilisateur obligatoire avant modification statut
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil-square"></i> Modifier la Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
        <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-vg-bordeaux rounded-pill">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Informations commande -->
        <div class="col-md-5">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-vg-gold text-vg-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informations Commande</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Utilisateur :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['utilisateur_prenom'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5">Email :</dt>
                        <dd class="col-sm-7">
                            <a href="mailto:<?= htmlspecialchars($commande['utilisateur_email']) ?>">
                                <?= htmlspecialchars($commande['utilisateur_email']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-5">Téléphone :</dt>
                        <dd class="col-sm-7">
                            <?php if (!empty($commande['utilisateur_telephone'])): ?>
                                <a href="tel:<?= htmlspecialchars($commande['utilisateur_telephone']) ?>">
                                    <?= htmlspecialchars($commande['utilisateur_telephone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-5">Menu(s) :</dt>
                        <dd class="col-sm-7">
                            <?php if (!empty($commande['lignesMenus'])): ?>
                                <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                    <strong><?= htmlspecialchars($ligne['menu_nom']) ?></strong> (<?= $ligne['nombre_personne'] ?> pers.)<br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Aucun menu</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-5">Date prestation :</dt>
                        <dd class="col-sm-7">
                            <?= date('d/m/Y', strtotime($commande['date_prestation'])) ?>
                            <?php if (!empty($commande['heure_livraison'])): ?>
                                à <?= htmlspecialchars($commande['heure_livraison']) ?>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-5">Total personnes :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['totalPersonnes'] ?? 0) ?></dd>

                        <dt class="col-sm-5">Lieu :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['lieu_livraison'] ?? 'Non renseigné') ?></dd>

                        <dt class="col-sm-5">Montant :</dt>
                        <dd class="col-sm-7"><strong><?= number_format($commande['total_final'] ?? 0, 2) ?> €</strong></dd>

                        <dt class="col-sm-5">Statut actuel :</dt>
                        <dd class="col-sm-7">
                            <span class="badge <?= match($commande['statut']) {
                                'en_attente' => 'bg-warning text-dark',
                                'acceptee' => 'bg-success',
                                'en_preparation' => 'bg-primary',
                                'en_cours_livraison' => 'bg-purple',
                                'livree' => 'bg-orange text-dark',
                                'attente_retour_materiel' => 'bg-brown text-white',
                                'terminee' => 'bg-dark-green text-white',
                                'annulee' => 'bg-danger',
                                default => 'bg-secondary'
                            } ?> fs-6">
                                <?= ucfirst(str_replace('_', ' ', $commande['statut'])) ?>
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Alerte -->
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Rappel:</strong>
                <p class="mb-0 mt-2">Vous devez contacter l'utilisateur par téléphone ou email avant de modifier ou annuler une commande.</p>
            </div>
        </div>

        <!-- Formulaire de modification -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Changement de Statut</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/employe/commandes/change-status?id=<?= htmlspecialchars($commande['numero_commande']) ?>" id="formChangeStatus">
                        
                        <!-- Nouveau statut -->
                        <div class="mb-4">
                            <label for="nouveau_statut" class="form-label fw-bold">
                                Nouveau Statut <span class="text-danger">*</span>
                            </label>
                            <select name="nouveau_statut" id="nouveau_statut" class="form-select" data-statut-actuel="<?= htmlspecialchars($commande['statut']) ?>" required>
                                <option value="">-- Sélectionner --</option>
                                <?php if ($commande['statut'] === 'en_attente'): ?>
                                    <option value="acceptee">✅ Acceptée</option>
                                    <option value="annulee">❌ Refusée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'acceptee'): ?>
                                    <option value="en_preparation">🔄 En préparation</option>
                                    <option value="annulee">❌ Annulée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'en_preparation'): ?>
                                    <option value="en_cours_livraison">🚚 En cours de livraison</option>
                                    <option value="annulee">❌ Annulée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'en_cours_livraison'): ?>
                                    <option value="livree">📦 Livrée</option>
                                    <option value="annulee">❌ Annulée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'livree'): ?>
                                    <option value="attente_retour_materiel">⏳ Attente retour matériel</option>
                                    <option value="terminee">✅ Terminée</option>
                                    <option value="annulee">❌ Annulée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'attente_retour_materiel'): ?>
                                    <option value="terminee">✅ Terminée</option>
                                    <option value="annulee">❌ Annulée</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <hr>

                        <!-- Contact utilisateur (apparaît selon le statut choisi) -->
                        <div id="contactUtilisateurSection" class="d-none">
                            <h6 class="border-bottom pb-2">
                                <i class="bi bi-telephone-fill"></i> Contact Utilisateur Obligatoire
                            </h6>

                            <!-- Confirmation contact -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="contacte_utilisateur" id="contacte_utilisateur" class="form-check-input" value="1">
                                    <label for="contacte_utilisateur" class="form-check-label fw-bold">
                                        Je confirme avoir contacté l'utilisateur <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Mode de contact -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Mode de contact <span class="text-danger">*</span>
                                </label>
                                <div class="form-check">
                                    <input type="radio" name="mode_contact" id="mode_gsm" class="form-check-input" value="GSM">
                                    <label for="mode_gsm" class="form-check-label">
                                        <i class="bi bi-phone"></i> Téléphone (GSM)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="mode_contact" id="mode_email" class="form-check-input" value="Email">
                                    <label for="mode_email" class="form-check-label">
                                        <i class="bi bi-envelope"></i> Email
                                    </label>
                                </div>
                            </div>

                            <!-- Motif -->
                            <div class="mb-3">
                                <label for="motif_contact" class="form-label fw-bold">
                                    Motif du contact / Raison de la modification <span class="text-danger">*</span>
                                </label>
                                <textarea name="motif_contact" id="motif_contact" class="form-control" rows="4" 
                                          placeholder="Ex: Utilisateur a demandé une annulation, problème de disponibilité, changement de date..."></textarea>
                                <small class="text-muted">Minimum 10 caractères</small>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning flex-fill">
                                <i class="bi bi-check-circle"></i> Valider
                            </button>
                            <a href="/employe/commandes" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$additionalScripts = ['/assets/js/employe-commandes.js'];
require_once __DIR__ . '/../../layouts/footer.php'; 
?>
