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