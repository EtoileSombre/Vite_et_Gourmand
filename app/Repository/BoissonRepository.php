<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Boisson;
use PDO;
class BoissonRepository implements BoissonRepositoryInterface
{
    private PDO $db;
    private string $table = 'boisson';
    private string $primaryKey = 'boisson_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAllAvailable(): array
    {
        $stmt = $this->db->query("
            SELECT boisson_id, nom, type_boisson, prix_unitaire, contenance, description, photo
            FROM {$this->table}
            WHERE disponible = 1
            ORDER BY type_boisson, nom
        ");
        $boissons = array_map(fn($row) => Boisson::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // Grouper par type
        $grouped = [];
        foreach ($boissons as $boisson) {
            $type = $boisson->getTypeBoisson();
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $boisson;
        }
        
        return $grouped;
    }
    public function findById(int $id): ?Boisson
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE {$this->primaryKey} = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? Boisson::fromArray($result) : null;
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
        return array_map(fn($row) => Boisson::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM {$this->table}
            ORDER BY type_boisson, nom
        ");
        return array_map(fn($row) => Boisson::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function findByType(string $type): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE type_boisson = ? AND disponible = 1
            ORDER BY nom
        ");
        $stmt->execute([$type]);
        return array_map(fn($row) => Boisson::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function getTypes(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT type_boisson
            FROM {$this->table}
            ORDER BY type_boisson
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Calcule le total des boissons pour une commande
    public function getTotalByCommande(string $numeroCommande): float
    {
        $stmt = $this->db->prepare("
            SELECT SUM(total_ligne) as total
            FROM commande_boisson
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    //Ajoute une boisson à une commande
    public function addBoissonToCommande(string $numeroCommande, int $boissonId, int $quantite, float $prixUnitaire): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO commande_boisson (numero_commande, boisson_id, quantite, prix_unitaire)
            VALUES (:numero_commande, :boisson_id, :quantite, :prix_unitaire)
        ");
        return $stmt->execute([
            'numero_commande' => $numeroCommande,
            'boisson_id' => $boissonId,
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire
        ]);
    }
}
