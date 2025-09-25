// MongoDB initialization script for menu data
// This script initializes the menu collections for Vite & Gourmand

// Switch to vite_gourmand database
use vite_gourmand;

// Create menus collection with sample data
db.menus.insertMany([
  {
    _id: ObjectId(),
    name: "Menu Mariage Prestige",
    event_type: "Mariage",
    description: "Un menu d'exception pour votre jour le plus important",
    price: 85.00,
    serves: "par personne",
    active: true,
    created_at: new Date(),
    items: [
      {
        category: "Entrée",
        name: "Foie gras mi-cuit aux figues",
        description: "Accompagné d'un confit d'oignons et pain toasté"
      },
      {
        category: "Plat principal",
        name: "Filet de bœuf Wellington",
        description: "Sauce aux champignons, légumes de saison"
      },
      {
        category: "Dessert",
        name: "Pièce montée traditionnelle",
        description: "Choux à la crème pâtissière et caramel"
      }
    ]
  },
  {
    _id: ObjectId(),
    name: "Menu Anniversaire Festif",
    event_type: "Anniversaire",
    description: "Pour célébrer en grande pompe",
    price: 45.00,
    serves: "par personne",
    active: true,
    created_at: new Date(),
    items: [
      {
        category: "Entrée",
        name: "Velouté de courge butternut",
        description: "Crème de coco et graines torréfiées"
      },
      {
        category: "Plat principal",
        name: "Suprême de volaille farci",
        description: "Farce aux herbes fraîches, gratin dauphinois"
      },
      {
        category: "Dessert",
        name: "Gâteau d'anniversaire personnalisé",
        description: "Selon vos préférences"
      }
    ]
  },
  {
    _id: ObjectId(),
    name: "Menu Événement d'Entreprise",
    event_type: "Événement d'entreprise",
    description: "Sophistiqué et professionnel",
    price: 55.00,
    serves: "par personne",
    active: true,
    created_at: new Date(),
    items: [
      {
        category: "Entrée",
        name: "Carpaccio de saumon",
        description: "Câpres, aneth et citron vert"
      },
      {
        category: "Plat principal",
        name: "Médaillon de porc aux pruneaux",
        description: "Sauce au Porto, écrasé de pommes de terre"
      },
      {
        category: "Dessert",
        name: "Tiramisu revisité",
        description: "Aux fruits rouges"
      }
    ]
  },
  {
    _id: ObjectId(),
    name: "Menu Cocktail Apéritoire",
    event_type: "Cocktail",
    description: "Assortiment de petites bouchées raffinées",
    price: 25.00,
    serves: "par personne",
    active: true,
    created_at: new Date(),
    items: [
      {
        category: "Bouchées salées",
        name: "Mini quiches lorraines",
        description: "Pâte brisée, lardons et gruyère"
      },
      {
        category: "Bouchées salées",
        name: "Canapés au saumon fumé",
        description: "Pain de mie, crème fraîche et aneth"
      },
      {
        category: "Bouchées salées",
        name: "Feuilletés au fromage",
        description: "Pâte feuilletée et comté"
      },
      {
        category: "Bouchées sucrées",
        name: "Mini éclairs",
        description: "Chocolat et vanille"
      }
    ]
  },
  {
    _id: ObjectId(),
    name: "Menu Familial Traditionnel",
    event_type: "Repas familial",
    description: "Saveurs authentiques pour se retrouver",
    price: 35.00,
    serves: "par personne",
    active: true,
    created_at: new Date(),
    items: [
      {
        category: "Entrée",
        name: "Terrine de campagne",
        description: "Cornichons et pain de campagne"
      },
      {
        category: "Plat principal",
        name: "Blanquette de veau",
        description: "Légumes anciens et riz pilaf"
      },
      {
        category: "Dessert",
        name: "Tarte aux pommes grand-mère",
        description: "Pâte brisée et compote maison"
      }
    ]
  }
]);

// Create indexes for better performance
db.menus.createIndex({ event_type: 1 });
db.menus.createIndex({ active: 1 });
db.menus.createIndex({ price: 1 });