<?php

namespace App\Factory;

use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use App\Repository\CommandeMenuRepository;
use App\Repository\SuiviCommandeRepository;
use App\Repository\BoissonRepository;
use App\Repository\MaterielRepository;
use App\Repository\ContactRepository;
use App\Repository\PlatRepository;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepositoryInterface;
use App\Repository\CommandeRepositoryInterface;
use App\Repository\MenuRepositoryInterface;
use App\Repository\AvisRepositoryInterface;
use App\Repository\CommandeMenuRepositoryInterface;
use App\Repository\SuiviCommandeRepositoryInterface;
use App\Repository\BoissonRepositoryInterface;
use App\Repository\MaterielRepositoryInterface;
use App\Repository\ContactRepositoryInterface;
use App\Repository\PlatRepositoryInterface;
use App\Repository\PasswordResetRepositoryInterface;

/**
 * Factory pour la création des Repositories
 */
class RepositoryFactory
{
    private static ?RepositoryFactory $instance = null;
    private array $repositories = [];
    private function __construct()
    {
    }

    /**
     * Récupère l'instance unique de la factory
     * 
     * @return RepositoryFactory
     */
    public static function getInstance(): RepositoryFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Crée ou récupère un UserRepository
     * 
     * @return UserRepositoryInterface
     */
    public function createUserRepository(): UserRepositoryInterface
    {
        if (!isset($this->repositories['user'])) {
            $this->repositories['user'] = new UserRepository();
        }
        return $this->repositories['user'];
    }

    /**
     * Crée ou récupère un CommandeRepository
     * 
     * @return CommandeRepositoryInterface
     */
    public function createCommandeRepository(): CommandeRepositoryInterface
    {
        if (!isset($this->repositories['commande'])) {
            $this->repositories['commande'] = new CommandeRepository();
        }
        return $this->repositories['commande'];
    }

    /**
     * Crée ou récupère un MenuRepository
     * 
     * @return MenuRepositoryInterface
     */
    public function createMenuRepository(): MenuRepositoryInterface
    {
        if (!isset($this->repositories['menu'])) {
            $this->repositories['menu'] = new MenuRepository();
        }
        return $this->repositories['menu'];
    }

    /**
     * Crée ou récupère un AvisRepository
     * 
     * @return AvisRepositoryInterface
     */
    public function createAvisRepository(): AvisRepositoryInterface
    {
        if (!isset($this->repositories['avis'])) {
            $this->repositories['avis'] = new AvisRepository();
        }
        return $this->repositories['avis'];
    }

    /**
     * Crée ou récupère un CommandeMenuRepository
     * 
     * @return CommandeMenuRepositoryInterface
     */
    public function createCommandeMenuRepository(): CommandeMenuRepositoryInterface
    {
        if (!isset($this->repositories['commandemenu'])) {
            $this->repositories['commandemenu'] = new CommandeMenuRepository();
        }
        return $this->repositories['commandemenu'];
    }

    /**
     * Crée ou récupère un SuiviCommandeRepository
     * 
     * @return SuiviCommandeRepositoryInterface
     */
    public function createSuiviCommandeRepository(): SuiviCommandeRepositoryInterface
    {
        if (!isset($this->repositories['suivicommande'])) {
            $this->repositories['suivicommande'] = new SuiviCommandeRepository();
        }
        return $this->repositories['suivicommande'];
    }

    /**
     * Crée ou récupère un BoissonRepository
     * 
     * @return BoissonRepositoryInterface
     */
    public function createBoissonRepository(): BoissonRepositoryInterface
    {
        if (!isset($this->repositories['boisson'])) {
            $this->repositories['boisson'] = new BoissonRepository();
        }
        return $this->repositories['boisson'];
    }

    /**
     * Crée ou récupère un MaterielRepository
     * 
     * @return MaterielRepositoryInterface
     */
    public function createMaterielRepository(): MaterielRepositoryInterface
    {
        if (!isset($this->repositories['materiel'])) {
            $this->repositories['materiel'] = new MaterielRepository();
        }
        return $this->repositories['materiel'];
    }

    /**
     * Crée ou récupère un ContactRepository
     * 
     * @return ContactRepositoryInterface
     */
    public function createContactRepository(): ContactRepositoryInterface
    {
        if (!isset($this->repositories['contact'])) {
            $this->repositories['contact'] = new ContactRepository();
        }
        return $this->repositories['contact'];
    }

    /**
     * Crée ou récupère un PlatRepository
     * 
     * @return PlatRepositoryInterface
     */
    public function createPlatRepository(): PlatRepositoryInterface
    {
        if (!isset($this->repositories['plat'])) {
            $this->repositories['plat'] = new PlatRepository();
        }
        return $this->repositories['plat'];
    }

    /**
     * Crée ou récupère un PasswordResetRepository
     * 
     * @return PasswordResetRepositoryInterface
     */
    public function createPasswordResetRepository(): PasswordResetRepositoryInterface
    {
        if (!isset($this->repositories['passwordreset'])) {
            $this->repositories['passwordreset'] = new PasswordResetRepository();
        }
        return $this->repositories['passwordreset'];
    }

    /**
     * Crée un repository par nom (méthode générique)
     * 
     * @return mixed
     */
    public function create(string $name)
    {
        return match(strtolower($name)) {
            'user', 'utilisateur' => $this->createUserRepository(),
            'commande', 'order' => $this->createCommandeRepository(),
            'menu' => $this->createMenuRepository(),
            'avis', 'review' => $this->createAvisRepository(),
            'commandemenu', 'commande_menu' => $this->createCommandeMenuRepository(),
            'suivicommande', 'suivi_commande', 'suivi' => $this->createSuiviCommandeRepository(),
            'boisson', 'drink' => $this->createBoissonRepository(),
            'materiel', 'material' => $this->createMaterielRepository(),
            'contact' => $this->createContactRepository(),
            'plat', 'dish' => $this->createPlatRepository(),
            'passwordreset', 'password_reset' => $this->createPasswordResetRepository(),
            default => throw new \InvalidArgumentException("Repository '$name' non supporté")
        };
    }

    /**
     * Réinitialise le cache des repositories
     * Utile pour les tests
     * 
     * @return void
     */
    public function reset(): void
    {
        $this->repositories = [];
    }
    private function __clone()
    {
    }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
