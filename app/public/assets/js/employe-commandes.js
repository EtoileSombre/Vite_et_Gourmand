/**
 * Gestion des événements pour la page de gestion des commandes employé
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    
    if (filterForm) {
        // Auto-submit quand on change le statut
        const selectStatut = document.getElementById('statut');
        if (selectStatut) {
            selectStatut.addEventListener('change', function() {
                filterForm.submit();
            });
        }

        // Auto-submit quand on coche/décoche "Prestations aujourd'hui"
        const checkboxAujourdhui = document.getElementById('aujourdhui');
        if (checkboxAujourdhui) {
            checkboxAujourdhui.addEventListener('change', function() {
                filterForm.submit();
            });
        }

        // Debounce pour l'input utilisateur (500ms après la dernière frappe)
        const inputUtilisateur = document.getElementById('utilisateur');
        if (inputUtilisateur) {
            let debounceTimer;
            inputUtilisateur.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            });
        }
    }
    
    const editSection = document.getElementById('formEditCommandeSection');
    
    if (editSection) {
        // Boutons pour afficher le formulaire
        const showButtons = document.querySelectorAll('[data-action="show-edit-form"]');
        
        showButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                editSection.classList.remove('d-none');
                editSection.scrollIntoView({behavior: 'smooth'});
            });
        });
        
        // Bouton pour masquer le formulaire
        document.querySelectorAll('[data-action="hide-edit-form"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                editSection.classList.add('d-none');
            });
        });
        
        // Bouton reset du formulaire
        const editForm = document.getElementById('formEditCommande');
        if (editForm) {
            document.querySelectorAll('[data-action="reset-edit-form"]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    editForm.reset();
                });
            });
            
            // Gestion de la case "Annuler commande"
            const checkboxAnnuler = document.getElementById('annuler_commande');
            if (checkboxAnnuler) {
                checkboxAnnuler.addEventListener('change', function() {
                    // Rendre les champs de modification optionnels si annulation
                    const champsModif = editForm.querySelectorAll('input[name="date_prestation"], input[name="heure_livraison"], input[name="lieu_livraison"], input[name="ville_livraison"], input[name="code_postal_livraison"]');
                    const champsQuantite = editForm.querySelectorAll('input[name^="quantite_menu"]');
                    
                    if (this.checked) {
                        // Rendre optionnels les champs de modification
                        champsModif.forEach(champ => champ.removeAttribute('required'));
                        champsQuantite.forEach(champ => champ.removeAttribute('required'));
                        
                        // Désactiver visuellement
                        champsModif.forEach(champ => {
                            champ.style.opacity = '0.5';
                            champ.disabled = true;
                        });
                        champsQuantite.forEach(champ => {
                            champ.style.opacity = '0.5';
                            champ.disabled = true;
                        });
                    } else {
                        // Remettre required
                        champsModif.forEach(champ => champ.setAttribute('required', 'required'));
                        champsQuantite.forEach(champ => champ.setAttribute('required', 'required'));
                        
                        // Réactiver
                        champsModif.forEach(champ => {
                            champ.style.opacity = '1';
                            champ.disabled = false;
                        });
                        champsQuantite.forEach(champ => {
                            champ.style.opacity = '1';
                            champ.disabled = false;
                        });
                    }
                });
            }
        }
    }

    const formChangeStatus = document.getElementById('formChangeStatus');
    const selectNouveauStatut = document.getElementById('nouveau_statut');
    const contactSection = document.getElementById('contactUtilisateurSection');
    
    if (formChangeStatus && selectNouveauStatut) {
        const checkboxContact = document.getElementById('contacte_utilisateur');
        const motifContact = document.getElementById('motif_contact');
        const modesContact = document.getElementsByName('mode_contact');
        
        // Rendre les champs de contact obligatoires si le statut est déjà annulee au chargement
        if (selectNouveauStatut && selectNouveauStatut.value === 'annulee') {
            if (contactSection) {
                contactSection.classList.remove('d-none');
                
                // Rendre les champs obligatoires
                if (checkboxContact) checkboxContact.setAttribute('required', 'required');
                if (motifContact) motifContact.setAttribute('required', 'required');
                modesContact.forEach(radio => radio.setAttribute('required', 'required'));
            }
        }
        
        // Afficher/masquer la section contact selon le statut choisi (seulement pour les SELECT)
        if (selectNouveauStatut && selectNouveauStatut.tagName === 'SELECT') {
            selectNouveauStatut.addEventListener('change', function() {
                const nouveauStatut = this.value;
                
                if (nouveauStatut === 'annulee') {
                    if (contactSection) {
                        contactSection.classList.remove('d-none');
                        
                        // Rendre les champs obligatoires
                        if (checkboxContact) checkboxContact.setAttribute('required', 'required');
                        if (motifContact) motifContact.setAttribute('required', 'required');
                        modesContact.forEach(radio => radio.setAttribute('required', 'required'));
                        
                        alert('⚠️ Annulation : vous devez contacter le client et remplir le formulaire ci-dessous.');
                        contactSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                } else if (contactSection) {
                    contactSection.classList.add('d-none');
                    
                    // Retirer l'obligation des champs
                    if (checkboxContact) checkboxContact.removeAttribute('required');
                    if (motifContact) motifContact.removeAttribute('required');
                    modesContact.forEach(radio => radio.removeAttribute('required'));
                }
            });
        }
        
        // Validation du formulaire
        formChangeStatus.addEventListener('submit', function(e) {
            const nouveauStatut = selectNouveauStatut.value;
            
            if (nouveauStatut === 'annulee') {
                const errors = [];
                
                if (!checkboxContact.checked) {
                    errors.push('Vous devez confirmer avoir contacté l\'utilisateur');
                }
                
                const modeContactSelectionne = Array.from(modesContact).some(radio => radio.checked);
                if (!modeContactSelectionne) {
                    errors.push('Vous devez sélectionner un mode de contact (GSM ou Email)');
                }
                
                const motif = motifContact.value.trim();
                if (motif.length < 10) {
                    errors.push('Le motif doit contenir au moins 10 caractères');
                }
                
                if (errors.length > 0) {
                    e.preventDefault();
                    alert('⚠️ Formulaire incomplet :\n\n' + errors.join('\n'));
                    return false;
                }
            }
            
            const confirmed = confirm('Êtes-vous sûr de vouloir modifier le statut de cette commande ?');
            if (!confirmed) {
                e.preventDefault();
            }
            return confirmed;
        });
    }
});
