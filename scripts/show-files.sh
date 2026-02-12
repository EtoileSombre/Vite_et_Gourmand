#!/bin/bash

# Script pour afficher rapidement où se trouvent les fichiers importants

echo ""
echo "CHEMINS DES FICHIERS PRINCIPAUX"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Routes
echo "ROUTES (définir les URLs)"
echo "   → app/routes.php"
echo ""

# Contrôleurs
echo "🎮 CONTRÔLEURS (logique métier)"
echo "   Admin:"
for file in /opt/Vite_et_Gourmand_dev/app/Controllers/Admin/*.php; do
    echo "     → app/Controllers/Admin/$(basename $file)"
done
echo ""
echo "   Public:"
for file in /opt/Vite_et_Gourmand_dev/app/Controllers/Public/*.php; do
    echo "     → app/Controllers/Public/$(basename $file)"
done
echo ""
echo "   Utilisateur:"
for file in /opt/Vite_et_Gourmand_dev/app/Controllers/Utilisateur/*.php; do
    echo "     → app/Controllers/Utilisateur/$(basename $file)"
done
echo ""

# Modèles
echo "🗄️  MODÈLES (base de données)"
for file in /opt/Vite_et_Gourmand_dev/app/Models/*.php; do
    echo "   → app/Models/$(basename $file)"
done
echo ""

# Vues principales
echo "VUES PRINCIPALES (interface)"
echo "   Page d'accueil     → app/Views/public/home/index.php"
echo "   Contact            → app/Views/public/contact/index.php"
echo "   Menus             → app/Views/public/menus/"
echo "   Dashboard Admin    → app/Views/admin/dashboard.php"
echo "   Dashboard Employé  → app/Views/employe/dashboard.php"
echo ""

# Configuration
echo "⚙️  CONFIGURATION"
echo "   Base de données → app/config/db.php"
echo "   Email           → app/config/mail.php"
echo ""

# Assets
echo "🎨 DESIGN & ASSETS"
echo "   CSS    → app/public/assets/css/"
echo "   JS     → app/public/assets/js/"
echo "   Images → app/public/assets/img/"
echo ""

# Base de données
echo "💾 BASE DE DONNÉES"
echo "   Structure → app/sql/structure.sql"
echo "   Données   → app/sql/donnees.sql"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "DANS VS CODE:"
echo "   • Appuyez sur Ctrl+P et tapez le nom du fichier"
echo "   • Utilisez Ctrl+Shift+F pour rechercher dans tous les fichiers"
echo ""
echo "📖 DOCUMENTATION COMPLÈTE:"
echo "   → /opt/Vite_et_Gourmand_dev/OU_CODER.md"
echo "   → /opt/Vite_et_Gourmand_dev/AIDE_MEMOIRE.txt"
echo ""
