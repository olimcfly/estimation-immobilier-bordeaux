# Installateur de Estimateur Immobilier avec base DVF

Ce dépôt est désormais organisé en **noyau réutilisable** (`core/`) + **personnalisation locale** (`site-specific/`).

## Structure

```text
estimation-immobilier-bordeaux/
├── core/
│   ├── admin/
│   │   ├── ajax/
│   │   ├── includes/
│   │   ├── modules/
│   │   ├── index.php
│   │   ├── login.php
│   │   ├── users.php
│   │   └── settings.php
│   ├── api/
│   ├── assets/
│   ├── classes/
│   ├── config/
│   ├── cron/
│   ├── database/
│   ├── dvf-estimation/
│   ├── includes/
│   ├── install/
│   ├── logs/
│   ├── pages/
│   ├── templates/
│   └── index.php
├── site-specific/
│   ├── assets/
│   ├── config/
│   └── pages/
├── .htaccess
├── README.md
├── composer.json
└── index.php
```

## Principes

- `core/` contient la logique portable d'un projet à l'autre.
- `site-specific/` contient le branding (logo, pages locales, configuration locale).
- Le point d'entrée racine (`index.php`) délègue vers `core/index.php`.
- `.htaccess` maintient des routes courtes (`/admin`, `/api`, etc.) vers `core/`.

## Démarrage

1. Configurer `site-specific/config/site.php`.
2. Vérifier `core/config/config.php` et `core/config/database.php`.
3. Accéder à `/install/` pour compléter l'installation si nécessaire.

## 💾 Sauvegardes automatiques

### Configuration du cron

Ajoutez cette ligne à votre crontab (`crontab -e`) pour une sauvegarde quotidienne à 2h du matin :

```bash
0 2 * * * /usr/bin/php /chemin/vers/skyline/cron/backup_db.php >> /var/log/skyline_backup.log 2>&1
```

## Onboarding admin

- L'accès à `/admin/onboarding.php` est piloté par la session admin et le nombre d'entrées dans `admins`.
- Il n'existe pas de mécanisme `setup.lock` dans ce dépôt.

## Ressources

- Plan SEO/SEA + automatisation: `docs/plan-seo-ads-automation-bordeaux.md`

## Intégration SEO/Ads (implémentation)

- Gestion du lexique: `core/classes/SEO/KeywordManager.php`
- Génération de pages SEO: `core/classes/SEO/PageGenerator.php`
- Schema.org: `core/classes/SEO/SchemaMarkup.php`
- Préparation intégration Google Ads: `core/classes/SEO/GoogleAdsAPI.php`
- Page dynamique estimation: `core/pages/estimation.php`
- Templates SEO: `core/templates/seo/meta.php`, `core/templates/seo/schema.php`, `core/templates/seo/estimation.php`
- Scripts utilitaires: `core/scripts/generate_pages.php`, `core/scripts/import_keywords.php`
