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