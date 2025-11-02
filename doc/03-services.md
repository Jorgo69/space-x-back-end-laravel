
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


###### Utilisation
➡️ Retourne
Un tableau de lancements au format JSON (voir exemple SpaceX ).

2. getRocket(string $id)
🔗 Appel à l’API
URL : https://api.spacexdata.com/v4/rockets/{id}
Utilisé pour enrichir le détail d’un lancement
⏱ Cache
Clé : rocket_{id}
Durée : 24h
➡️ Retourne
Données de la fusée (nom, description, image, etc.)

3. getLaunchpad(string $id)
Même principe que getRocket, mais pour les sites de lancement.

4. getDashboardData()
Pas d’appel API direct → utilise getAllLaunches() puis traite les données :

Compte total de lancements
Taux de succès global
Prochain lancement (tri par date)
Statistiques par année

-> Retourne
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