# Estimateur Immobilier SaaS (PHP MVC)

Application SaaS immobilière en **PHP 8+**, architecture MVC légère, **MySQL (PDO)**.

## Fonctionnalités livrées

- Estimateur immobilier (`/estimation`) avec calcul bas/moyen/haut
- Intégration Perplexity pour prix m² (fallback local si API indisponible)
- Capture lead après estimation
- Stockage lead sécurisé en MySQL via requêtes préparées
- Scoring lead (`chaud`, `tiede`, `froid`)
- Blog SEO public (`/blog`, `/blog/{slug}`)
- CMS blog admin (`/admin/blog`) avec génération d'article IA

## Arborescence

```text
/public
/app
  /controllers
  /models
  /services
  /views
  /core
/config
/routes
/storage
/database
```

## Installation

1. Configurer Apache avec `DocumentRoot` sur `public/`
2. Créer la base MySQL
3. Exécuter `database/schema.sql`
4. Définir les variables d'environnement:

```bash
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_NAME=immobilier_saas
export DB_USER=root
export DB_PASS=''
export PERPLEXITY_API_KEY=''
```

5. Ouvrir `/estimation`

## Routes

- `GET /` → redirection logique estimateur (même vue)
- `GET /estimation` → formulaire
- `POST /estimation` → calcul estimation
- `POST /lead` → insertion lead
- `GET /blog` → listing des articles publiés
- `GET /blog/{slug}` → détail article
- `GET /admin/blog` → liste CMS
- `GET /admin/blog/create` → création manuelle
- `POST /admin/blog/generate` → pré-remplissage IA
