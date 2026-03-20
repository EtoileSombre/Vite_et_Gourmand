<?php

namespace App\Repository;

use App\Core\Database;
use PDO;
class MaterielRepository implements MaterielRepositoryInterface
{
    private PDO $db;
    private string $table = 'materiel';
    private string $primaryKey = 'materiel_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAllAvailable(): array
    {
        $stmt = $this->db->query("
            SELECT materiel_id, nom, categorie, prix_caution, quantite_disponible, 
                   quantite_totale, description, photo
            FROM {$this->table}
            WHERE actif = 1 AND quantite_disponible > 0
            ORDER BY categorie, nom
        ");
        $materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
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
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE {$this->primaryKey} = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->table}
            ORDER BY categorie, nom
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByCategorie(string $categorie): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE categorie = ? AND actif = 1
            ORDER BY nom
        ");
        $stmt->execute([$categorie]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCategories(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT categorie
            FROM {$this->table}
            ORDER BY categorie
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTotalCautionByCommande(string $numeroCommande): float
    {
        $stmt = $this->db->prepare("
            SELECT SUM(total_caution) as total
            FROM commande_materiel
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Ajoute un matériel à une commande
    public function addMaterielToCommande(string $numeroCommande, int $materielId, int $quantite, float $prixCautionUnitaire, string $dateRetourPrevue): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO commande_materiel (numero_commande, materiel_id, quantite, prix_caution_unitaire, date_retour_prevue)
            VALUES (:numero_commande, :materiel_id, :quantite, :prix_caution_unitaire, :date_retour_prevue)
        ");
        return $stmt->execute([
            'numero_commande' => $numeroCommande,
            'materiel_id' => $materielId,
            'quantite' => $quantite,
            'prix_caution_unitaire' => $prixCautionUnitaire,
            'date_retour_prevue' => $dateRetourPrevue
        ]);
    }

    //Décrémente la quantité disponible d'un matériel
    public function decrementQuantite(int $materielId, int $quantite): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET quantite_disponible = quantite_disponible - :quantite
            WHERE {$this->primaryKey} = :materiel_id
        ");
        return $stmt->execute([
            'quantite' => $quantite,
            'materiel_id' => $materielId
        ]);
    }
}
