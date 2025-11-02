Parfait ! Voici comment démarrer avec Docker :

## 🚀 Démarrage en local avec Docker

### 1️⃣ Vérifier que tout est prêt

```bash
# Tu dois être à la racine de ton projet Laravel
ls -la

# Tu dois voir :
# - Dockerfile
# - docker-compose.yml
# - .dockerignore
# - docker/ (dossier avec apache.conf et entrypoint.sh)
```

### 2️⃣ Rendre le script exécutable

```bash
chmod +x docker/entrypoint.sh
```

### 3️⃣ Lancer Docker

```bash
# Construction et démarrage
docker-compose up -d --build
```

**Ça va prendre 2-5 minutes la première fois** (téléchargement de l'image PHP, installation des dépendances, etc.)

### 4️⃣ Voir les logs en temps réel

```bash
docker-compose logs -f
```

Tu devrais voir :
```
🚀 Démarrage de l'application SpaceX API...
📝 Création du fichier .env...
🔑 Génération de la clé d'application...
💾 Création de la base de données SQLite...
🔄 Exécution des migrations...
🌱 Exécution des seeders...
✅ Application prête !
```

**Appuie sur `Ctrl+C` pour sortir des logs** (le conteneur continue de tourner)

### 5️⃣ Vérifier que ça fonctionne

```bash
# Health check
curl http://localhost:8000/api/health

# Devrait retourner :
# {"status":"ok","timestamp":"2025-11-02T...","service":"SpaceX API"}
```

### 6️⃣ Tester le login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

*(Ajuste l'email/password selon ton UserSeeder)*

## 🔧 Commandes utiles

```bash
# Voir l'état des conteneurs
docker-compose ps

# Arrêter
docker-compose down

# Redémarrer
docker-compose restart

# Voir les logs
docker-compose logs -f

# Entrer dans le conteneur
docker-compose exec laravel bash

# Réinitialiser la DB
docker-compose exec laravel php artisan migrate:fresh --seed
```

## ❌ Si ça ne marche pas

### Erreur de permissions sur entrypoint.sh ?
```bash
chmod +x docker/entrypoint.sh
docker-compose down
docker-compose up -d --build
```

### Port 8000 déjà utilisé ?
```bash
# Changer le port dans docker-compose.yml
ports:
  - "8080:80"  # Au lieu de 8000:80

# Puis relancer
docker-compose up -d --build
```

### Voir les erreurs détaillées
```bash
docker-compose logs laravel
```

**Dis-moi ce que tu vois quand tu lances `docker-compose up -d --build` !** 🚀