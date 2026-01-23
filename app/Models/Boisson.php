<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Boisson
 * Gestion des boissons disponibles à la commande
 */
class Boisson extends Model
{
    protected $table = 'boisson';
    protected $primaryKey = 'boisson_id';

    /**
     * Récupère toutes les boissons disponibles groupées par type
     * 
     * @return array Boissons groupées par type
     */
    public function findAllAvailable(): array
    {
        $stmt = $this->db->query("
            SELECT boisson_id, nom, type_boisson, prix_unitaire, contenance, description, photo
            FROM {$this->table}
            WHERE disponible = 1
            ORDER BY type_boisson, nom
        ");
        $boissons = $stmt->fetchAll();
        
        // Grouper par type
        $grouped = [];
        foreach ($boissons as $boisson) {
            $type = $boisson['type_boisson'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $boisson;
        }
        
        return $grouped;
    }

    /**
     * Récupère une boisson par son ID
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
     * Récupère plusieurs boissons par leurs IDs
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
     * Récupère toutes les boissons (incluant indisponibles) pour l'admin
     * 
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->table}
            ORDER BY type_boisson, nom
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère les boissons d'un type spécifique
     * 
     * @return array
     */
    public function findByType(string $type): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE type_boisson = ? AND disponible = 1
            ORDER BY nom
        ");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    /**
     * Crée une nouvelle boisson
     * 
     * @return bool|int
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (nom, description, type_boisson, prix_unitaire, contenance, disponible, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['type_boisson'] ?? 'Autre',
            $data['prix_unitaire'],
            $data['contenance'] ?? null,
            $data['disponible'] ?? 1,
            $data['photo'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Met à jour une boisson
     * 
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET nom = ?, description = ?, type_boisson = ?, prix_unitaire = ?,
                contenance = ?, disponible = ?, photo = ?
            WHERE {$this->primaryKey} = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['type_boisson'] ?? 'Autre',
            $data['prix_unitaire'],
            $data['contenance'] ?? null,
            $data['disponible'] ?? 1,
            $data['photo'] ?? null,
            $id
        ]);
    }

    /**
     * Supprime une boisson
     * 
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Change la disponibilité d'une boisson
     * 
     * @return bool
     */
    public function setDisponibilite(int $id, bool $disponible): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET disponible = ?
            WHERE {$this->primaryKey} = ?
        ");
        return $stmt->execute([$disponible ? 1 : 0, $id]);
    }

    /**
     * Récupère les types de boissons disponibles
     * 
     * @return array
     */
    public function getTypes(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT type_boisson
            FROM {$this->table}
            ORDER BY type_boisson
        ");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Ajoute une boisson à une commande
     * 
     * @return bool
     */
    public function addToCommande(string $numeroCommande, int $boissonId, int $quantite, float $prixUnitaire): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO commande_boisson 
            (numero_commande, boisson_id, quantite, prix_unitaire)
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $numeroCommande,
            $boissonId,
            $quantite,
            $prixUnitaire
        ]);
    }

    /**
     * Récupère les boissons d'une commande
     * 
     * @return array
     */
    public function getByCommande(string $numeroCommande): array
    {
        $stmt = $this->db->prepare("
            SELECT cb.*, b.nom, b.type_boisson, b.contenance
            FROM commande_boisson cb
            JOIN {$this->table} b ON cb.boisson_id = b.boisson_id
            WHERE cb.numero_commande = ?
            ORDER BY b.type_boisson, b.nom
        ");
        $stmt->execute([$numeroCommande]);
        return $stmt->fetchAll();
    }

    /**
     * Calcule le total des boissons pour une commande
     * 
     * @return float
     */
    public function getTotalByCommande(string $numeroCommande): float
    {
        $stmt = $this->db->prepare("
            SELECT SUM(total_ligne) as total
            FROM commande_boisson
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
}
