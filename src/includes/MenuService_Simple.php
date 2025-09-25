<?php
// Simple file-based menu service for testing without MongoDB
require_once __DIR__ . '/../config/database.php';

class MenuService {
    private $menusData;

    public function __construct() {
        $this->menusData = [
            [
                "_id" => "menu1",
                "name" => "Menu Mariage Prestige",
                "event_type" => "Mariage",
                "description" => "Un menu d'exception pour votre jour le plus important",
                "price" => 85.00,
                "serves" => "par personne",
                "active" => true,
                "created_at" => date('Y-m-d H:i:s'),
                "items" => [
                    [
                        "category" => "Entrée",
                        "name" => "Foie gras mi-cuit aux figues",
                        "description" => "Accompagné d'un confit d'oignons et pain toasté"
                    ],
                    [
                        "category" => "Plat principal",
                        "name" => "Filet de bœuf Wellington",
                        "description" => "Sauce aux champignons, légumes de saison"
                    ],
                    [
                        "category" => "Dessert",
                        "name" => "Pièce montée traditionnelle",
                        "description" => "Choux à la crème pâtissière et caramel"
                    ]
                ]
            ],
            [
                "_id" => "menu2",
                "name" => "Menu Anniversaire Festif",
                "event_type" => "Anniversaire",
                "description" => "Pour célébrer en grande pompe",
                "price" => 45.00,
                "serves" => "par personne",
                "active" => true,
                "created_at" => date('Y-m-d H:i:s'),
                "items" => [
                    [
                        "category" => "Entrée",
                        "name" => "Velouté de courge butternut",
                        "description" => "Crème de coco et graines torréfiées"
                    ],
                    [
                        "category" => "Plat principal",
                        "name" => "Suprême de volaille farci",
                        "description" => "Farce aux herbes fraîches, gratin dauphinois"
                    ],
                    [
                        "category" => "Dessert",
                        "name" => "Gâteau d'anniversaire personnalisé",
                        "description" => "Selon vos préférences"
                    ]
                ]
            ],
            [
                "_id" => "menu3",
                "name" => "Menu Événement d'Entreprise",
                "event_type" => "Événement d'entreprise",
                "description" => "Sophistiqué et professionnel",
                "price" => 55.00,
                "serves" => "par personne",
                "active" => true,
                "created_at" => date('Y-m-d H:i:s'),
                "items" => [
                    [
                        "category" => "Entrée",
                        "name" => "Carpaccio de saumon",
                        "description" => "Câpres, aneth et citron vert"
                    ],
                    [
                        "category" => "Plat principal",
                        "name" => "Médaillon de porc aux pruneaux",
                        "description" => "Sauce au Porto, écrasé de pommes de terre"
                    ],
                    [
                        "category" => "Dessert",
                        "name" => "Tiramisu revisité",
                        "description" => "Aux fruits rouges"
                    ]
                ]
            ],
            [
                "_id" => "menu4",
                "name" => "Menu Cocktail Apéritoire",
                "event_type" => "Cocktail",
                "description" => "Assortiment de petites bouchées raffinées",
                "price" => 25.00,
                "serves" => "par personne",
                "active" => true,
                "created_at" => date('Y-m-d H:i:s'),
                "items" => [
                    [
                        "category" => "Bouchées salées",
                        "name" => "Mini quiches lorraines",
                        "description" => "Pâte brisée, lardons et gruyère"
                    ],
                    [
                        "category" => "Bouchées salées",
                        "name" => "Canapés au saumon fumé",
                        "description" => "Pain de mie, crème fraîche et aneth"
                    ],
                    [
                        "category" => "Bouchées salées",
                        "name" => "Feuilletés au fromage",
                        "description" => "Pâte feuilletée et comté"
                    ],
                    [
                        "category" => "Bouchées sucrées",
                        "name" => "Mini éclairs",
                        "description" => "Chocolat et vanille"
                    ]
                ]
            ],
            [
                "_id" => "menu5",
                "name" => "Menu Familial Traditionnel",
                "event_type" => "Repas familial",
                "description" => "Saveurs authentiques pour se retrouver",
                "price" => 35.00,
                "serves" => "par personne",
                "active" => true,
                "created_at" => date('Y-m-d H:i:s'),
                "items" => [
                    [
                        "category" => "Entrée",
                        "name" => "Terrine de campagne",
                        "description" => "Cornichons et pain de campagne"
                    ],
                    [
                        "category" => "Plat principal",
                        "name" => "Blanquette de veau",
                        "description" => "Légumes anciens et riz pilaf"
                    ],
                    [
                        "category" => "Dessert",
                        "name" => "Tarte aux pommes grand-mère",
                        "description" => "Pâte brisée et compote maison"
                    ]
                ]
            ]
        ];
    }

    public function getAllMenus() {
        return array_filter($this->menusData, function($menu) {
            return $menu['active'];
        });
    }

    public function getMenusByEventType($eventType) {
        return array_filter($this->menusData, function($menu) use ($eventType) {
            return $menu['active'] && $menu['event_type'] === $eventType;
        });
    }

    public function getMenuById($id) {
        foreach ($this->menusData as $menu) {
            if ($menu['_id'] === $id && $menu['active']) {
                return $menu;
            }
        }
        return null;
    }

    public function getEventTypes() {
        $eventTypes = [];
        foreach ($this->menusData as $menu) {
            if ($menu['active'] && !in_array($menu['event_type'], $eventTypes)) {
                $eventTypes[] = $menu['event_type'];
            }
        }
        return array_unique($eventTypes);
    }
}
?>