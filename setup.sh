#!/bin/bash

# Vite & Gourmand Setup Script

echo "=== Configuration de Vite & Gourmand ==="

# Check if Docker and Docker Compose are installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

echo "✅ Docker et Docker Compose sont installés"

# Create necessary directories
echo "📁 Création des répertoires..."
mkdir -p src/uploads
chmod 755 src/uploads

# Set up environment file
if [ ! -f .env ]; then
    echo "🔧 Création du fichier .env..."
    cat > .env << EOF
# Database Configuration
MYSQL_HOST=mysql
MYSQL_DATABASE=vite_gourmand
MYSQL_USER=root
MYSQL_PASSWORD=password

# MongoDB Configuration  
MONGODB_HOST=mongodb
MONGODB_PORT=27017
MONGODB_DATABASE=vite_gourmand

# Application Configuration
APP_NAME=Vite & Gourmand
APP_ENV=production
EOF
fi

# Build and start containers
echo "🐳 Construction et démarrage des conteneurs Docker..."
docker-compose down --remove-orphans
docker-compose build
docker-compose up -d

# Wait for services to be ready
echo "⏳ Attente du démarrage des services..."
sleep 30

# Check if services are running
echo "🔍 Vérification du statut des services..."
docker-compose ps

echo ""
echo "🎉 Installation terminée !"
echo ""
echo "📋 Informations d'accès :"
echo "   • Application web : http://localhost:8080"
echo "   • phpMyAdmin : http://localhost:8081"
echo "   • Base MySQL : localhost:3306"
echo "   • Base MongoDB : localhost:27017"
echo ""
echo "🔧 Commandes utiles :"
echo "   • Arrêter : docker-compose down"
echo "   • Redémarrer : docker-compose restart"
echo "   • Voir les logs : docker-compose logs -f"
echo "   • Accéder au PHP : docker-compose exec php bash"
echo ""
echo "📖 Consultez le README.md pour plus d'informations"