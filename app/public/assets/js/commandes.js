/**
 * Vite & Gourmand - Module Commandes
 * Gestion du système de commande et calculs de prix
 * @author DWWM - ECF 2025
 */

'use strict';

const CommandeModule = {
    /**
     * Calculer le prix d'une commande
     */
    calculatePrice: function(prixParPersonne, nombrePersonnes, minPersonnes) {
        let prixMenu = prixParPersonne * nombrePersonnes;
        let reduction = 0;
        
        // Réduction -10% si >= minPersonnes + 5
        if (nombrePersonnes >= (minPersonnes + 5)) {
            reduction = prixMenu * 0.10;
            prixMenu -= reduction;
        }
        
        return {
            prixMenu: prixMenu,
            reduction: reduction,
            sousTotal: prixParPersonne * nombrePersonnes
        };
    },

    /**
     * Gérer les confirmations d'annulation de commande
     */
    initConfirmationAnnulation: function() {
        const btnsAnnuler = document.querySelectorAll('.btn-annuler-commande');
        btnsAnnuler.forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    },

    /**
     * Initialiser la page modifier-commande.php
     */
    initModifierCommandePage: function() {
        const menuSelect = document.getElementById('menu_id');
        const personnesInfo = document.getElementById('personnes-info');

        if (!menuSelect || !personnesInfo) {
            return; // Pas sur la page modifier-commande
        }

        menuSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const min = option.dataset.min;
            const max = option.dataset.max;
            const nombrePersonneInput = document.getElementById('nombre_personne');
            
            if (min && max) {
                personnesInfo.textContent = `Ce menu est prévu pour ${min} à ${max} personnes.`;
                nombrePersonneInput.min = min;
                nombrePersonneInput.max = max;
            } else {
                personnesInfo.textContent = '';
            }
        });

        // Déclencher l'événement au chargement si un menu est déjà sélectionné
        if (menuSelect.value) {
            menuSelect.dispatchEvent(new Event('change'));
        }
    },

    /*Initialiser la page nouvelle commande*/
    initCreatePage: function() {
        const alerteLivraison = document.getElementById('alerte-frais-livraison');
        if (alerteLivraison) {
            alerteLivraison.style.display = 'block';
            alerteLivraison.style.visibility = 'visible';
            alerteLivraison.style.opacity = '1';
        }
        
        const menuSelect = document.getElementById('menu_id');
        const nbPersonnesInput = document.getElementById('nombre_personnes');
        const distanceInput = document.getElementById('distance_km');
        
        if (!menuSelect || !nbPersonnesInput) {
            return;
        }
        
        // Événements - utiliser app.updateCalculations() pour gérer tous les calculs
        menuSelect.addEventListener('change', () => {
            if (this.app) this.app.updateCalculations();
        });
        nbPersonnesInput.addEventListener('input', () => {
            if (this.app) this.app.updateCalculations();
        });
        if (distanceInput) {
            distanceInput.addEventListener('input', () => {
                if (this.app) this.app.updateCalculations();
            });
        }
        
        // Calcul initial si menu pré-sélectionné
        if (menuSelect.value && this.app) {
            setTimeout(() => this.app.updateCalculations(), 100);
        }
    },

    /*Gestion boissons et matériels pour create.php*/
    app: {
        boissons: [],
        materiels: [],
        
        init() {
            this.bindEvents();
        },
        
        bindEvents() {
            // Événements boissons
            const btnBoisson = document.getElementById('btn_ajouter_boisson');
            if (btnBoisson) btnBoisson.addEventListener('click', () => this.ajouterBoisson());
            
            // Événements matériel
            const btnMateriel = document.getElementById('btn_ajouter_materiel');
            if (btnMateriel) btnMateriel.addEventListener('click', () => this.ajouterMateriel());
            
            // Délégation d'événements pour les boutons des boissons
            const containerBoissons = document.getElementById('liste_boissons');
            if (containerBoissons) {
                containerBoissons.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-action]');
                    if (!btn) return;
                    
                    const item = btn.closest('[data-index]');
                    if (!item) return;
                    
                    const index = parseInt(item.dataset.index);
                    const action = btn.dataset.action;
                    
                    if (action === 'decrease-boisson') this.updateBoissonQty(index, -1);
                    else if (action === 'increase-boisson') this.updateBoissonQty(index, 1);
                    else if (action === 'remove-boisson') this.removeBoisson(index);
                });
            }
            
            // Délégation d'événements pour les boutons des matériels
            const containerMateriels = document.getElementById('liste_materiel');
            if (containerMateriels) {
                containerMateriels.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-action]');
                    if (!btn) return;
                    
                    const item = btn.closest('[data-index]');
                    if (!item) return;
                    
                    const index = parseInt(item.dataset.index);
                    const action = btn.dataset.action;
                    
                    if (action === 'decrease-materiel') this.updateMaterielQty(index, -1);
                    else if (action === 'increase-materiel') this.updateMaterielQty(index, 1);
                    else if (action === 'remove-materiel') this.removeMateriel(index);
                });
            }
        },
        
        ajouterBoisson() {
            const select = document.getElementById('boisson_select');
            const option = select.options[select.selectedIndex];
            
            if (!option.value) return;
            
            const boisson = {
                id: option.value,
                nom: option.dataset.nom,
                prix: parseFloat(option.dataset.prix),
                contenance: option.dataset.contenance,
                quantite: 1
            };
            
            const existing = this.boissons.find(b => b.id === boisson.id);
            if (existing) {
                alert('Cette boisson est déjà ajoutée. Modifiez sa quantité.');
                return;
            }
            
            this.boissons.push(boisson);
            this.renderBoissons();
            this.updateCalculations();
            select.selectedIndex = 0;
        },
        
        ajouterMateriel() {
            const select = document.getElementById('materiel_select');
            const option = select.options[select.selectedIndex];
            
            if (!option.value) return;
            
            const materiel = {
                id: option.value,
                nom: option.dataset.nom,
                caution: parseFloat(option.dataset.caution),
                quantiteDispo: parseInt(option.dataset.quantiteDispo),
                quantite: 1
            };
            
            const existing = this.materiels.find(m => m.id === materiel.id);
            if (existing) {
                alert('Ce matériel est déjà ajouté. Modifiez sa quantité.');
                return;
            }
            
            this.materiels.push(materiel);
            this.renderMateriels();
            this.updateCalculations();
            
            const pretMateriel = document.getElementById('pret_materiel');
            if (pretMateriel) pretMateriel.value = this.materiels.length > 0 ? '1' : '0';
            
            select.selectedIndex = 0;
        },
        
        renderBoissons() {
            const container = document.getElementById('liste_boissons');
            const recap = document.getElementById('recap_boissons');
            
            if (!container) return;
            
            if (this.boissons.length === 0) {
                container.innerHTML = '';
                if (recap) recap.style.display = 'none';
                return;
            }
            
            container.innerHTML = this.boissons.map((b, index) => `
                <div class="boisson-item" data-index="${index}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <strong>${b.nom}</strong>
                            <small class="text-muted ms-2">${b.contenance}</small>
                            <div class="price-badge">${b.prix.toFixed(2)} € / unité</div>
                        </div>
                        <div class="qty-control">
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-btn rounded-pill" data-action="decrease-boisson">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="qty-input" value="${b.quantite}" min="1" readonly>
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-btn rounded-pill" data-action="increase-boisson">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-remove rounded-pill" data-action="remove-boisson">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="boissons[${index}][id]" value="${b.id}">
                    <input type="hidden" name="boissons[${index}][quantite]" value="${b.quantite}">
                    <input type="hidden" name="boissons[${index}][prix_unitaire]" value="${b.prix}">
                </div>
            `).join('');
            
            const total = this.boissons.reduce((sum, b) => sum + (b.prix * b.quantite), 0);
            const totalDisplay = document.getElementById('total_boissons_display');
            if (totalDisplay) totalDisplay.textContent = total.toFixed(2) + ' €';
            if (recap) recap.style.display = 'block';
        },
        
        renderMateriels() {
            const container = document.getElementById('liste_materiel');
            const recap = document.getElementById('recap_materiel');
            
            if (!container) return;
            
            if (this.materiels.length === 0) {
                container.innerHTML = '';
                if (recap) recap.style.display = 'none';
                return;
            }
            
            container.innerHTML = this.materiels.map((m, index) => `
                <div class="materiel-item" data-index="${index}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <strong>${m.nom}</strong>
                            <div class="caution-badge">Caution: ${m.caution.toFixed(2)} € / unité</div>
                            <small class="text-muted">Max disponible: ${m.quantiteDispo}</small>
                        </div>
                        <div class="qty-control">
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-btn rounded-pill" data-action="decrease-materiel">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="qty-input" value="${m.quantite}" min="1" max="${m.quantiteDispo}" readonly>
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-btn rounded-pill" data-action="increase-materiel">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-remove rounded-pill" data-action="remove-materiel">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="materiels[${index}][id]" value="${m.id}">
                    <input type="hidden" name="materiels[${index}][quantite]" value="${m.quantite}">
                    <input type="hidden" name="materiels[${index}][caution_unitaire]" value="${m.caution}">
                </div>
            `).join('');
            
            const totalCaution = this.materiels.reduce((sum, m) => sum + (m.caution * m.quantite), 0);
            const totalDisplay = document.getElementById('total_caution_display');
            if (totalDisplay) totalDisplay.textContent = totalCaution.toFixed(2) + ' €';
            if (recap) recap.style.display = 'block';
        },
        
        updateBoissonQty(index, delta) {
            this.boissons[index].quantite = Math.max(1, this.boissons[index].quantite + delta);
            this.renderBoissons();
            this.updateCalculations();
        },
        
        setBoissonQty(index, value) {
            this.boissons[index].quantite = Math.max(1, parseInt(value) || 1);
            this.renderBoissons();
            this.updateCalculations();
        },
        
        removeBoisson(index) {
            this.boissons.splice(index, 1);
            this.renderBoissons();
            this.updateCalculations();
        },
        
        updateMaterielQty(index, delta) {
            const newQty = this.materiels[index].quantite + delta;
            if (newQty >= 1 && newQty <= this.materiels[index].quantiteDispo) {
                this.materiels[index].quantite = newQty;
                this.renderMateriels();
                this.updateCalculations();
            }
        },
        
        setMaterielQty(index, value) {
            const qty = parseInt(value) || 1;
            this.materiels[index].quantite = Math.max(1, Math.min(qty, this.materiels[index].quantiteDispo));
            this.renderMateriels();
            this.updateCalculations();
        },
        
        removeMateriel(index) {
            this.materiels.splice(index, 1);
            this.renderMateriels();
            this.updateCalculations();
            const pretMateriel = document.getElementById('pret_materiel');
            if (pretMateriel) pretMateriel.value = this.materiels.length > 0 ? '1' : '0';
        },
        
        updateCalculations() {
            const menuSelect = document.getElementById('menu_id');
            if (!menuSelect) return;
            
            const option = menuSelect.options[menuSelect.selectedIndex];
            
            if (!option.value) {
                const prixMenuBase = document.getElementById('prix-menu-base');
                const totalFinal = document.getElementById('total-final');
                const fraisLivraison = document.getElementById('frais-livraison');
                if (prixMenuBase) prixMenuBase.textContent = '0,00';
                if (totalFinal) totalFinal.textContent = '0,00';
                if (fraisLivraison) fraisLivraison.textContent = '5,00';
                return;
            }
            
            const prixParPersonne = parseFloat(option.dataset.prix);
            const minPersonnes = parseInt(option.dataset.min);
            const nombrePersonnesInput = document.getElementById('nombre_personnes');
            const distanceKmInput = document.getElementById('distance_km');
            
            if (!nombrePersonnesInput || !distanceKmInput) return;
            
            const nombrePersonnes = parseInt(nombrePersonnesInput.value) || 0;
            const distanceKm = parseFloat(distanceKmInput.value) || 0;
            
            // Mise à jour info minimum
            const minPersonnesInfo = document.getElementById('min-personnes-info');
            if (minPersonnesInfo) {
                minPersonnesInfo.textContent = `Minimum requis : ${minPersonnes} personne${minPersonnes > 1 ? 's' : ''}`;
            }
            
            // Calculs
            const prixMenuBase = prixParPersonne * nombrePersonnes;
            let reduction = 0;
            
            // Réduction 10% si +5 personnes au-dessus du minimum
            const reductionRow = document.getElementById('reduction-row');
            
            if (nombrePersonnes >= (minPersonnes + 5)) {
                reduction = prixMenuBase * 0.10;
                if (reductionRow) reductionRow.classList.remove('d-none');
            } else {
                if (reductionRow) reductionRow.classList.add('d-none');
            }
            
            const totalBoissons = this.boissons.reduce((sum, b) => sum + (b.prix * b.quantite), 0);
            const fraisLivraison = 5.00 + (distanceKm * 0.59);
            const totalHT = prixMenuBase - reduction + totalBoissons + fraisLivraison;
            const montantTVA = totalHT * 0.10; // TVA à 10%
            const totalTTC = totalHT + montantTVA;
            const totalCaution = this.materiels.reduce((sum, m) => sum + (m.caution * m.quantite), 0);
            
            // Affichage
            const prixMenuBaseEl = document.getElementById('prix-menu-base');
            const montantReductionEl = document.getElementById('montant-reduction');
            const fraisLivraisonEl = document.getElementById('frais-livraison');
            const totalHTEl = document.getElementById('total-ht');
            const montantTVAEl = document.getElementById('montant-tva');
            const totalFinalEl = document.getElementById('total-final');
            
            if (prixMenuBaseEl) prixMenuBaseEl.textContent = prixMenuBase.toFixed(2);
            if (montantReductionEl) montantReductionEl.textContent = reduction.toFixed(2);
            if (fraisLivraisonEl) fraisLivraisonEl.textContent = fraisLivraison.toFixed(2);
            if (totalHTEl) totalHTEl.textContent = totalHT.toFixed(2);
            if (montantTVAEl) montantTVAEl.textContent = montantTVA.toFixed(2);
            if (totalFinalEl) totalFinalEl.textContent = totalTTC.toFixed(2);
            
            // Boissons
            const rowBoissons = document.getElementById('row-boissons');
            const montantBoissons = document.getElementById('montant-boissons');
            if (totalBoissons > 0 && rowBoissons && montantBoissons) {
                rowBoissons.classList.remove('d-none');
                montantBoissons.textContent = totalBoissons.toFixed(2);
            } else if (rowBoissons) {
                rowBoissons.classList.add('d-none');
            }
            
            // Caution
            const rowCaution = document.getElementById('row-caution');
            const montantCautionFinal = document.getElementById('montant-caution-final');
            if (totalCaution > 0 && rowCaution && montantCautionFinal) {
                rowCaution.classList.remove('d-none');
                montantCautionFinal.textContent = totalCaution.toFixed(2);
            } else if (rowCaution) {
                rowCaution.classList.add('d-none');
            }
        }
    },

    /*Initialiser la page annuler-commande*/
    initAnnulerCommandePage: function() {
        const motifSelect = document.getElementById('motif_annulation_select');
        const motifTextarea = document.getElementById('motif_annulation');

        if (!motifSelect || !motifTextarea) {
            return; // Pas sur la page annuler-commande
        }

        motifSelect.addEventListener('change', function() {
            if (this.value && this.value !== 'Autre') {
                motifTextarea.value = this.value;
            } else if (this.value === 'Autre') {
                motifTextarea.value = '';
                motifTextarea.focus();
            }
        });
    },

    /*Initialiser la page view.php (détails commande employé)*/
    initEmployeViewPage: function() {
        // Bouton pour afficher le formulaire de modification
        const btnShowEditForm = document.querySelector('[data-action="show-edit-form"]');
        if (btnShowEditForm) {
            btnShowEditForm.addEventListener('click', function() {
                const editForm = document.getElementById('formEditCommandeSection');
                if (editForm) {
                    editForm.classList.remove('d-none');
                    editForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        }

        // Bouton pour annuler la modification
        const btnCancelEdit = document.querySelector('[data-action="hide-edit-form"]');
        if (btnCancelEdit) {
            btnCancelEdit.addEventListener('click', function() {
                const editForm = document.getElementById('formEditCommandeSection');
                if (editForm) {
                    editForm.classList.add('d-none');
                }
            });
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    CommandeModule.initCreatePage();
    CommandeModule.initModifierCommandePage();
    CommandeModule.initAnnulerCommandePage();
    CommandeModule.initEmployeViewPage();
    CommandeModule.initConfirmationAnnulation();
    
    // Initialiser l'app pour create.php si les éléments existent
    if (document.getElementById('btn_ajouter_boisson') || document.getElementById('btn_ajouter_materiel')) {
        CommandeModule.app.init();
    }
});

window.CommandeModule = CommandeModule;
