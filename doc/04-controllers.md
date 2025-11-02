
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

LaunchController
dashboard()
Appelle SpaceXService::getDashboardData()
Retourne KPIs + stats
list(Request $request)
Récupère tous les lancements
Filtre par year, success
Pagination manuelle (page, per_page)
✅ Exemple de requête :
GET /api/launches?page=1&year=2024&success=true

show(string $id)
Trouve un lancement par ID
Enrichit avec rocket et launchpad via les méthodes du service
Retourne tout en un seul objet
SyncController
resync(Request $request)
Vérifie que l’utilisateur est admin
Appelle getAllLaunches(forceRefresh: true)
Retourne confirmation
✅ Exemple de réponse :

{ "message": "Resynchronisation terminée", "launches_count": 250 }