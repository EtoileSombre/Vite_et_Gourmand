<?php

namespace App\Services;

use App\MongoDB\MongoStats;
use App\Repository\CommandeMenuRepositoryInterface;
use App\Repository\CommandeRepositoryInterface;
use App\Repository\MenuRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Services\Exceptions\AdminException;

class AdminService extends AbstractService
{
    private const ROLES_STAFF = ['employé', 'administrateur'];
    private const DERNIERES_COMMANDES_LIMIT = 10;
    private const PASSWORD_MIN_LENGTH = 8;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CommandeRepositoryInterface $commandeRepository,
        private MenuRepositoryInterface $menuRepository,
        private CommandeMenuRepositoryInterface $commandeMenuRepository,
        private MongoStats $mongoStats,
    ) {
    }

    public function getDashboardStats(): array
    {
        $allUsers = $this->userRepository->findAllWithRole();
        $totalEmployes = count(array_filter(
            $allUsers,
            fn($u) => in_array($u->getRoleLibelle(), self::ROLES_STAFF, true)
        ));

        $totalCommandes = count($this->commandeRepository->findAll());
        $totalMenus = count($this->menuRepository->findAll());

        $dernieresCommandes = array_slice(
            $this->commandeRepository->findAll(),
            0,
            self::DERNIERES_COMMANDES_LIMIT
        );

        foreach ($dernieresCommandes as $cmd) {
            $cmd->setLignesMenus(
                $this->commandeMenuRepository->findByCommande($cmd->getNumeroCommande())
            );
            $cmd->setTotalPersonnes(
                $this->commandeMenuRepository->getTotalPersonnes($cmd->getNumeroCommande())
            );
            if (!empty($cmd->getLignesMenus())) {
                $cmd->setMenuNom($cmd->getLignesMenus()[0]->getMenuNom() ?? 'Menu');
            }
        }

        return [
            'totalEmployes'      => $totalEmployes,
            'totalCommandes'     => $totalCommandes,
            'totalMenus'         => $totalMenus,
            'dernieresCommandes' => $dernieresCommandes,
        ];
    }

    public function listStaffUsers(): array
    {
        return array_filter(
            $this->userRepository->findAllWithRole(),
            fn($u) => in_array($u->getRoleLibelle(), self::ROLES_STAFF, true)
        );
    }

    /**
     * @throws AdminException
     */
    public function createEmploye(array $data, int $adminId, string $adminEmail): int
    {
        $email = trim((string) ($data['email'] ?? ''));
        $prenom = trim((string) ($data['prenom'] ?? ''));
        $nom = trim((string) ($data['nom'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }
        if ($prenom === '') $errors[] = "Le prénom est obligatoire";
        if ($nom === '')    $errors[] = "Le nom est obligatoire";
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "Le mot de passe doit contenir au moins "
                . self::PASSWORD_MIN_LENGTH . " caractères";
        }
        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        if ($errors === [] && $this->userRepository->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé";
        }
        if (!empty($errors)) {
            throw new AdminException(implode('<br>', $errors));
        }

        $userId = $this->userRepository->createEmployeWithPassword($email, $prenom, $nom, $password);
        if (!$userId) {
            throw new AdminException("Erreur lors de la création du compte");
        }

        try {
            require_once __DIR__ . '/../config/mail.php';
            sendEmployeeAccountCreatedEmail($email, $prenom, $nom);
        } catch (\Exception $e) {
            error_log("Erreur envoi email création employé: " . $e->getMessage());
        }

        error_log("[ADMIN] Création employé : id={$userId}, email={$email}, par={$adminEmail}");

        $this->logActivity('create_employee', $adminId, [
            'employee_id'    => $userId,
            'employee_email' => $email,
            'created_by'     => $adminEmail,
        ]);

        return (int) $userId;
    }

    /**
     * @throws AdminException
     */
    public function deactivateUser(int $utilisateurId, int $adminId, string $adminEmail): void
    {
        if (!$this->userRepository->deactivate($utilisateurId)) {
            throw new AdminException("Erreur lors de la désactivation");
        }

        error_log("[ADMIN] Désactivation employé : id={$utilisateurId}, par={$adminEmail}");
        $this->logActivity('deactivate_employee', $adminId, [
            'employee_id'    => $utilisateurId,
            'deactivated_by' => $adminEmail,
        ]);
    }

    /**
     * @throws AdminException
     */
    public function activateUser(int $utilisateurId, int $adminId, string $adminEmail): void
    {
        if (!$this->userRepository->activate($utilisateurId)) {
            throw new AdminException("Erreur lors de l'activation");
        }

        error_log("[ADMIN] Réactivation employé : id={$utilisateurId}, par={$adminEmail}");
        $this->logActivity('activate_employee', $adminId, [
            'employee_id'  => $utilisateurId,
            'activated_by' => $adminEmail,
        ]);
    }

    public function listAllCommandes(): array
    {
        return $this->commandeRepository->findAll();
    }

    private function logActivity(string $action, int $userId, array $details): void
    {
        try {
            $this->mongoStats->logUserActivity($action, $userId, $details);
        } catch (\Exception $e) {
            error_log("Erreur log MongoDB ({$action}) : " . $e->getMessage());
        }
    }
}
