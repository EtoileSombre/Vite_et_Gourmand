<?php
$additionalStyles = ['/assets/css/pages/commandes.css'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt"></i> Commande #<?= htmlspecialchars($commande->getNumeroCommande()) ?></h1>
        <a href="/mes-commandes" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <!-- Informations de la commande -->
        <div class="col-lg-8">
            <!-- Récapitulatif Commande -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">Votre commande</h5>
                </div>
                <div class="card-body">
                    <!-- Menus -->
                    <?php if (!empty($commande->getLignesMenus())): ?>
                        <h6 class="text-vg-bordeaux mb-3">Menus</h6>
                        <?php foreach ($commande->getLignesMenus() as $ligne): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <strong><?= htmlspecialchars($ligne->getMenuNom()) ?></strong><br>
                                    <small class="text-muted"><?= $ligne->getNombrePersonne() ?> personne(s)</small>
                                </div>
                                <div class="text-end">
                                    <strong><?= number_format($ligne->getTotalLigne(), 2) ?> €</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Matériel -->
                    <?php if (!empty($commande->getLignesMateriels())): ?>
                        <h6 class="text-vg-bordeaux mb-3 mt-4">Matériel emprunté</h6>
                        <?php foreach ($commande->getLignesMateriels() as $mat): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <strong><?= htmlspecialchars($mat['nom']) ?></strong>
                                    <?php if (!empty($mat['description'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($mat['description']) ?></small>
                                    <?php endif; ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($mat['quantite']) ?> pièce(s)</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-warning">Caution: <?= number_format($mat['total_caution'], 2) ?> €</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Boissons -->
                    <?php if (!empty($commande->getLignesBoissons())): ?>
                        <h6 class="text-vg-bordeaux mb-3 mt-4">Boissons</h6>
                        <?php foreach ($commande->getLignesBoissons() as $boisson): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <strong><?= htmlspecialchars($boisson['nom']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($boisson['contenance']) ?> × <?= htmlspecialchars($boisson['quantite']) ?></small>
                                </div>
                                <div class="text-end">
                                    <strong><?= number_format($boisson['total_ligne'], 2) ?> €</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Totaux -->
                    <hr class="my-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total menus :</span>
                        <strong><?= number_format($commande->getSousTotal() ?? 0, 2) ?> €</strong>
                    </div>
                    <?php if (!empty($commande->getTotalBoissons())): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total boissons :</span>
                            <strong><?= number_format($commande->getTotalBoissons(), 2) ?> €</strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($commande->getPrixLivraison() && $commande->getPrixLivraison() > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frais de livraison<?php if ($commande->getDistanceKm() && $commande->getDistanceKm() > 0): ?> (<?= number_format($commande->getDistanceKm(), 1) ?> km)<?php endif; ?> :</span>
                            <strong><?= number_format($commande->getPrixLivraison(), 2) ?> €</strong>
                        </div>
                    <?php endif; ?>
                    
                    <hr class="my-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Total HT :</span>
                        <span class="small text-muted"><?= number_format(($commande->getTotalFinal() ?? 0) / 1.1, 2) ?> €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">TVA (10%) :</span>
                        <span class="small text-muted"><?= number_format(($commande->getTotalFinal() ?? 0) - (($commande->getTotalFinal() ?? 0) / 1.1), 2) ?> €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">TOTAL TTC :</h5>
                        <h5 class="mb-0 text-vg-bordeaux"><?= number_format($commande->getTotalFinal() ?? 0, 2) ?> €</h5>
                    </div>

                    <?php if (!empty($commande->getTotalCaution())): ?>
                        <div class="alert alert-warning mb-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>Cautions matériel</strong> <small class="text-muted">(restituable)</small></span>
                                <strong class="text-warning"><?= number_format($commande->getTotalCaution(), 2) ?> €</strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informations générales -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h5 class="mb-0">Informations de livraison</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Date de commande :</dt>
                        <dd class="col-sm-8"><?= date('d/m/Y à H:i', strtotime($commande->getDateCommande())) ?></dd>

                        <dt class="col-sm-4">Date de prestation :</dt>
                        <dd class="col-sm-8">
                            <strong><?= date('d/m/Y', strtotime($commande->getDatePrestation())) ?></strong>
                            <?php if (!empty($commande->getHeureLivraison())): ?>
                                à <?= htmlspecialchars($commande->getHeureLivraison()) ?>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4">Lieu de livraison :</dt>
                        <dd class="col-sm-8">
                            <?= htmlspecialchars($commande->getLieuLivraison() ?? 'Non renseigné') ?>
                            <?php if (!empty($commande->getVilleLivraison())): ?>
                                <br><small class="text-muted">
                                    <?= htmlspecialchars($commande->getVilleLivraison()) ?>
                                    <?php if (!empty($commande->getCodePostalLivraison())): ?>
                                        <?= htmlspecialchars($commande->getCodePostalLivraison()) ?>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </dd>
                    </dl>

                    <!-- Carte OpenStreetMap -->
                    <?php if (!empty($commande->getLieuLivraison()) && !empty($commande->getVilleLivraison())): ?>
                        <hr class="my-3">
                        <div id="map-<?= htmlspecialchars($commande->getNumeroCommande()) ?>" class="map-container-small rounded"></div>
                        
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                        <script>
                            (function() {
                                const address = <?= json_encode($commande->getLieuLivraison() . ', ' . $commande->getVilleLivraison() . ' ' . ($commande->getCodePostalLivraison() ?? '')) ?>;
                                const mapId = 'map-<?= htmlspecialchars($commande->getNumeroCommande()) ?>';
                                
                                // Attendre le chargement complet de la page et de Leaflet
                                function initMap() {
                                    if (typeof L === 'undefined') {
                                        console.log('Leaflet pas encore chargé, nouvelle tentative...');
                                        setTimeout(initMap, 150);
                                        return;
                                    }
                                    
                                    console.log('Initialisation de la carte pour:', address);
                                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.length > 0) {
                                            const lat = parseFloat(data[0].lat);
                                            const lon = parseFloat(data[0].lon);
                                            
                                            const map = L.map(mapId).setView([lat, lon], 15);
                                            
                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                attribution: '&copy; OpenStreetMap',
                                                maxZoom: 19
                                            }).addTo(map);
                                            
                                            L.marker([lat, lon]).addTo(map)
                                                .bindPopup('<strong>Lieu de livraison</strong><br>' + address)
                                                .openPopup();
                                        } else {
                                            // Fallback : essayer juste avec la ville
                                            console.log('Adresse précise non trouvée, recherche de la ville...');
                                            const ville = '<?= addslashes($commande->getVilleLivraison()) ?>';
                                            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(ville + ', France'))
                                                .then(response => response.json())
                                                .then(villeData => {
                                                    if (villeData && villeData.length > 0) {
                                                        const lat = parseFloat(villeData[0].lat);
                                                        const lon = parseFloat(villeData[0].lon);
                                                        
                                                        const map = L.map(mapId).setView([lat, lon], 12);
                                                        
                                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                            attribution: '&copy; OpenStreetMap',
                                                            maxZoom: 19
                                                        }).addTo(map);
                                                        
                                                        L.marker([lat, lon]).addTo(map)
                                                            .bindPopup('<strong>Zone de livraison</strong><br>' + ville + '<br><small class="text-muted">Adresse précise non géolocalisée</small>');
                                                        
                                                        document.getElementById(mapId).style.opacity = '0.8';
                                                    } else {
                                                        document.getElementById(mapId).innerHTML = '<div class="alert alert-warning mb-0"><i class="bi bi-geo-alt-fill"></i> Adresse non géolocalisable</div>';
                                                    }
                                                })
                                                .catch(() => {
                                                    document.getElementById(mapId).innerHTML = '<div class="alert alert-warning mb-0"><i class="bi bi-geo-alt-fill"></i> Adresse non géolocalisable</div>';
                                                });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Erreur carte:', error);
                                        document.getElementById(mapId).innerHTML = '<div class="alert alert-danger mb-0">Erreur de chargement</div>';
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
                </div>
            </div>
        </div>

        <!-- Suivi de la commande -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-vg-gold">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Suivi de la Commande</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($historique)): ?>
                        <div class="timeline">
                            <?php foreach ($historique as $index => $suivi): ?>
                                <div class="timeline-item mb-3 pb-3 <?= $index < count($historique) - 1 ? 'border-bottom' : '' ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <?php
                                            $timelineStatut = $suivi->getNouveauStatut();
                                            $timelineBadgeClass = 'badge-statut-' . str_replace('_', '-', $timelineStatut);
                                            ?>
                                            <div class="rounded-circle p-2 timeline-badge <?= $timelineBadgeClass ?>">
                                                <i class="bi bi-<?= match($suivi->getNouveauStatut()) {
                                                    'en_attente' => 'hourglass-split',
                                                    'acceptee' => 'check-circle',
                                                    'en_preparation' => 'gear',
                                                    'en_cours_livraison' => 'truck',
                                                    'livree' => 'box-seam',
                                                    'attente_retour_materiel' => 'arrow-return-left',
                                                    'terminee' => 'check-all',
                                                    'annulee' => 'x-circle',
                                                    default => 'circle'
                                                } ?>"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= $statuts[$suivi->getNouveauStatut()] ?? ucfirst(str_replace('_', ' ', $suivi->getNouveauStatut())) ?></h6>
                                            <small class="text-muted">
                                                <?= date('d/m/Y à H:i', strtotime($suivi->getDateChangement())) ?>
                                            </small>
                                            <?php if (!empty($suivi->getEmployePrenom())): ?>
                                                <br><small class="text-muted">
                                                    Par <?= htmlspecialchars($suivi->getEmployePrenom()) ?> 
                                                    <?= htmlspecialchars($suivi->getEmployeNom()) ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if (!empty($suivi->getCommentaire())): ?>
                                                <div class="mt-1">
                                                    <small><em><?= htmlspecialchars($suivi->getCommentaire()) ?></em></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucun suivi disponible pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation -->
<div class="modal fade" id="modalAnnulerCommande" tabindex="-1" aria-labelledby="modalAnnulerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-vg-bordeaux text-white">
                <h5 class="modal-title" id="modalAnnulerLabel">
                    Confirmer l'annulation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="bi bi-x-circle fs-1 text-vg-bordeaux"></i>
                </div>
                <h5 class="mb-3">Êtes-vous sûr de vouloir annuler cette commande ?</h5>
                <p class="text-muted mb-4">
                    Cette action est irréversible. Vous devrez créer une nouvelle commande si vous changez d'avis.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="#" id="confirmAnnulerBtn" class="btn btn-vg-bordeaux rounded-pill">
                    <i class="bi bi-x-circle"></i> Oui, annuler
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$additionalScripts = ['/assets/js/modales.js'];
include __DIR__ . '/../../layouts/footer.php'; 
?>
