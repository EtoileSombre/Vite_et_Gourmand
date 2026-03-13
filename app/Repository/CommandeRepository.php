<?php

namespace App\Repository;

use App\Core\Database;
use PDO;
class CommandeRepository implements CommandeRepositoryInterface
{
    private PDO $db;
    private string $table = 'commande';

    //Libellés des statuts de commande
    public const STATUTS = [
        'en_attente' => 'En attente',
        'acceptee' => 'Accepté',
        'en_preparation' => 'En préparation',
        'en_cours_livraison' => 'En cours de livraison',
        'livree' => 'Livré',
        'attente_retour_materiel' => 'Attente retour matériel',
        'terminee' => 'Terminé',
        'annulee' => 'Annulé',
        'refusee' => 'Refusé'
    ];
    
    //Libellés des statuts de commande pour l'affichage dans les listes
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAll(): array
    {
        $stmt = $this->db->prepare('
            SELECT c.*,
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            ORDER BY c.date_prestation DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE commande_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT c.*
            FROM commande c
            WHERE c.utilisateur_id = ?
            ORDER BY c.date_prestation DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function findByNumero(string $numeroCommande): ?array
    {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email,
                   u.telephone as utilisateur_telephone,
                   u.adresse_postale as utilisateur_adresse,
                   u.ville as utilisateur_ville,
                   u.code_postal as utilisateur_code_postal
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.numero_commande = ?
        ');
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    public function findWithDetails(string $numeroCommande): ?array
    {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email,
                   u.telephone as utilisateur_telephone
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.numero_commande = ?
        ');
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    public function findAllWithDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email,
                   u.telephone as utilisateur_telephone
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            ORDER BY c.date_prestation DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function create(array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => ":$field", $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return (int) $this->db->lastInsertId();
    }
    public function update(int $id, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = :$field";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE commande_id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    public function updateByNumero(string $numeroCommande, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = :$field";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE numero_commande = :numero_commande";
        
        $data['numero_commande'] = $numeroCommande;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    public function updateStatut(string $numeroCommande, string $statut): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET statut = ? 
            WHERE numero_commande = ?
        ");
        return $stmt->execute([$statut, $numeroCommande]);
    }
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE commande_id = ?");
        return $stmt->execute([$id]);
    }
    public function findByStatuts(array $statuts): array
    {
        $placeholders = str_repeat('?,', count($statuts) - 1) . '?';
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email,
                   u.telephone as utilisateur_telephone
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.statut IN ($placeholders)
            ORDER BY c.date_prestation DESC
        ");
        $stmt->execute($statuts);
        return $stmt->fetchAll();
    }
    public function findByDate(string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE DATE(c.date_prestation) = ?
            AND c.statut != 'annulee'
            ORDER BY c.heure_livraison ASC
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }
}
