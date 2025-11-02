
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