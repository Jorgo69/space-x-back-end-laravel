Absolument ! Voici une **documentation complète, claire et pédagogique**, organisée en fichiers Markdown (`*.md`), destinée à **expliquer chaque composant de ton backend Laravel** pour le test **“SpaceX Launch Dashboard”**.

Elle est conçue pour être **compréhensible même par un non-développeur** (chef de projet, recruteur technique, auditeur), avec :
- Des **explications simples**,
- Des **exemples concrets**,
- Des **logs simulés**,
- Des **liens vers l’API SpaceX**,
- Le **comportement attendu** à chaque étape,
- Ce qui est **stocké en cache**, **quand**, **pourquoi**,
- Et **ce que retourne chaque endpoint**.

---

## 📁 Structure proposée des fichiers `.md`

```
docs/
├── 00-introduction.md
├── 01-authentification.md
├── 02-models.md
├── 03-services.md
├── 04-controllers.md
├── 05-routes.md
├── 06-cache-strategy.md
└── 07-exemple-de-flux-complet.md
```

Je vais te fournir **le contenu de chaque fichier** ci-dessous. Tu pourras les copier dans des fichiers séparés.

---

### 📄 `00-introduction.md`

```markdown
# Introduction – SpaceX Launch Dashboard (Backend Laravel)

Ce document explique **comment le backend Laravel** de l’application “SpaceX Launch Dashboard” fonctionne, **pas à pas**, sans supposer de connaissances techniques avancées.

## Objectif global

> Récupérer des données publiques depuis l’[API SpaceX](https://github.com/r-spacex/SpaceX-API), les **mettre en cache**, les **traiter**, puis les **exposer via une API sécurisée** que seul le frontend Angular peut consommer.

### Pourquoi ne pas appeler SpaceX directement depuis Angular ?
- L’API SpaceX **n’autorise pas les requêtes depuis un navigateur** (problème CORS).
- On veut **éviter de surcharger l’API publique** → on met en cache les données.
- On veut **centraliser la logique métier** (calculs, filtres, enrichissement) côté serveur.

---

## Architecture simplifiée

```
Angular (frontend)
       ↓ (appelle)
Laravel (backend)
       ↓ (appelle une seule fois, puis cache)
SpaceX API (v5 + v4)
```

✅ Tout le traitement complexe est fait **dans Laravel**.  
✅ Angular ne fait que **afficher** les données reçues.
```

---

### 📄 `01-authentification.md`

```markdown
# Authentification – Sanctum + Rôles

## Mécanisme utilisé : Laravel Sanctum (mode “Personal Access Token”)

Au lieu d’utiliser JWT (complexe), on utilise **Sanctum**, un outil officiel de Laravel, qui permet de :
- Créer un **token unique** lors de la connexion,
- Envoyer ce token dans chaque requête suivante,
- Identifier l’utilisateur et son rôle (`user` ou `admin`).

### Étapes de connexion

1. L’utilisateur envoie son email + mot de passe à `/api/login`.
2. Le backend vérifie les identifiants.
3. S’ils sont bons, il crée un **token d’accès** (ex: `1|abc123...`).
4. Ce token est retourné à Angular, qui le stocke.
5. Toutes les requêtes suivantes incluent :  
   `Authorization: Bearer 1|abc123...`

> 🔒 Ce token expire après **24 heures** (configurable dans `config/sanctum.php`).

### Rôles

- **USER** : peut consulter le dashboard, les lancements.
- **ADMIN** : peut en plus appeler `/api/sync` pour forcer une mise à jour.

Le rôle est stocké dans la base de données (`users.role`).

### Comptes de test

| Email               | Mot de passe | Rôle   |
|---------------------|--------------|--------|
| user@example.com    | password     | user   |
| admin@example.com   | password     | admin  |

> Ces comptes sont créés automatiquement via un “seeder” au démarrage.
```

---

### 📄 `02-models.md`

