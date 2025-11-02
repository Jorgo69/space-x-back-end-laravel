#!/bin/bash

echo "🚀 SpaceX API - Déploiement Docker"
echo "=================================="
echo ""

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Installez Docker Desktop."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé."
    exit 1
fi

echo "✅ Docker détecté"
echo ""

# Créer le dossier docker s'il n'existe pas
if [ ! -d "docker" ]; then
    echo "📁 Création du dossier docker..."
    mkdir -p docker
fi

# Arrêter les conteneurs existants
echo "🛑 Arrêt des conteneurs existants..."
docker-compose down 2>/dev/null

# Construire et démarrer
echo "🔨 Construction de l'image Docker..."
docker-compose build --no-cache

echo "🚀 Démarrage du conteneur..."
docker-compose up -d

echo ""
echo "⏳ Attente du démarrage de l'application (30s)..."
sleep 30

# Vérifier que le conteneur tourne
if [ "$(docker-compose ps -q laravel)" ]; then
    echo ""
    echo "✅ Conteneur démarré avec succès !"
    echo ""
    echo "📡 API disponible sur : http://localhost:8000"
    echo ""
    echo "🧪 Testez avec :"
    echo "   curl http://localhost:8000/api/dashboard"
    echo ""
    echo "📊 Voir les logs :"
    echo "   docker-compose logs -f"
    echo ""
else
    echo ""
    echo "❌ Erreur : le conteneur n'a pas démarré"
    echo "📋 Consultez les logs :"
    echo "   docker-compose logs"
    exit 1
fi