/**
 * Vite & Gourmand - Module Commandes
 * Gestion du système de commande et calculs de prix
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
            const selectedOption = menuSelect.options[menuSelect.selectedIndex];
            const minPersonnes = parseInt(selectedOption.dataset.min) || 2;
            
            nbPersonnesInput.min = minPersonnes;
            
            // Si le nombre actuel est inférieur au minimum, l'ajuster
            if (parseInt(nbPersonnesInput.value) < minPersonnes) {
                nbPersonnesInput.value = minPersonnes;
            }
            const minPersonnesInfo = document.getElementById('min-personnes-info');
            if (minPersonnesInfo && selectedOption.value) {
                minPersonnesInfo.textContent = `Minimum requis : ${minPersonnes} personne${minPersonnes > 1 ? 's' : ''}`;
            }
            
            if (this.app) this.app.updateCalculations();
        });
        
        // Valider le nombre de personnes à la saisie
        nbPersonnesInput.addEventListener('input', () => {
            const minPersonnes = parseInt(nbPersonnesInput.min) || 2;
            const currentValue = parseInt(nbPersonnesInput.value);
            const errorDiv = document.getElementById('nombre-personnes-error');
            
            if (currentValue < minPersonnes) {
                nbPersonnesInput.classList.add('is-invalid');
                nbPersonnesInput.classList.remove('is-valid');
                nbPersonnesInput.setCustomValidity(`Le nombre minimum de personnes est ${minPersonnes}`);
                
                if (errorDiv) {
                    errorDiv.textContent = `Le nombre minimum de personnes pour ce menu est ${minPersonnes}`;
                    errorDiv.style.display = 'block';
                }
            } else {
                nbPersonnesInput.classList.remove('is-invalid');
                nbPersonnesInput.classList.add('is-valid');
                nbPersonnesInput.setCustomValidity('');
                
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            }
            
            if (this.app) this.app.updateCalculations();
        });
        if (distanceInput) {
            distanceInput.addEventListener('input', () => {
                if (this.app) this.app.updateCalculations();
            });
        }
        
        // Calcul initial si menu pré-sélectionné
        if (menuSelect.value && this.app) {
            // Déclencher l'événement change pour initialiser le minimum
            menuSelect.dispatchEvent(new Event('change'));
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

            container.replaceChildren();

            if (this.boissons.length === 0) {
                if (recap) recap.style.display = 'none';
                return;
            }

            const fragment = document.createDocumentFragment();
            this.boissons.forEach((b, index) => {
                const item = document.createElement('div');
                item.className = 'boisson-item';
                item.dataset.index = index;

                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center';

                const infoDiv = document.createElement('div');
                infoDiv.className = 'flex-grow-1';

                const strong = document.createElement('strong');
                strong.textContent = b.nom;
                infoDiv.appendChild(strong);

                const small = document.createElement('small');
                small.className = 'text-muted ms-2';
                small.textContent = b.contenance;
                infoDiv.appendChild(small);

                const priceBadge = document.createElement('div');
                priceBadge.className = 'price-badge';
                priceBadge.textContent = b.prix.toFixed(2) + ' € / unité';
                infoDiv.appendChild(priceBadge);

                const qtyControl = document.createElement('div');
                qtyControl.className = 'qty-control';

                const decreaseBtn = document.createElement('button');
                decreaseBtn.type = 'button';
                decreaseBtn.className = 'btn btn-sm btn-outline-secondary qty-btn rounded-pill';
                decreaseBtn.dataset.action = 'decrease-boisson';
                const dashIcon = document.createElement('i');
                dashIcon.className = 'bi bi-dash';
                decreaseBtn.appendChild(dashIcon);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'number';
                qtyInput.className = 'qty-input';
                qtyInput.value = b.quantite;
                qtyInput.min = '1';
                qtyInput.readOnly = true;

                const increaseBtn = document.createElement('button');
                increaseBtn.type = 'button';
                increaseBtn.className = 'btn btn-sm btn-outline-secondary qty-btn rounded-pill';
                increaseBtn.dataset.action = 'increase-boisson';
                const plusIcon = document.createElement('i');
                plusIcon.className = 'bi bi-plus';
                increaseBtn.appendChild(plusIcon);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger btn-remove rounded-pill';
                removeBtn.dataset.action = 'remove-boisson';
                const trashIcon = document.createElement('i');
                trashIcon.className = 'bi bi-trash';
                removeBtn.appendChild(trashIcon);

                qtyControl.appendChild(decreaseBtn);
                qtyControl.appendChild(qtyInput);
                qtyControl.appendChild(increaseBtn);
                qtyControl.appendChild(removeBtn);

                row.appendChild(infoDiv);
                row.appendChild(qtyControl);
                item.appendChild(row);

                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'boissons[' + index + '][id]';
                hiddenId.value = b.id;

                const hiddenQty = document.createElement('input');
                hiddenQty.type = 'hidden';
                hiddenQty.name = 'boissons[' + index + '][quantite]';
                hiddenQty.value = b.quantite;

                const hiddenPrix = document.createElement('input');
                hiddenPrix.type = 'hidden';
                hiddenPrix.name = 'boissons[' + index + '][prix_unitaire]';
                hiddenPrix.value = b.prix;

                item.appendChild(hiddenId);
                item.appendChild(hiddenQty);
                item.appendChild(hiddenPrix);

                fragment.appendChild(item);
            });

            container.appendChild(fragment);
            
            const total = this.boissons.reduce((sum, b) => sum + (b.prix * b.quantite), 0);
            const totalDisplay = document.getElementById('total_boissons_display');
            if (totalDisplay) totalDisplay.textContent = total.toFixed(2) + ' €';
            if (recap) recap.style.display = 'block';
        },
        
        renderMateriels() {
            const container = document.getElementById('liste_materiel');
            const recap = document.getElementById('recap_materiel');

            if (!container) return;

            container.replaceChildren();

            if (this.materiels.length === 0) {
                if (recap) recap.style.display = 'none';
                return;
            }

            const fragment = document.createDocumentFragment();
            this.materiels.forEach((m, index) => {
                const item = document.createElement('div');
                item.className = 'materiel-item';
                item.dataset.index = index;

                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center';

                const infoDiv = document.createElement('div');
                infoDiv.className = 'flex-grow-1';

                const strong = document.createElement('strong');
                strong.textContent = m.nom;
                infoDiv.appendChild(strong);

                const cautionBadge = document.createElement('div');
                cautionBadge.className = 'caution-badge';
                cautionBadge.textContent = 'Caution: ' + m.caution.toFixed(2) + ' € / unité';
                infoDiv.appendChild(cautionBadge);

                const small = document.createElement('small');
                small.className = 'text-muted';
                small.textContent = 'Max disponible: ' + m.quantiteDispo;
                infoDiv.appendChild(small);

                const qtyControl = document.createElement('div');
                qtyControl.className = 'qty-control';

                const decreaseBtn = document.createElement('button');
                decreaseBtn.type = 'button';
                decreaseBtn.className = 'btn btn-sm btn-outline-secondary qty-btn rounded-pill';
                decreaseBtn.dataset.action = 'decrease-materiel';
                const dashIcon = document.createElement('i');
                dashIcon.className = 'bi bi-dash';
                decreaseBtn.appendChild(dashIcon);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'number';
                qtyInput.className = 'qty-input';
                qtyInput.value = m.quantite;
                qtyInput.min = '1';
                qtyInput.max = m.quantiteDispo;
                qtyInput.readOnly = true;

                const increaseBtn = document.createElement('button');
                increaseBtn.type = 'button';
                increaseBtn.className = 'btn btn-sm btn-outline-secondary qty-btn rounded-pill';
                increaseBtn.dataset.action = 'increase-materiel';
                const plusIcon = document.createElement('i');
                plusIcon.className = 'bi bi-plus';
                increaseBtn.appendChild(plusIcon);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger btn-remove rounded-pill';
                removeBtn.dataset.action = 'remove-materiel';
                const trashIcon = document.createElement('i');
                trashIcon.className = 'bi bi-trash';
                removeBtn.appendChild(trashIcon);

                qtyControl.appendChild(decreaseBtn);
                qtyControl.appendChild(qtyInput);
                qtyControl.appendChild(increaseBtn);
                qtyControl.appendChild(removeBtn);

                row.appendChild(infoDiv);
                row.appendChild(qtyControl);
                item.appendChild(row);

                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'materiels[' + index + '][id]';
                hiddenId.value = m.id;

                const hiddenQty = document.createElement('input');
                hiddenQty.type = 'hidden';
                hiddenQty.name = 'materiels[' + index + '][quantite]';
                hiddenQty.value = m.quantite;

                const hiddenCaution = document.createElement('input');
                hiddenCaution.type = 'hidden';
                hiddenCaution.name = 'materiels[' + index + '][caution_unitaire]';
                hiddenCaution.value = m.caution;

                item.appendChild(hiddenId);
                item.appendChild(hiddenQty);
                item.appendChild(hiddenCaution);

                fragment.appendChild(item);
            });

            container.appendChild(fragment);
            
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
    
    // Initialiser l'app pour create.php si les éléments existent
    if (document.getElementById('btn_ajouter_boisson') || document.getElementById('btn_ajouter_materiel')) {
        CommandeModule.app.init();
    }
});

window.CommandeModule = CommandeModule;
