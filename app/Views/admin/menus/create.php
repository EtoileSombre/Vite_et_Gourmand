<?php
/**
 * Vue Admin - Créer un menu
 */
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><i class="bi bi-plus-circle"></i> Créer un Nouveau Menu</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['flash_error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/admin/menus/store">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du menu <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="titre" 
                                   name="titre" 
                                   required 
                                   maxlength="50"
                                   placeholder="Ex: Menu Méditerranéen">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      maxlength="255"
                                      placeholder="Description du menu (facultatif)"></textarea>
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
                                       placeholder="28.00">
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
                                       value="10"
                                       placeholder="10">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Régime alimentaire -->
                            <div class="col-md-6 mb-3">
                                <label for="regime" class="form-label">Régime alimentaire</label>
                                <select class="form-select" id="regime" name="regime">
                                    <option value="">-- Aucun régime spécifique --</option>
                                    <option value="Végétarien">Végétarien</option>
                                    <option value="Végétalien">Végétalien</option>
                                    <option value="Sans gluten">Sans gluten</option>
                                    <option value="Halal">Halal</option>
                                    <option value="Kasher">Kasher</option>
                                </select>
                            </div>

                            <!-- Stock disponible -->
                            <div class="col-md-6 mb-3">
                                <label for="quantite_restante" class="form-label">Stock disponible</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quantite_restante" 
                                       name="quantite_restante" 
                                       min="0" 
                                       value="100"
                                       placeholder="100">
                                <div class="form-text">Si 0, le menu sera inactif</div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/admin/menus" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Créer le Menu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
