<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Materiel
 * Gestion du matériel disponible en location avec cautions
 */
class Materiel extends Model
{
    protected $table = 'materiel';
    protected $primaryKey = 'materiel_id';

    /**
     * Récupère tout le matériel disponible groupé par catégorie
     * 
     * @return array Matériel groupé par catégorie
     */
    public function findAllAvailable(): array
    {
        $stmt = $this->db->query("
            SELECT materiel_id, nom, categorie, prix_caution, quantite_disponible, 
                   quantite_totale, description, photo
            FROM {$this->table}
            WHERE actif = 1 AND quantite_disponible > 0
            ORDER BY categorie, nom
        ");
        $materiels = $stmt->fetchAll();
        
        // Grouper par catégorie
        $grouped = [];
        foreach ($materiels as $materiel) {
            $categorie = $materiel['categorie'];
            if (!isset($grouped[$categorie])) {
                $grouped[$categorie] = [];
            }
            $grouped[$categorie][] = $materiel;
        }
        
        return $grouped;
    }

    /**
     * Récupère un matériel par son ID
     * 
     * @return array|null|false
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE {$this->primaryKey} = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupère plusieurs matériels par leurs IDs
     * 
     * @return array
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE {$this->primaryKey} IN ($placeholders)
        ");
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    /**
     * Récupère tout le matériel (incluant inactif) pour l'admin
     * 
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->table}
            ORDER BY categorie, nom
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère le matériel d'une catégorie spécifique
     * 
     * @return array
     */
    public function findByCategorie(string $categorie): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE categorie = ? AND actif = 1
            ORDER BY nom
        ");
        $stmt->execute([$categorie]);
        return $stmt->fetchAll();
    }

