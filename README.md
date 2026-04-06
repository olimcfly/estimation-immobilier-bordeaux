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
