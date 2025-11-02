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
