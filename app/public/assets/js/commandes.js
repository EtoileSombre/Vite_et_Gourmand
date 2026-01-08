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
     * Initialiser le calcul dynamique sur la page commander.php
     */
    initCommanderPage: function() {
        const menuSelect = document.getElementById('menu_id');
        const nombrePersonneInput = document.getElementById('nombre_personne');
        const summaryDiv = document.getElementById('summary');
        const reductionInfo = document.getElementById('reductionInfo');

        if (!menuSelect || !nombrePersonneInput || !summaryDiv) {
            return; // Pas sur la page commander
        }

        function updateSummary() {
            const selectedOption = menuSelect.options[menuSelect.selectedIndex];
            const prixParPersonne = parseFloat(selectedOption.dataset.prix) || 0;
            const minPersonnes = parseInt(selectedOption.dataset.min) || 0;
            const nombrePersonnes = parseInt(nombrePersonneInput.value) || 0;
            
            if (prixParPersonne === 0 || nombrePersonnes === 0) {
                summaryDiv.innerHTML = '<p class="text-muted text-center">Sélectionnez un menu et le nombre de personnes</p>';
                if (reductionInfo) reductionInfo.style.display = 'none';
                return;
            }
            
            // Mettre à jour le min du input
            nombrePersonneInput.min = minPersonnes;
            if (nombrePersonnes < minPersonnes) {
                nombrePersonneInput.value = minPersonnes;
                return;
            }
            
            // Calculs
            const calcul = CommandeModule.calculatePrice(prixParPersonne, nombrePersonnes, minPersonnes);
            
            // Afficher ou masquer l'info réduction
            if (reductionInfo) {
                reductionInfo.style.display = calcul.reduction > 0 ? 'block' : 'none';
            }
            
            // Frais de livraison (calculés côté serveur selon la ville)
            const fraisLivraisonEstime = '<em>calculés selon la ville</em>';
            
            summaryDiv.innerHTML = `
                <table class="table table-sm">
                    <tr>
                        <td>Menu sélectionné:</td>
                        <td class="text-end"><strong>${selectedOption.text.split(' - ')[0]}</strong></td>
                    </tr>
                    <tr>
                        <td>Prix unitaire:</td>
                        <td class="text-end">${prixParPersonne.toFixed(2).replace('.', ',')} €</td>
                    </tr>
                    <tr>
                        <td>Nombre de personnes:</td>
                        <td class="text-end">${nombrePersonnes}</td>
                    </tr>
                    <tr>
                        <td>Sous-total menu:</td>
                        <td class="text-end">${calcul.sousTotal.toFixed(2).replace('.', ',')} €</td>
                    </tr>
                    ${calcul.reduction > 0 ? `
                    <tr class="text-success">
                        <td>Réduction -10%:</td>
                        <td class="text-end">-${calcul.reduction.toFixed(2).replace('.', ',')} €</td>
                    </tr>
                    ` : ''}
                    <tr>
                        <td>Prix menu final:</td>
                        <td class="text-end"><strong>${calcul.prixMenu.toFixed(2).replace('.', ',')} €</strong></td>
                    </tr>
                    <tr>
                        <td>Frais de livraison:</td>
                        <td class="text-end">${fraisLivraisonEstime}</td>
                    </tr>
                </table>
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle"></i>
                    Le montant final sera calculé en fonction de votre ville de livraison
                </div>
            `;
        }

        menuSelect.addEventListener('change', updateSummary);
        nombrePersonneInput.addEventListener('input', updateSummary);

        // Initialisation
        if (menuSelect.value) {
            updateSummary();
        }
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
        const villeLivraisonInput = document.getElementById('ville_livraison');
        const distanceInput = document.getElementById('distance_km');
        const distanceGroup = document.getElementById('distance-group');
        
        const prixMenuBase = document.getElementById('prix-menu-base');
        const montantReduction = document.getElementById('montant-reduction');
        const fraisLivraison = document.getElementById('frais-livraison');
        const totalFinal = document.getElementById('total-final');
        
        const reductionAlert = document.getElementById('reduction-alert');
        const reductionRow = document.getElementById('reduction-row');
        const minPersonnesInfo = document.getElementById('min-personnes-info');
        
        if (!menuSelect || !nbPersonnesInput) {
            return;
        }
        
        function calculerPrix() {
            const menuOption = menuSelect.options[menuSelect.selectedIndex];
            if (!menuOption || !menuOption.value) {
                return;
            }
            
            const prixParPersonne = parseFloat(menuOption.dataset.prix) || 0;
            const minPersonnes = parseInt(menuOption.dataset.minPersonnes) || 2;
            const nbPersonnes = parseInt(nbPersonnesInput.value) || 0;
            
            // Validation minimum personnes
            nbPersonnesInput.min = minPersonnes;
            minPersonnesInfo.textContent = 'Minimum ' + minPersonnes + ' personnes pour ce menu';
            
            if (nbPersonnes < minPersonnes) {
                minPersonnesInfo.classList.add('text-danger');
                return;
            } else {
                minPersonnesInfo.classList.remove('text-danger');
            }
            
            // 1. Prix de base
            const prixBase = prixParPersonne * nbPersonnes;
            prixMenuBase.textContent = prixBase.toFixed(2);
            
            // 2. Réduction 10% si >= (min + 5)
            let reduction = 0;
            if (nbPersonnes >= (minPersonnes + 5)) {
                reduction = prixBase * 0.10;
                montantReduction.textContent = reduction.toFixed(2);
                reductionAlert.style.display = 'block';
                reductionRow.style.display = 'flex';
            } else {
                reductionAlert.style.display = 'none';
                reductionRow.style.display = 'none';
            }
            
            // 3. Frais livraison
            if (!villeLivraisonInput || !distanceInput || !distanceGroup) {
                return;
            }
            
            const ville = villeLivraisonInput.value.toLowerCase().trim();
            let frais = 5.00;
            
            if (ville === 'bordeaux') {
                frais = 5.00;
                distanceGroup.style.display = 'none';
                distanceInput.value = 0;
            } else {
                const distance = parseFloat(distanceInput.value) || 0;
                frais = 5.00 + (distance * 0.59);
                distanceGroup.style.display = 'block';
            }
            
            fraisLivraison.textContent = frais.toFixed(2);
            
            // 4. Total final
            const total = (prixBase - reduction) + frais;
            totalFinal.textContent = total.toFixed(2);
        }
        
        // Événements
        menuSelect.addEventListener('change', calculerPrix);
        nbPersonnesInput.addEventListener('input', calculerPrix);
        villeLivraisonInput.addEventListener('input', calculerPrix);
        distanceInput.addEventListener('input', calculerPrix);
        
        // Calcul initial si menu pré-sélectionné
        if (menuSelect.value) {
            calculerPrix();
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
    }
};

document.addEventListener('DOMContentLoaded', function() {
    CommandeModule.initCreatePage();
    CommandeModule.initCommanderPage();
    CommandeModule.initModifierCommandePage();
    CommandeModule.initAnnulerCommandePage();
    CommandeModule.initConfirmationAnnulation();
});

window.CommandeModule = CommandeModule;
