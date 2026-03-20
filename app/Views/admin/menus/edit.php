<?php
/**
 * Vue Admin - Modifier un menu
 */
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white bg-vg-bordeaux">
                    <h2 class="mb-0"><i class="bi bi-pencil"></i> Modifier le Menu</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['flash_error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/admin/menus/update">
                        <?= csrf_field() ?>
                        <input type="hidden" name="menu_id" value="<?= htmlspecialchars($menu['menu_id']) ?>">

                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du menu <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="titre" 
                                   name="titre" 
                                   required 
                                   maxlength="50"
                                   value="<?= htmlspecialchars($menu['titre']) ?>">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      maxlength="255"><?= htmlspecialchars($menu['description'] ?? '') ?></textarea>
                            <div class="form-text">Maximum 255 caractères</div>
                        </div>

                        <div class="row">
                            <!-- Prix par personne -->
                            <div class="col-md-6 mb-3">
                                <label for="prix_par_personne" class="form-label">Prix par personne (€) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       id="prix_par_personne" 
                                       name="prix_par_personne" 
                                       required 
                                       min="1" 
                                       step="0.01"
                                       value="<?= htmlspecialchars($menu['prix_par_personne']) ?>">
                            </div>

                            <!-- Nombre de personnes minimum -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre_personne_minimum" class="form-label">Nombre de personnes minimum <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       id="nombre_personne_minimum" 
                                       name="nombre_personne_minimum" 
                                       required 
                                       min="1" 
                                       value="<?= htmlspecialchars($menu['nombre_personne_minimum'] ?? 10) ?>">
                            </div>
                        </div>

                        <!-- Stock disponible -->
                        <div class="mb-3">
                            <div class="col-md-6">
                                <label for="quantite_restante" class="form-label">Stock disponible</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quantite_restante" 
                                       name="quantite_restante" 
                                       min="0" 
                                       value="<?= htmlspecialchars($menu['quantite_restante'] ?? 0) ?>">
                                <div class="form-text">Si 0, le menu sera inactif</div>
                            </div>
                        </div>

                        <!-- Composition du menu (plats) -->
                        <div class="mb-4 mt-4">
                            <h5 class="mb-3"><i class="bi bi-list-check"></i> Composition du menu</h5>
                            <p class="text-muted small">Sélectionnez les plats qui composent ce menu</p>
                            
                            <?php 
                            $types = ['Entree' => 'Entrées', 'Plat' => 'Plats', 'Dessert' => 'Desserts', 'Accompagnement' => 'Accompagnements'];
                            foreach ($types as $type => $label): 
                                $platsType = array_filter($plats, fn($p) => $p['type_plat'] === $type);
                                if (empty($platsType)) continue;
                            ?>
                                <div class="mb-3">
                                    <strong class="d-block mb-2"><?= htmlspecialchars($label) ?></strong>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($platsType as $plat): 
                                            // Les allergènes sont déjà chargés par le contrôleur
                                            $allergenesText = !empty($plat['allergenes']) ? 'Allergènes: ' . implode(', ', $plat['allergenes']) : '';
                                        ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="plats[]" 
                                                       value="<?= $plat['plat_id'] ?>"
                                                       id="plat_<?= $plat['plat_id'] ?>"
                                                       <?= in_array($plat['plat_id'], $platIds) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="plat_<?= $plat['plat_id'] ?>" title="<?= htmlspecialchars(($plat['description'] ?? '') . ($allergenesText ? ' | ' . $allergenesText : '')) ?>">
                                                    <?= htmlspecialchars($plat['titre_plat']) ?>
                                                    <?php if (!empty($allergenesLabels)): ?>
                                                        <span class="text-danger small">⚠️</span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/admin/menus" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-vg-gold rounded-pill">
                                <i class="bi bi-save"></i> Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
