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

## 📅 ÉTAPE 7 : DÉPLOIEMENT

### 7.1 Pour une nouvelle installation

1. Exécuter `core/database/install.sql`.
2. Configurer les permissions pour `core/cron/backups/` :
   ```bash
   chmod 755 core/cron/backups/
   ```

### 7.2 Pour une installation existante

1. Exécuter `sql/admin_security_upgrade.sql`.
2. Mettre à jour les fichiers PHP concernés (`security.php`, `admin-auth.php`, etc.).
3. Configurer le cron pour les sauvegardes.

## Onboarding admin

- L'accès à `/admin/onboarding.php` est piloté par la session admin et le nombre d'entrées dans `admins`.
- Il n'existe pas de mécanisme `setup.lock` dans ce dépôt.

## Sauvegarde et restauration de la base

### Restaurer une sauvegarde SQL

```bash
mysql -u [utilisateur] -p [base_de_données] < /chemin/vers/backup/YYYY-MM-DD_HH-MM-SS.sql
```

## Tests et vérifications (admin + backup)

### Vérifications de syntaxe PHP

```bash
php -l core/includes/security.php
php -l core/includes/admin-auth.php
php -l core/admin/users.php
php -l core/admin/ajax/dvf_sync.php
php -l core/cron/send-relances.php
```

### Tests manuels

| Scénario | Résultat attendu |
| --- | --- |
| Connexion en tant qu'admin | Le statut `is_online` est mis à jour dans `admins` et `user_sessions`. |
| Déconnexion | Le statut `is_online` passe à `FALSE`, la session est supprimée de `user_sessions`. |
| Accès à `/admin/users.php` | Page accessible uniquement pour `superadmin`, liste des utilisateurs affichée. |
| Synchronisation DVF depuis l'admin | Retour AJAX valide, aucune erreur PHP côté endpoint `core/admin/ajax/dvf_sync.php`. |
| Exécution de `core/cron/send-relances.php` | Le script s'exécute sans erreur fatale et journalise les envois si des relances sont à traiter. |

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
