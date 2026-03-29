<?php

namespace App\Core;

/**
 * Classe Model de base
 * Définit la structure de l'entité (table, clé primaire)
 * L'accès aux données se fait exclusivement via les Repositories
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getDb()
    {
        return $this->db;
    }
}