```markdown
# Modèles – La table `users`

## Table `users`

Stocke les comptes utilisateurs.

### Champs

| Champ     | Type     | Description                     |
|-----------|----------|---------------------------------|
| id        | UUID     | Identifiant unique              |
| email     | string   | Email (unique)                  |
| password  | string   | Mot de passe haché              |
| role      | enum     | `'user'` ou `'admin'`           |
| created_at| datetime | Date de création                |
| updated_at| datetime | Date de dernière modification   |

> ❌ Pas de nom, pas de vérification d’email → simplifié pour le test.

### Modèle Laravel (`App\Models\User`)

Utilise le trait `HasApiTokens` de Sanctum → permet de créer des tokens.

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = ['email', 'password', 'role'];
}
```
```

---

### 📄 `03-services.md`

```markdown
# Service – `SpaceXService`

Ce service centralise **tous les appels à l’API SpaceX** et gère le **cache**.

## Pourquoi un service ?
- Éviter de dupliquer la logique dans plusieurs contrôleurs.
- Faciliter les tests et la maintenance.
- Centraliser les logs et la gestion d’erreurs.

---

## Méthodes principales

### 1. `getAllLaunches(bool $forceRefresh = false)`

#### 🔗 Appel à l’API
- URL : `https://api.spacexdata.com/v5/launches`
- Méthode : `GET`
- Données : liste de tous les lancements (historique + futurs)

#### ⏱ Cache
- Clé : `spacex_launches`
- Durée : **24 heures** (1440 minutes)
- Si `$forceRefresh = true` → cache vidé, nouvel appel

#### 📝 Exemple de log
```log
[2025-11-02 10:00:00] Appel à l’API SpaceX v5...
[2025-11-02 10:00:02] Données récupérées : 250 lancements
```

