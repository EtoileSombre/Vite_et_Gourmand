<?php

namespace App\Factory;

use App\Services\CommandeService;
use App\MongoDB\MongoStats;

/**
 * Factory pour la création des Services métier.
 *
 * Suit le même pattern que RepositoryFactory : singleton + cache
 * des instances. Les services sont construits avec leurs dépendances
 * (repositories, MongoStats...) via RepositoryFactory.
 */
class ServiceFactory
{
    private static ?ServiceFactory $instance = null;

    /** @var array<string, object> */
    private array $services = [];

    private function __construct()
    {
    }

    public static function getInstance(): ServiceFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Réinitialise l'instance (utile pour les tests).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    public function createCommandeService(): CommandeService
    {
        if (!isset($this->services['commande'])) {
            $repoFactory = RepositoryFactory::getInstance();
            $this->services['commande'] = new CommandeService(
                $repoFactory->createCommandeRepository(),
                $repoFactory->createMenuRepository(),
                $repoFactory->createCommandeMenuRepository(),
                $repoFactory->createBoissonRepository(),
                $repoFactory->createMaterielRepository(),
                $repoFactory->createSuiviCommandeRepository(),
                new MongoStats()
            );
        }
        return $this->services['commande'];
    }
}
