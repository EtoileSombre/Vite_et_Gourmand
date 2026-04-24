<?php

namespace App\Services;

use App\Models\Menu;
use App\Repository\MenuRepositoryInterface;
use App\Repository\PlatRepositoryInterface;
use App\Services\Exceptions\MenuException;

class MenuService extends AbstractService
{
    private const DEFAULT_STOCK = 100;

    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private PlatRepositoryInterface $platRepository,
    ) {
    }

    /** @return Menu[] */
    public function listAllWithPlats(): array
    {
        $menus = $this->menuRepository->findAll();
        foreach ($menus as $menu) {
            $menu->setPlats($this->menuRepository->getPlatsForMenu($menu->getMenuId()));
        }
        return $menus;
    }

    /** @return \App\Models\Plat[] */
    public function listPlatsWithAllergenes(): array
    {
        $plats = $this->platRepository->findAllPlats();
        $allAllergenes = $this->platRepository->getAllAllergenes();

        foreach ($plats as $plat) {
            $platAllergeneIds = $this->platRepository->getAllergenesForPlat($plat->getPlatId());
            $libelles = [];
            foreach ($platAllergeneIds as $allergeneId) {
                $match = array_filter($allAllergenes, fn($a) => $a['allergene_id'] == $allergeneId);
                if (!empty($match)) {
                    $libelles[] = reset($match)['libelle'];
                }
            }
            $plat->setAllergenes($libelles);
        }

        return $plats;
    }

    /**
     * @throws MenuException
     */
    public function loadForEdit(int $menuId): array
    {
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new MenuException("Menu introuvable");
        }

        return [
            'menu'    => $menu,
            'plats'   => $this->listPlatsWithAllergenes(),
            'platIds' => $this->menuRepository->getPlatIdsForMenu($menuId),
        ];
    }

    /**
     * @throws MenuException
     */
    public function createMenu(array $data, array $platsIds = []): int
    {
        $clean = $this->validateMenuData($data);

        $payload = [
            'titre'                    => $clean['titre'],
            'description'              => $clean['description'],
            'prix_par_personne'        => $clean['prix_par_personne'],
            'nombre_personne_minimum'  => $clean['nombre_personnes_min'],
            'quantite_restante'        => $data['quantite_restante'] ?? self::DEFAULT_STOCK,
        ];

        $menuId = $this->menuRepository->create($payload);
        if (!$menuId) {
            throw new MenuException("Erreur lors de la création du menu");
        }

        if (!empty($platsIds)) {
            $this->menuRepository->syncPlats($menuId, $platsIds);
        }

        return (int) $menuId;
    }

    /**
     * @throws MenuException
     */
    public function updateMenu(int $menuId, array $data, array $platsIds = []): Menu
    {
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new MenuException("Menu introuvable");
        }

        $clean = $this->validateMenuData($data);

        $payload = [
            'titre'                    => $clean['titre'],
            'description'              => $clean['description'],
            'prix_par_personne'        => $clean['prix_par_personne'],
            'nombre_personne_minimum'  => $clean['nombre_personnes_min'],
            'quantite_restante'        => $data['quantite_restante'] ?? 0,
        ];

        if (!$this->menuRepository->update($menuId, $payload)) {
            throw new MenuException("Erreur lors de la mise à jour du menu");
        }

        $this->menuRepository->syncPlats($menuId, $platsIds);

        return $menu;
    }

    /**
     * @throws MenuException
     */
    public function deleteMenu(int $menuId): Menu
    {
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new MenuException("Menu introuvable");
        }

        if (!$this->menuRepository->delete($menuId)) {
            throw new MenuException("Erreur lors de la suppression du menu");
        }

        return $menu;
    }

    /**
     * @throws MenuException
     */
    public function reactivate(int $menuId): void
    {
        if (!$this->menuRepository->update($menuId, ['quantite_restante' => self::DEFAULT_STOCK])) {
            throw new MenuException("Erreur lors de la réactivation du menu");
        }
    }

    /**
     * @throws MenuException
     */
    private function validateMenuData(array $data): array
    {
        $titre       = trim((string) ($data['titre'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $prix        = trim((string) ($data['prix_par_personne'] ?? ''));
        $minPers     = trim((string) ($data['nombre_personne_minimum'] ?? ''));

        $errors = [];
        if ($titre === '') {
            $errors[] = "Le titre est obligatoire";
        }
        if ($prix === '' || !is_numeric($prix) || (float) $prix <= 0) {
            $errors[] = "Le prix par personne doit être un nombre positif";
        }
        if ($minPers === '' || !is_numeric($minPers) || (int) $minPers <= 0) {
            $errors[] = "Le nombre de personnes minimum doit être un nombre positif";
        }

        if (!empty($errors)) {
            throw new MenuException(implode('<br>', $errors));
        }

        return [
            'titre'                => $titre,
            'description'          => $description,
            'prix_par_personne'    => (float) $prix,
            'nombre_personnes_min' => (int) $minPers,
        ];
    }
}