#### ➡️ Retourne
Un tableau de lancements au format JSON (voir [exemple SpaceX](https://api.spacexdata.com/v5/launches)).

---

### 2. `getRocket(string $id)`

#### 🔗 Appel à l’API
- URL : `https://api.spacexdata.com/v4/rockets/{id}`
- Utilisé pour enrichir le détail d’un lancement

#### ⏱ Cache
- Clé : `rocket_{id}`
- Durée : 24h

#### ➡️ Retourne
Données de la fusée (nom, description, image, etc.)

---

### 3. `getLaunchpad(string $id)`

Même principe que `getRocket`, mais pour les sites de lancement.

---

### 4. `getDashboardData()`

**Pas d’appel API direct** → utilise `getAllLaunches()` puis **traite les données** :

- Compte total de lancements
- Taux de succès global
- Prochain lancement (tri par date)
- Statistiques par année

#### ➡️ Retourne
```json
{
  "kpi": {
    "total_launches": 250,
    "success_rate": 96.4,
    "next_launch": {
      "name": "Starlink Group 6-58",
      "date_utc": "2025-11-10T12:00:00.000Z",
      "days_until": 8
    }
  },
  "stats_by_year": [
    { "year": "2023", "total": 98, "success_rate": 97.9 },
    { "year": "2024", "total": 112, "success_rate": 95.5 }
  ]
}
```
```

---

### 📄 `04-controllers.md`

```markdown
# Contrôleurs

## `AuthController`

### `login(Request $request)`

- Vérifie email/mot de passe
- Crée un token Sanctum
- Retourne utilisateur + token

✅ Exemple de réponse :
```json
{
  "user": { "id": "...", "email": "admin@example.com", "role": "admin" },
  "token": "1|abc123..."
}
```

---

## `LaunchController`

### `dashboard()`
- Appelle `SpaceXService::getDashboardData()`
- Retourne KPIs + stats

### `list(Request $request)`
- Récupère tous les lancements
- Filtre par `year`, `success`
- Pagination manuelle (`page`, `per_page`)

✅ Exemple de requête :
```
GET /api/launches?page=1&year=2024&success=true
```

### `show(string $id)`
- Trouve un lancement par ID
- Enrichit avec `rocket` et `launchpad` via les méthodes du service
- Retourne tout en un seul objet

---

## `SyncController`

### `resync(Request $request)`
- Vérifie que l’utilisateur est `admin`
- Appelle `getAllLaunches(forceRefresh: true)`
- Retourne confirmation

✅ Exemple de réponse :
```json
{ "message": "Resynchronisation terminée", "launches_count": 250 }
```
```

---

### 📄 `05-routes.md`

```markdown
# Routes API

Toutes les routes sont préfixées par `/api`.

| Méthode | Route               | Middleware        | Description                          | Rôle requis |
|--------|---------------------|-------------------|--------------------------------------|-------------|
| POST   | `/login`            | —                 | Connexion → obtient un token         | —           |
| GET    | `/user`             | `auth:sanctum`    | Récupère le profil de l’utilisateur  | USER/ADMIN  |
| GET    | `/dashboard`        | `auth:sanctum`    | Données du tableau de bord           | USER/ADMIN  |
| GET    | `/launches`         | `auth:sanctum`    | Liste paginée/filtrable              | USER/ADMIN  |
| GET    | `/launches/{id}`    | `auth:sanctum`    | Détail d’un lancement                | USER/ADMIN  |
| POST   | `/sync`             | `auth:sanctum` + `admin` | Force la resynchronisation     | ADMIN       |

> Toutes les routes sauf `/login` nécessitent un header :  
> `Authorization: Bearer <token>`
```

---

### 📄 `06-cache-strategy.md`

```markdown
# Stratégie de mise en cache

## Pourquoi cacher ?
- L’API SpaceX est publique mais **limitée**.
- Éviter de la solliciter à chaque requête utilisateur.
- Améliorer les performances du dashboard.

## Où est stocké le cache ?
- Par défaut : dans `storage/framework/cache/` (fichiers).
- Alternative : Redis (plus rapide, mais non requis ici).

## Données mises en cache

| Donnée               | Clé de cache          | Durée    | Rafraîchissement forcé ? |
|----------------------|------------------------|----------|---------------------------|
| Liste des lancements | `spacex_launches`      | 24h      | Oui (via `/sync`)         |
| Fusée (par ID)       | `rocket_{id}`          | 24h      | Non                       |
| Site de lancement    | `launchpad_{id}`       | 24h      | Non                       |

## Comportement lors d’un appel

1. Le service cherche dans le cache.
2. Si trouvé → retourne les données **sans appeler SpaceX**.
3. Si non trouvé ou expiré → appel à SpaceX → stocke en cache → retourne.

> ✅ Même si 100 utilisateurs chargent le dashboard en même temps, **un seul appel** est fait à SpaceX toutes les 24h (sauf resync manuel).
```

---

### 📄 `07-exemple-de-flux-complet.md`

```markdown
# Exemple de flux complet – Du login au dashboard

## Étape 1 : Connexion
```http
POST /api/login
{ "email": "admin@example.com", "password": "password" }
```
→ Réponse :
```json
{ "token": "1|abc123...", "user": { "role": "admin" } }
```

## Étape 2 : Charger le dashboard
```http
GET /api/dashboard
Authorization: Bearer 1|abc123...
```

### Ce qui se passe dans Laravel :
1. Sanctum authentifie l’utilisateur.
2. `LaunchController@dashboard` est appelé.
3. `SpaceXService@getDashboardData()` est appelé.
4. Le service cherche `spacex_launches` en cache → **non trouvé** (premier appel).
5. Appel à `https://api.spacexdata.com/v5/launches` → 250 lancements reçus.
6. Données mises en cache.
7. Calculs effectués : taux de succès, stats par année, prochain lancement.
8. Réponse JSON envoyée à Angular.

### Logs générés :
```log
[INFO] Utilisateur admin@example.com authentifié.
[INFO] Cache manquant pour 'spacex_launches' → appel à SpaceX API...
[INFO] 250 lancements récupérés. Mise en cache.
[INFO] Dashboard calculé et retourné.
```

## Étape 3 : ADMIN force une resync
```http
POST /api/sync
Authorization: Bearer 1|abc123...
```
→ Cache vidé → nouvel appel à SpaceX → données mises à jour.

✅ Angular n’a **jamais** parlé à SpaceX. Tout est géré par Laravel.
```

---

## ✅ Prochaines étapes

Tu peux :
1. Créer le dossier `docs/` dans ton projet Laravel.
2. Copier chaque contenu ci-dessus dans les fichiers correspondants.
3. Fournir ce dossier au recruteur ou l’inclure dans ton README.

Souhaites-tu que je t’envoie **un ZIP de ces fichiers** ou un **README.md global** qui les référence ?