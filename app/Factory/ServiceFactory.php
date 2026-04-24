<?php

namespace App\Factory;

use App\Services\AdminService;
use App\Services\AuthService;
use App\Services\CommandeService;
use App\Services\ContactService;
use App\Services\MenuService;
use App\MongoDB\MongoStats;

/**
 * Singleton + cache d'instances, miroir de RepositoryFactory.
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

    /** Utile pour les tests. */
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

    public function createAuthService(): AuthService
    {
        if (!isset($this->services['auth'])) {
            $repoFactory = RepositoryFactory::getInstance();
            $this->services['auth'] = new AuthService(
                $repoFactory->createUserRepository(),
                $repoFactory->createPasswordResetRepository(),
            );
        }
        return $this->services['auth'];
    }

    public function createMenuService(): MenuService
    {
        if (!isset($this->services['menu'])) {
            $repoFactory = RepositoryFactory::getInstance();
            $this->services['menu'] = new MenuService(
                $repoFactory->createMenuRepository(),
                $repoFactory->createPlatRepository(),
            );
        }
        return $this->services['menu'];
    }

    public function createAdminService(): AdminService
    {
        if (!isset($this->services['admin'])) {
            $repoFactory = RepositoryFactory::getInstance();
            $this->services['admin'] = new AdminService(
                $repoFactory->createUserRepository(),
                $repoFactory->createCommandeRepository(),
                $repoFactory->createMenuRepository(),
                $repoFactory->createCommandeMenuRepository(),
                new MongoStats(),
            );
        }
        return $this->services['admin'];
    }

    public function createContactService(): ContactService
    {
        if (!isset($this->services['contact'])) {
            $repoFactory = RepositoryFactory::getInstance();
            $this->services['contact'] = new ContactService(
                $repoFactory->createContactRepository(),
            );
        }
        return $this->services['contact'];
    }
}
