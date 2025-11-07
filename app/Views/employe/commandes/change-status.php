<?php
/**
 * Vue : Formulaire de changement de statut (Employé)
 * OBLIGATION ECF : Contact client obligatoire avant modification
 */
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil-square"></i> Modifier la Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
        <a href="/employe/commandes" class="btn btn-outline-secondary">
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
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informations Commande</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Client :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['client_prenom'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5">Email :</dt>
                        <dd class="col-sm-7">
                            <a href="mailto:<?= htmlspecialchars($commande['client_email']) ?>">
                                <?= htmlspecialchars($commande['client_email']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-5">Téléphone :</dt>
                        <dd class="col-sm-7">
                            <?php if (!empty($commande['client_telephone'])): ?>
                                <a href="tel:<?= htmlspecialchars($commande['client_telephone']) ?>">
                                    <?= htmlspecialchars($commande['client_telephone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-5">Menu :</dt>
                        <dd class="col-sm-7"><strong><?= htmlspecialchars($commande['menu_titre'] ?? 'N/A') ?></strong></dd>

                        <dt class="col-sm-5">Date prestation :</dt>
                        <dd class="col-sm-7">
                            <?= date('d/m/Y', strtotime($commande['date_prestation'])) ?>
                            <?php if (!empty($commande['heure_livraison'])): ?>
                                à <?= htmlspecialchars($commande['heure_livraison']) ?>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-5">Nb personnes :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['nombre_personne'] ?? 0) ?></dd>

                        <dt class="col-sm-5">Lieu :</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($commande['lieu_livraison'] ?? 'Non renseigné') ?></dd>

                        <dt class="col-sm-5">Montant :</dt>
                        <dd class="col-sm-7"><strong><?= number_format($commande['prix_total'] ?? 0, 2) ?> €</strong></dd>

                        <dt class="col-sm-5">Statut actuel :</dt>
                        <dd class="col-sm-7">
                            <span class="badge <?= match($commande['statut']) {
                                'en attente' => 'bg-warning',
                                'validée' => 'bg-info',
                                'en préparation' => 'bg-primary',
                                'terminée' => 'bg-success',
                                default => 'bg-secondary'
                            } ?>">
                                <?= ucfirst($commande['statut']) ?>
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Alerte ECF -->
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Rappel ECF :</strong>
                <p class="mb-0 mt-2">Vous DEVEZ contacter le client par téléphone ou email avant de modifier ou annuler une commande.</p>
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
                            <select name="nouveau_statut" id="nouveau_statut" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php if ($commande['statut'] === 'en attente'): ?>
                                    <option value="validée">✅ Validée</option>
                                    <option value="refusée">❌ Refusée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'validée'): ?>
                                    <option value="en préparation">🔄 En préparation</option>
                                    <option value="annulée">❌ Annulée</option>
                                <?php endif; ?>
                                
                                <?php if ($commande['statut'] === 'en préparation'): ?>
                                    <option value="terminée">✅ Terminée</option>
                                    <option value="annulée">❌ Annulée</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <hr>

                        <!-- Contact client (apparaît selon le statut choisi) -->
                        <div id="contactClientSection" class="hidden-section">
                            <h6 class="text-danger mb-3">
                                <i class="bi bi-telephone-fill"></i> Contact Client Obligatoire
                            </h6>

                            <!-- Confirmation contact -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="contacte_client" id="contacte_client" class="form-check-input" value="1">
                                    <label for="contacte_client" class="form-check-label fw-bold">
                                        Je confirme avoir contacté le client <span class="text-danger">*</span>
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
                                          placeholder="Ex: Client a demandé une annulation, problème de disponibilité, changement de date..."></textarea>
                                <small class="text-muted">Minimum 10 caractères</small>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning flex-fill">
                                <i class="bi bi-check-circle"></i> Valider le changement
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

<script>
// Afficher/masquer la section contact client selon le statut choisi
document.getElementById('nouveau_statut').addEventListener('change', function() {
    const statut = this.value;
    const contactSection = document.getElementById('contactClientSection');
    const statutActuel = '<?= $commande['statut'] ?>';
    
    // Statuts nécessitant un contact client
    const requiresContact = ['refusée', 'annulée'].includes(statut) || 
                           (statutActuel !== 'en attente' && statut !== statutActuel);
    
    if (requiresContact) {
        contactSection.style.display = 'block';
        document.getElementById('contacte_client').required = true;
        document.getElementById('motif_contact').required = true;
    } else {
        contactSection.style.display = 'none';
        document.getElementById('contacte_client').required = false;
        document.getElementById('motif_contact').required = false;
    }
});

// Validation avant envoi
document.getElementById('formChangeStatus').addEventListener('submit', function(e) {
    const contactSection = document.getElementById('contactClientSection');
    
    if (contactSection.style.display !== 'none') {
        const contacte = document.getElementById('contacte_client').checked;
        const modeContact = document.querySelector('input[name="mode_contact"]:checked');
        const motif = document.getElementById('motif_contact').value.trim();
        
        if (!contacte) {
            e.preventDefault();
            alert('❌ Vous devez confirmer avoir contacté le client');
            return false;
        }
        
        if (!modeContact) {
            e.preventDefault();
            alert('❌ Veuillez sélectionner le mode de contact (GSM ou Email)');
            return false;
        }
        
        if (motif.length < 10) {
            e.preventDefault();
            alert('❌ Le motif doit contenir au moins 10 caractères');
            return false;
        }
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
