# 🌐 Déploiement sur Render.com

## 📋 Étapes de déploiement

### 1. Préparer le repository GitHub

```bash
# Créer et pousser la branche docker/deploy
git checkout -b docker/deploy
git add .
git commit -m "feat: Docker configuration for deployment"
git push origin docker/deploy
```

### 2. Créer un compte Render

1. Allez sur [render.com](https://render.com)
2. Inscrivez-vous avec GitHub
3. Autorisez Render à accéder à vos repos

### 3. Créer un nouveau Web Service

#### Option A : Avec render.yaml (Recommandé)

1. Dans Render Dashboard, cliquez **"New +"** → **"Blueprint"**
2. Sélectionnez votre repository
3. Sélectionnez la branche `docker/deploy`
4. Render détectera automatiquement le `render.yaml`
5. Cliquez **"Apply"**
6. Attendez le déploiement (5-10 minutes)

#### Option B : Configuration manuelle

1. Dans Render Dashboard, cliquez **"New +"** → **"Web Service"**
2. Connectez votre repository GitHub
3. Configuration :
   ```
   Name: spacex-api
   Region: Frankfurt (ou le plus proche)
   Branch: docker/deploy
   Runtime: Docker
   Instance Type: Free
   ```

4. Variables d'environnement :
   ```
   APP_NAME=SpaceX API
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=<sera généré>
   APP_URL=https://votre-app.onrender.com
   DB_CONNECTION=sqlite
   DB_DATABASE=/var/www/html/database/database.sqlite
   ```

5. Ajoutez un **Persistent Disk** :
   ```
   Name: spacex-db
   Mount Path: /var/www/html/database
   Size: 1 GB
   ```

6. Cliquez **"Create Web Service"**

### 4. Attendre le déploiement

Render va :
1. ✅ Cloner votre repo
2. ✅ Builder l'image Docker
3. ✅ Déployer le conteneur
4. ✅ Exécuter les migrations et seeders
5. ✅ Rendre l'API disponible

**Temps estimé : 5-10 minutes**

### 5. Récupérer l'URL de l'API

Votre API sera disponible sur :
```
https://spacex-api-xxxx.onrender.com
```

### 6. Tester l'API

```bash
# Health check
curl https://votre-app.onrender.com/api/health

# Login
curl -X POST https://votre-app.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Dashboard (avec le token)
curl https://votre-app.onrender.com/api/dashboard \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

## 🔧 Configuration du frontend Angular

Dans votre projet Angular, créez un fichier `src/environments/environment.prod.ts` :

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://votre-app.onrender.com/api'
};
```

Puis dans votre service :

```typescript
import { environment } from '../environments/environment';

export class ApiService {
  private apiUrl = environment.apiUrl;
  
  // ...
}
```

## 🚀 Déploiement du frontend

### Option 1 : Vercel

```bash
cd votre-projet-angular
npm install -g vercel
vercel --prod
```

### Option 2 : Surge

```bash
npm install -g surge
ng build --configuration production
cd dist/votre-app
surge --domain votre-app.surge.sh
```

### Option 3 : Netlify

1. `ng build --configuration production`
2. Drag & drop le dossier `dist/` sur netlify.com

## ⚙️ Configuration CORS

Assurez-vous que dans Laravel `config/cors.php` :

```php
'allowed_origins' => [
    'http://localhost:4200',
    'https://votre-app.vercel.app',
    'https://votre-app.surge.sh',
    'https://votre-app.netlify.app',
],
```

Ou utilisez un wildcard pour le développement :
```php
'allowed_origins' => ['*'],
```

## 📊 Monitoring

### Voir les logs en direct

1. Dans Render Dashboard, cliquez sur votre service
2. Onglet **"Logs"**
3. Vous verrez les logs d'Apache et Laravel

### Redéployer

1. Dans Render Dashboard
2. Cliquez **"Manual Deploy"** → **"Deploy latest commit"**

## 🐛 Résolution des problèmes

### L'API ne démarre pas

**Vérifiez les logs Render :**
- Erreurs de build Docker ?
- Permissions sur la base SQLite ?
- Variables d'environnement manquantes ?

### Build timeout

Le plan gratuit de Render a une limite de 15 minutes.
Si le build est trop long, optimisez le Dockerfile.

### Base de données vide

Render peut réinitialiser le disque. Ajoutez un persistent disk pour éviter cela.

### CORS errors

Vérifiez que :
1. `config/cors.php` autorise votre domaine frontend
2. Le middleware CORS est actif dans `app/Http/Kernel.php`

## 💰 Limitations du plan gratuit

- ⏰ Le service se met en veille après 15 min d'inactivité
- 🐌 Premier appel après veille : ~30 secondes
- 💾 750 heures/mois de calcul
- 💿 Disque persistant : 1 GB gratuit

**Parfait pour un test technique !** 👍

## ✅ Checklist finale

- [ ] Repository poussé sur GitHub (branche docker/deploy)
- [ ] Render.yaml configuré
- [ ] Service créé sur Render
- [ ] Déploiement réussi (logs OK)
- [ ] Health check répond : `GET /api/health`
- [ ] Login fonctionne : `POST /api/login`
- [ ] Dashboard accessible avec token
- [ ] URL copiée pour le frontend Angular
- [ ] Frontend déployé (Vercel/Surge/Netlify)
- [ ] CORS configuré correctement
- [ ] Test end-to-end OK

## 🎯 URL finale à donner au DG

```
Frontend: https://votre-app.vercel.app
Backend API: https://spacex-api-xxxx.onrender.com
```

---

**Bon courage pour le test ! 🚀**