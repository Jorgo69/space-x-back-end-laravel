# Exemple de flux complet – Du login au dashboard

## Étape 1 : Connexion
```http
POST /api/login
{ "email": "admin@example.com", "password": "password" }

-> Reponse
{ "token": "1|abc123...", "user": { "role": "admin" } }

Étape 2 : Charger le dashboard
GET /api/dashboard
Authorization: Bearer 1|abc123...