# ⚡ Quick Start - Configuration Docker

## 📁 Structure des fichiers à créer

Copiez ces fichiers dans votre projet Laravel :

```
votre-projet-laravel/
├── docker/
│   ├── apache.conf          ← Configuration Apache
│   └── entrypoint.sh         ← Script de démarrage
├── Dockerfile                ← Image Docker
├── docker-compose.yml        ← Orchestration
├── .dockerignore             ← Fichiers à ignorer
├── render.yaml               ← Config Render (optionnel)
├── deploy.sh                 ← Script de déploiement (optionnel)
└── routes/api.php            ← Ajoutez la route /health
```

## 🚀 Démarrage en 3 commandes

### 1. Créer la structure

```bash
mkdir -p docker
```

### 2. Placer tous les fichiers

Copiez le contenu des artifacts dans les fichiers correspondants.

### 3. Lancer Docker

```bash
# Option A : Avec le script
chmod +x deploy.sh
./deploy.sh

# Option B : Manuellement
docker-compose up -d --build
```

## ✅ Vérification

```bash
# Attendre 30 secondes puis tester
curl http://localhost:8000/api/health

# Devrait retourner :
# {"status":"ok","timestamp":"...","service":"SpaceX API"}
```

## 🔑 Tester l'authentification

```bash
# 1. Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Copier le token retourné

# 2. Accéder au dashboard
curl http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

## 🌐 Déployer sur Render

```bash
# 1. Créer la branche
git checkout -b docker/deploy
git add .
git commit -m "feat: Docker configuration"
git push origin docker/deploy

# 2. Sur render.com
# - New + → Blueprint
# - Sélectionner votre repo
# - Branch: docker/deploy
# - Apply

# 3. Attendre 5-10 minutes
# → Vous aurez votre URL : https://spacex-api-xxxx.onrender.com
```

## 📱 Configurer Angular

```typescript
// src/environments/environment.prod.ts
export const environment = {
  production: true,
  apiUrl: 'https://spacex-api-xxxx.onrender.com/api'
};

// src/app/services/spacex.ts
private apiUrl = environment.apiUrl;
```

## 🎯 Résumé final

| Étape | Commande/Action | Résultat |
|-------|----------------|----------|
| 1 | Créer les fichiers Docker | Structure prête |
| 2 | `docker-compose up -d --build` | API en local (port 8000) |
| 3 | `curl localhost:8000/api/health` | ✅ API fonctionne |
| 4 | Push sur GitHub | Code en ligne |
| 5 | Déployer sur Render | API en production |
| 6 | Configurer Angular avec l'URL | Frontend connecté |
| 7 | Déployer Angular sur Vercel | App complète en ligne |

## 🎓 Pour le DG

**Commande unique pour démarrer :**
```bash
docker-compose up -d --build
```

**Vérifier :**
```bash
curl http://localhost:8000/api/health
```

**C'est tout ! L'API tourne avec :**
- ✅ Base de données SQLite
- ✅ Utilisateurs créés automatiquement
- ✅ Authentification Sanctum
- ✅ Données SpaceX synchronisées

---

**Questions ? Besoin d'aide ?**

- Logs : `docker-compose logs -f`
- Arrêter : `docker-compose down`
- Redémarrer : `docker-compose restart`