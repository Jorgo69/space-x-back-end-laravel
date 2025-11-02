#!/bin/bash

echo "🚀 Démarrage de l'application SpaceX API..."

# Attendre que le système de fichiers soit prêt
sleep 2

# Vérifier si le fichier .env existe, sinon le créer
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
fi

# Générer la clé d'application si elle n'existe pas
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force
fi

# Vérifier et créer la base de données SQLite si elle n'existe pas
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "💾 Création de la base de données SQLite..."
    touch /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# Exécuter les migrations
echo "🔄 Exécution des migrations..."
php artisan migrate --force

# Exécuter les seeders
echo "🌱 Exécution des seeders..."
php artisan db:seed --force

# Nettoyer et optimiser le cache
echo "🧹 Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Définir les permissions finales
echo "🔒 Configuration des permissions..."
chown -R www-data:www-data /var/www/html/database
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "✅ Application prête !"
echo "📡 API disponible sur le port 80"

# Exécuter la commande passée au conteneur
exec "$@"
```

### 3️⃣ `.dockerignore` (à la racine)
```
# Dépendances
/vendor
/node_modules

# Fichiers de développement
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log

# IDE
.idea
.vscode
*.swp
*.swo
*~

# Git
.git
.gitignore
.gitattributes

# Tests
/tests

# Documentation
README.md
CHANGELOG.md

# Base de données (sera créée dans le conteneur)
/database/database.sqlite

# Fichiers système
.DS_Store
Thumbs.db

# Docker
docker-compose.yml
Dockerfile
.dockerignore