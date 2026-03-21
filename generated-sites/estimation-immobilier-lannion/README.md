# Estimation Immobilier Lannion (PHP MVC)

Application SaaS d'estimation immobilière pour **Lannion et ses alentours** en **PHP 8+**, architecture MVC légère, **MySQL (PDO)**.

## Fonctionnalités

- Estimateur immobilier (`/estimation`) avec calcul bas/moyen/haut
- Intégration Perplexity AI pour prix m² (fallback local si API indisponible)
- Capture lead après estimation avec scoring automatique (`chaud`, `tiede`, `froid`)
- Stockage sécurisé en MySQL via requêtes préparées
- Liste des leads avec filtres par score (`/leads?score=chaud|tiede|froid`)
- Blog avec génération d'articles IA (OpenAI)
- Guide des quartiers de lannion avec carte interactive
- Newsletter et pages légales (RGPD)
- Mode maintenance configurable

## Arborescence

```text
/public          # Point d'entrée web
/app
  /controllers   # Contrôleurs MVC
  /models        # Modèles de données
  /services      # Services métier (estimation, IA, scoring)
  /views         # Templates PHP
  /core          # Router, Database, Config, Validator
/config          # Configuration applicative
/routes          # Définition des routes
/database        # Schéma SQL
/tests           # Tests unitaires (PHPUnit)
```

## Installation

1. Configurer Apache avec `DocumentRoot` sur `public/`
2. Créer la base MySQL
3. Exécuter `database/schema.sql`
4. Copier `.env.example` en `.env` et configurer les variables
5. Installer les dépendances : `composer install`
6. Ouvrir `/estimation`

## Routes principales

- `GET /` → page d'accueil
- `GET /estimation` → formulaire d'estimation
- `POST /estimation` → calcul estimation
- `POST /api/estimation` → endpoint API JSON
- `POST /lead` → insertion lead
- `GET /leads` → visualisation + filtres des leads
- `GET /quartiers` → guide des quartiers de lannion
- `GET /blog` → articles immobiliers
- `GET /contact` → formulaire de contact

## Tests

```bash
composer test
```

## Mode maintenance

`MAINTENANCE_MODE=true` active une page de maintenance (HTTP 503) avec retry configurable. La route `/admin/leads` reste accessible.