    /**
     * Crée un nouveau matériel
     * 
     * @return bool|int
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (nom, description, categorie, quantite_totale, quantite_disponible, 
             prix_caution, valeur_unitaire, photo, actif)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $quantite = $data['quantite_totale'] ?? 0;
        
        $result = $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['categorie'] ?? 'Autre',
            $quantite,
            $data['quantite_disponible'] ?? $quantite,
            $data['prix_caution'] ?? 0,
            $data['valeur_unitaire'] ?? null,
            $data['photo'] ?? null,
            $data['actif'] ?? 1
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Met à jour un matériel
     * 
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET nom = ?, description = ?, categorie = ?, quantite_totale = ?,
                quantite_disponible = ?, prix_caution = ?, valeur_unitaire = ?,
                photo = ?, actif = ?
            WHERE {$this->primaryKey} = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['categorie'] ?? 'Autre',
            $data['quantite_totale'],
            $data['quantite_disponible'],
            $data['prix_caution'] ?? 0,
            $data['valeur_unitaire'] ?? null,
            $data['photo'] ?? null,
            $data['actif'] ?? 1,
            $id
        ]);
    }

    /**
     * Supprime un matériel
     * 
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Met à jour la quantité disponible
     * 
     * @return bool
     */
    public function updateQuantiteDisponible(int $id, int $quantite): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET quantite_disponible = ?
            WHERE {$this->primaryKey} = ?
        ");
        return $stmt->execute([$quantite, $id]);
    }

    /**
     * Diminue la quantité disponible (lors d'une location)
     * 
     * @return bool
     */
    public function decreaseQuantite(int $id, int $quantite): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET quantite_disponible = quantite_disponible - ?
            WHERE {$this->primaryKey} = ? AND quantite_disponible >= ?
        ");
        return $stmt->execute([$quantite, $id, $quantite]);
    }

    /**
     * Augmente la quantité disponible (lors d'un retour)
     * 
     * @return bool
     */
    public function increaseQuantite(int $id, int $quantite): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET quantite_disponible = quantite_disponible + ?
            WHERE {$this->primaryKey} = ?
        ");
        return $stmt->execute([$quantite, $id]);
    }

    /**
     * Vérifie si une quantité est disponible
     * 
     * @return bool
     */
    public function isQuantiteDisponible(int $id, int $quantite): bool
    {
        $stmt = $this->db->prepare("
            SELECT quantite_disponible >= ? as disponible
            FROM {$this->table}
            WHERE {$this->primaryKey} = ?
        ");
        $stmt->execute([$quantite, $id]);
        $result = $stmt->fetch();
        return (bool)($result['disponible'] ?? false);
    }

    /**
     * Ajoute du matériel à une commande
     * 
     * @return bool
     */
    public function addToCommande(string $numeroCommande, int $materielId, int $quantite, float $cautionUnitaire, string $dateRetourPrevue): bool
    {
        // Vérifier disponibilité
        if (!$this->isQuantiteDisponible($materielId, $quantite)) {
            return false;
        }

        // Ajouter à la commande
        $stmt = $this->db->prepare("
            INSERT INTO commande_materiel 
            (numero_commande, materiel_id, quantite, prix_caution_unitaire, date_retour_prevue)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([
            $numeroCommande,
            $materielId,
            $quantite,
            $cautionUnitaire,
            $dateRetourPrevue
        ]);

        // Diminuer la quantité disponible
        if ($success) {
            $this->decreaseQuantite($materielId, $quantite);
        }

        return $success;
    }

    /**
     * Récupère le matériel d'une commande
     * 
     * @return array
     */
    public function getByCommande(string $numeroCommande): array
    {
        $stmt = $this->db->prepare("
            SELECT cm.*, m.nom, m.categorie, m.description
            FROM commande_materiel cm
            JOIN {$this->table} m ON cm.materiel_id = m.materiel_id
            WHERE cm.numero_commande = ?
            ORDER BY m.categorie, m.nom
        ");
        $stmt->execute([$numeroCommande]);
        return $stmt->fetchAll();
    }

    /**
     * Calcule le total de caution pour une commande
     * 
     * @return float
     */
    public function getTotalCautionByCommande(string $numeroCommande): float
    {
        $stmt = $this->db->prepare("
            SELECT SUM(total_caution) as total
            FROM commande_materiel
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    /**
     * Enregistre le retour de matériel
     * 
     * @return bool
     */
    public function enregistrerRetour(int $commandeMaterielId, string $etatRetour, float $montantRetenu = 0, ?string $notes = null): bool
    {
        // Récupérer les infos
        $stmt = $this->db->prepare("
            SELECT materiel_id, quantite
            FROM commande_materiel
            WHERE commande_materiel_id = ?
        ");
        $stmt->execute([$commandeMaterielId]);
        $info = $stmt->fetch();

        if (!$info) {
            return false;
        }
        $stmt = $this->db->prepare("
            UPDATE commande_materiel
            SET date_retour_effective = NOW(),
                etat_retour = ?,
                caution_restituee = ?,
                montant_retenu = ?,
                notes = ?
            WHERE commande_materiel_id = ?
        ");
        
        $cautionRestituee = ($montantRetenu == 0) ? 1 : 0;
        
        $success = $stmt->execute([
            $etatRetour,
            $cautionRestituee,
            $montantRetenu,
            $notes,
            $commandeMaterielId
        ]);

        // Réaugmenter la quantité disponible (sauf si perdu)
        if ($success && $etatRetour !== 'perdu') {
            $this->increaseQuantite($info['materiel_id'], $info['quantite']);
        }

        return $success;
    }

    /**
     * Récupère les catégories disponibles
     * 
     * @return array
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT categorie
            FROM {$this->table}
            ORDER BY categorie
        ");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Récupère le matériel en attente de retour
     * 
     * @return array
     */
    public function getEnAttenteRetour(): array
    {
        $stmt = $this->db->query("
            SELECT cm.*, m.nom, m.categorie, c.numero_commande, 
                   u.prenom, u.nom as utilisateur_nom
            FROM commande_materiel cm
            JOIN {$this->table} m ON cm.materiel_id = m.materiel_id
            JOIN commande c ON cm.numero_commande = c.numero_commande
            JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE cm.etat_retour = 'non_retourne'
            ORDER BY cm.date_retour_prevue ASC
        ");
        return $stmt->fetchAll();
    }
}
