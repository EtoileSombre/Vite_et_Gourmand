<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
require_once __DIR__ . '/../../layouts/header.php';

$currentStatut = $commande['statut'];
$currentLabel = $statuts[$currentStatut] ?? ucfirst(str_replace('_', ' ', $currentStatut));
$badgeClass = 'badge-statut-' . str_replace('_', '-', $currentStatut);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt"></i> Commande #<?= htmlspecialchars($commande['numero_commande']) ?></h1>
        <a href="<?= ($_SESSION['user_role'] === 'administrateur') ? '/admin' : '/employe' ?>" class="btn btn-outline-secondary rounded-pill d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row">
        <!-- Informations Utilisateur -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Informations Utilisateur</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nom :</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars(($commande['utilisateur_nom'] ?? '') . ' ' . ($commande['utilisateur_prenom'] ?? 'N/A')) ?></dd>

                        <dt class="col-sm-4">Email :</dt>
                        <dd class="col-sm-8 text-break">
                            <a href="mailto:<?= htmlspecialchars($commande['utilisateur_email']) ?>">
                                <?= htmlspecialchars($commande['utilisateur_email']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-4">Téléphone :</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($commande['utilisateur_telephone'])): ?>
                                <a href="tel:<?= htmlspecialchars($commande['utilisateur_telephone']) ?>">
                                    <?= htmlspecialchars($commande['utilisateur_telephone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- FORMULAIRE DE CHANGEMENT DE STATUT -->
        <?php if (!in_array($commande['statut'], ['terminee', 'refusee', 'annulee'])): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-vg-gold text-white border-0">
                        <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Changement de Statut</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/employe/commandes/change-status?id=<?= htmlspecialchars($commande['numero_commande']) ?>">
                            <?= csrf_field() ?>
                            
                            <!-- Nouveau statut -->
                            <div class="mb-3">
                                <label for="nouveau_statut" class="form-label fw-bold">
                                    <i class="bi bi-clipboard-check"></i> Nouveau Statut <span class="text-danger">*</span>
                                </label>
                                <select name="nouveau_statut" id="nouveau_statut" class="form-select" data-statut-actuel="<?= htmlspecialchars($commande['statut']) ?>" required>
                                    <option value="">-- Sélectionner le nouveau statut --</option>
                                    <option value="acceptee" <?= $commande['statut'] === 'acceptee' ? 'selected' : '' ?>>Acceptée</option>
                                    <option value="en_preparation" <?= $commande['statut'] === 'en_preparation' ? 'selected' : '' ?>>En préparation</option>
                                    <option value="en_cours_livraison" <?= $commande['statut'] === 'en_cours_livraison' ? 'selected' : '' ?>>En cours de livraison</option>
                                    <option value="livree" <?= $commande['statut'] === 'livree' ? 'selected' : '' ?>>Livrée</option>
                                    <?php if ($commande['pret_materiel']): ?>
                                        <option value="attente_retour_materiel" <?= $commande['statut'] === 'attente_retour_materiel' ? 'selected' : '' ?>>Attente retour matériel (email auto 10j)</option>
                                    <?php endif; ?>
                                    <option value="terminee" <?= $commande['statut'] === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                                    <option value="annulee" <?= $commande['statut'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                                </select>
                            </div>

                            <!-- Bouton Validation -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-vg-bordeaux rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Valider
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- FORMULAIRE DE MODIFICATION DE COMMANDE -->
        <?php if (!in_array($commande['statut'], ['terminee', 'refusee', 'annulee'])): ?>
            <div class="col-md-12 mb-4 d-none" id="formEditCommandeSection">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white text-vg-bordeaux d-flex justify-content-between align-items-center border-0 border-bottom-bordeaux">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier la Commande</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" 
                                data-action="hide-edit-form"
                                title="Fermer le formulaire">
                            <i class="bi bi-x-lg"></i> Fermer
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Important :</strong> Vous devez contacter le client par téléphone ou email avant toute modification.
                        </div>

                        <form method="POST" action="/employe/commandes/edit" id="formEditCommande">
                            <?= csrf_field() ?>
                            <input type="hidden" name="numero_commande" value="<?= htmlspecialchars($commande['numero_commande']) ?>">

                            <div class="row">
                                <!-- Date et Heure -->
                                <div class="col-md-6 mb-3">
                                    <label for="date_prestation" class="form-label fw-bold">
                                        <i class="bi bi-calendar"></i> Date de Prestation <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="date_prestation" id="date_prestation" class="form-control" 
                                           value="<?= htmlspecialchars($commande['date_prestation']) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="heure_livraison" class="form-label fw-bold">
                                        <i class="bi bi-clock"></i> Heure de Livraison <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="heure_livraison" id="heure_livraison" class="form-control" 
                                           value="<?= htmlspecialchars($commande['heure_livraison']) ?>" required>
                                </div>

                                <!-- Lieu de livraison -->
                                <div class="col-md-12 mb-3">
                                    <label for="lieu_livraison" class="form-label fw-bold">
                                        <i class="bi bi-geo-alt"></i> Lieu de Livraison <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="lieu_livraison" id="lieu_livraison" class="form-control" 
                                           value="<?= htmlspecialchars($commande['lieu_livraison']) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ville_livraison" class="form-label fw-bold">
                                        <i class="bi bi-building"></i> Ville <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="ville_livraison" id="ville_livraison" class="form-control" 
                                           value="<?= htmlspecialchars($commande['ville_livraison']) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="code_postal_livraison" class="form-label fw-bold">
                                        <i class="bi bi-mailbox"></i> Code Postal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="code_postal_livraison" id="code_postal_livraison" class="form-control" 
                                           value="<?= htmlspecialchars($commande['code_postal_livraison'] ?? '') ?>" required>
                                </div>

                                <!-- Quantités des menus -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-people"></i> Quantités (Nombre de Personnes)
                                    </label>
                                    <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                        <div class="mb-2">
                                            <label class="form-label"><?= htmlspecialchars($ligne['menu_nom']) ?></label>
                                            <input type="number" name="quantite_menu[<?= $ligne['menu_id'] ?>]" 
                                                   class="form-control" min="1" 
                                                   value="<?= htmlspecialchars($ligne['nombre_personne']) ?>" required>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Contact utilisateur -->
                                <div class="col-md-12 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="border-bottom pb-2 mb-3">
                                                <i class="bi bi-telephone-fill text-danger"></i> Confirmation de Contact
                                            </h6>

                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="contacte_utilisateur_edit" id="contacte_utilisateur_edit" 
                                                           class="form-check-input" required>
                                                    <label for="contacte_utilisateur_edit" class="form-check-label fw-bold">
                                                        Je confirme avoir contacté l'utilisateur <span class="text-danger">*</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label fw-bold mb-1">
                                                    Mode de contact <span class="text-danger">*</span>
                                                </label>
                                                <div class="form-check">
                                                    <input type="radio" name="mode_contact_edit" id="mode_gsm_edit" 
                                                           class="form-check-input" value="GSM" required>
                                                    <label for="mode_gsm_edit" class="form-check-label">
                                                        <i class="bi bi-phone"></i> Téléphone
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" name="mode_contact_edit" id="mode_email_edit" 
                                                           class="form-check-input" value="Email" required>
                                                    <label for="mode_email_edit" class="form-check-label">
                                                        <i class="bi bi-envelope"></i> Email
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-0">
                                                <label for="motif_modification" class="form-label fw-bold mb-1">
                                                    Motif de la modification <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="motif_modification" id="motif_modification" 
                                                          class="form-control form-control-sm" rows="3" 
                                                          placeholder="Raison de la modification..." required></textarea>
                                                <small class="text-muted">Min. 10 caractères</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex gap-3 justify-content-between align-items-center">
                                <!-- Option pour annuler la commande -->
                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" class="form-check-input" name="annuler_commande" id="annuler_commande" value="1">
                                    <label class="form-check-label fw-bold text-vg-bordeaux" for="annuler_commande">
                                        Annuler cette commande
                                    </label>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-action="reset-edit-form">
                                        <i class="bi bi-x-circle"></i> Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-vg-bordeaux rounded-pill">
                                        <i class="bi bi-check-circle"></i> Enregistrer les Modifications
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Récapitulatif Commande -->
        <div class="col-md-12 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white text-vg-bordeaux border-0 border-bottom-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-card-list"></i> Récapitulatif de la Commande</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($commande['lignesMenus']) || !empty($lignesMateriels) || !empty($lignesBoissons)): ?>
                        <table class="table table-bordered">
                            <tbody>
                                <!-- Menus -->
                                <?php if (!empty($commande['lignesMenus'])): ?>
                                    <?php foreach ($commande['lignesMenus'] as $ligne): ?>
                                        <tr>
                                            <td>Menu</td>
                                            <td><strong><?= htmlspecialchars($ligne['menu_nom']) ?></strong></td>
                                            <td><?= htmlspecialchars($ligne['nombre_personne']) ?> personnes</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Matériel -->
                                <?php if (!empty($lignesMateriels)): ?>
                                    <?php foreach ($lignesMateriels as $mat): ?>
                                        <tr>
                                            <td>Matériel</td>
                                            <td>
                                                <strong><?= htmlspecialchars($mat['nom']) ?></strong>
                                                <?php if (!empty($mat['description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($mat['description']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($mat['quantite']) ?> pièce(s)</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Boissons -->
                                <?php if (!empty($lignesBoissons)): ?>
                                    <?php foreach ($lignesBoissons as $boisson): ?>
                                        <tr>
                                            <td>Boisson</td>
                                            <td>
                                                <strong><?= htmlspecialchars($boisson['nom']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($boisson['type_boisson']) ?> - <?= htmlspecialchars($boisson['contenance']) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($boisson['quantite']) ?> unité(s)</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <?php if (!in_array($commande['statut'], ['terminee', 'refusee', 'annulee'])): ?>
                                            <button type="button" class="btn btn-vg-gold rounded-pill" 
                                                    data-action="show-edit-form">
                                                <i class="bi bi-pencil-square me-1"></i> Modifier Commande
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Aucun élément commandé</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Détails Prestation -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white text-vg-bordeaux border-0 border-bottom-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Prestation</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 mb-4">Date commande :</dt>
                        <dd class="col-sm-7 mb-4"><?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></dd>

                        <dt class="col-sm-5 mb-4">Date prestation :</dt>
                        <dd class="col-sm-7 mb-4">
                            <strong><?= date('d/m/Y', strtotime($commande['date_prestation'])) ?></strong>
                        </dd>

                        <dt class="col-sm-5 mb-4">Heure livraison :</dt>
                        <dd class="col-sm-7 mb-4"><strong><?= htmlspecialchars($commande['heure_livraison']) ?></strong></dd>

                        <dt class="col-sm-5 mb-4">Lieu :</dt>
                        <dd class="col-sm-7 mb-4">
                            <?= htmlspecialchars($commande['lieu_livraison']) ?><br>
                            <?= htmlspecialchars($commande['ville_livraison']) ?> <?= htmlspecialchars($commande['code_postal_livraison'] ?? '') ?>
                        </dd>

                        <?php if (!empty($commande['distance_km'])): ?>
                            <dt class="col-sm-5">Distance :</dt>
                            <dd class="col-sm-7"><?= htmlspecialchars($commande['distance_km']) ?> km</dd>
                        <?php endif; ?>
                    </dl>

                    <!-- Carte OpenStreetMap -->
                    <?php if (!empty($commande['lieu_livraison']) && !empty($commande['ville_livraison'])): ?>
                        <hr class="my-3">
                        <div id="map-<?= htmlspecialchars($commande['numero_commande']) ?>" class="map-container rounded"></div>
                        
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                        <script>
                            (function() {
                                const address = <?= json_encode($commande['lieu_livraison'] . ', ' . $commande['ville_livraison'] . ' ' . ($commande['code_postal_livraison'] ?? '')) ?>;
                                const mapId = 'map-<?= htmlspecialchars($commande['numero_commande']) ?>';
                                
                                // Attendre le chargement complet de la page et de Leaflet
                                function initMap() {
                                    if (typeof L === 'undefined') {
                                        console.log('Leaflet pas encore chargé, nouvelle tentative...');
                                        setTimeout(initMap, 150);
                                        return;
                                    }
                                    
                                    console.log('Initialisation de la carte pour:', address);
                                    // Géocodage avec Nominatim (OpenStreetMap)
                                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.length > 0) {
                                            const lat = parseFloat(data[0].lat);
                                            const lon = parseFloat(data[0].lon);
                                            
                                            const map = L.map(mapId).setView([lat, lon], 15);
                                            
                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                                                maxZoom: 19
                                            }).addTo(map);
                                            
                                            L.marker([lat, lon]).addTo(map)
                                                .bindPopup('<strong>Lieu de livraison</strong><br>' + address)
                                                .openPopup();
                                        } else {
                                            // Fallback : essayer juste avec la ville
                                            console.log('Adresse précise non trouvée, recherche de la ville...');
                                            const ville = '<?= addslashes($commande['ville_livraison']) ?>';
                                            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(ville + ', France'))
                                                .then(response => response.json())
                                                .then(villeData => {
                                                    if (villeData && villeData.length > 0) {
                                                        const lat = parseFloat(villeData[0].lat);
                                                        const lon = parseFloat(villeData[0].lon);
                                                        
                                                        const map = L.map(mapId).setView([lat, lon], 12);
                                                        
                                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                                                            maxZoom: 19
                                                        }).addTo(map);
                                                        
                                                        L.marker([lat, lon]).addTo(map)
                                                            .bindPopup('<strong>Zone de livraison</strong><br>' + ville + '<br><small class="text-muted">Adresse précise non géolocalisée</small>');
                                                        
                                                        document.getElementById(mapId).style.opacity = '0.8';
                                                    } else {
                                                        document.getElementById(mapId).innerHTML = '<div class="alert alert-warning mb-0"><i class="bi bi-geo-alt-fill"></i> Adresse non géolocalisable : ' + address + '</div>';
                                                    }
                                                })
                                                .catch(() => {
                                                    document.getElementById(mapId).innerHTML = '<div class="alert alert-warning mb-0"><i class="bi bi-geo-alt-fill"></i> Adresse non géolocalisable</div>';
                                                });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Erreur carte:', error);
                                        document.getElementById(mapId).innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle"></i> Service de carte temporairement indisponible</div>';
                                    });
                                }
                                
                                // Attendre que le DOM et les ressources soient chargés
                                if (document.readyState === 'loading') {
                                    document.addEventListener('DOMContentLoaded', function() {
                                        setTimeout(initMap, 200);
                                    });
                                } else {
                                    setTimeout(initMap, 200);
                                }
                            })();
                        </script>
                    <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Matériel et Statut -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white text-vg-bordeaux border-0 border-bottom-bordeaux">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Matériel & Statut</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Statut actuel :</dt>
                        <dd class="col-sm-7 mb-3">
                            <span class="badge fs-6 <?= $badgeClass ?>">
                                <?= $currentLabel ?>
                            </span>
                        </dd>

                        <dt class="col-sm-5">Prêt matériel :</dt>
                        <dd class="col-sm-7 mb-3">
                            <?php if ($commande['pret_materiel']): ?>
                                <span class="badge bg-success">Oui</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non</span>
                            <?php endif; ?>
                        </dd>

                        <?php if ($commande['pret_materiel']): ?>
                            <dt class="col-sm-5">Restitution :</dt>
                            <dd class="col-sm-7 mb-0">
                                <?php if ($commande['restitution_materiel'] || $commande['statut'] === 'terminee'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Restitué</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> En attente</span>
                                <?php endif; ?>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>

                <!-- Récapitulatif Prix -->
                <div class="text-white px-3 py-2 bg-gray">
                    <h5 class="mb-0">Total</h5>
                </div>

                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5"><i class="bi bi-card-list text-vg-bordeaux"></i> Total menus :</dt>
                        <dd class="col-sm-7 mb-2"><strong><?= number_format($commande['total_menus'] ?? 0, 2) ?> €</strong></dd>

                        <?php if (!empty($totalBoissons)): ?>
                            <dt class="col-sm-5"><i class="bi bi-cup-straw text-vg-bordeaux"></i> Total boissons :</dt>
                            <dd class="col-sm-7 mb-2"><strong><?= number_format($totalBoissons, 2) ?> €</strong></dd>
                        <?php endif; ?>

                        <?php if (!empty($commande['prix_livraison']) && $commande['prix_livraison'] > 0): ?>
                            <hr class="my-2">
                            <dt class="col-sm-5"><i class="bi bi-truck text-secondary"></i> Frais livraison :</dt>
                            <dd class="col-sm-7 mb-2"><?= number_format($commande['prix_livraison'], 2) ?> €</dd>
                        <?php endif; ?>

                        <hr class="my-2">

                        <?php
                        // Calcul HT et TVA
                        $totalTTC = $commande['total_final'] ?? 0;
                        $totalHT = $totalTTC / 1.10;
                        $montantTVA = $totalTTC - $totalHT;
                        ?>

                        <dt class="col-sm-5 small text-muted">Total HT :</dt>
                        <dd class="col-sm-7 small text-muted mb-2"><?= number_format($totalHT, 2) ?> €</dd>

                        <dt class="col-sm-5 small text-muted">TVA (10%) :</dt>
                        <dd class="col-sm-7 small text-muted mb-2"><?= number_format($montantTVA, 2) ?> €</dd>

                        <hr class="my-2">

                        <dt class="col-sm-5"><strong>TOTAL TTC :</strong></dt>
                        <dd class="col-sm-7 mb-0">
                            <strong class="fs-5 text-vg-bordeaux">
                                <?= number_format($totalTTC, 2) ?> €
                            </strong>
                        </dd>

                        <?php if (!empty($totalCaution)): ?>
                            <hr class="my-3">
                            <dd class="col-12">
                                <div class="alert alert-warning mb-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-box-seam"></i> <strong>Cautions matériel</strong> <small class="text-muted">(restituable)</small></span>
                                        <strong class="text-warning"><?= number_format($totalCaution, 2) ?> €</strong>
                                    </div>
                                </div>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Historique de contact (si existe) -->
        <?php if (!empty($commande['motif_modification']) || !empty($commande['mode_contact_utilisateur'])): ?>
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historique Contact Utilisateur</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <?php if (!empty($commande['mode_contact_utilisateur'])): ?>
                                <dt class="col-sm-2">Mode contact :</dt>
                                <dd class="col-sm-10">
                                    <span class="badge bg-info"><?= htmlspecialchars($commande['mode_contact_utilisateur']) ?></span>
                                </dd>
                            <?php endif; ?>

                            <?php if (!empty($commande['motif_modification'])): ?>
                                <dt class="col-sm-2">Motif :</dt>
                                <dd class="col-sm-10"><?= nl2br(htmlspecialchars($commande['motif_modification'])) ?></dd>
                            <?php endif; ?>

                            <?php if (!empty($commande['date_dernier_contact'])): ?>
                                <dt class="col-sm-2">Date contact :</dt>
                                <dd class="col-sm-10"><?= date('d/m/Y à H:i', strtotime($commande['date_dernier_contact'])) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$additionalScripts = ['/assets/js/employe-commandes.js'];
require_once __DIR__ . '/../../layouts/footer.php'; 
?>
