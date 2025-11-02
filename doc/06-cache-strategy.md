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