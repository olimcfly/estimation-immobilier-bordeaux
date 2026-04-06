# 🏠 EstimIA - Estimation Immobilière Intelligente

Application web multi-tenant d'estimation immobilière en ligne 
avec CRM intégré et guide Google Ads.

## 🚀 Installation rapide (< 5 minutes)

### Prérequis
- Hébergement O2Switch (ou tout hébergement PHP 8.x + MySQL)
- Base de données MySQL créée via cPanel
- Clé API Google Maps (Places + Geocoding)

### Étapes

1. **Créez la base de données** dans cPanel O2Switch :
   - cPanel > Bases de données MySQL
   - Créez une base : `prefix_estimia`
   - Créez un utilisateur : `prefix_admin`
   - Assignez l'utilisateur à la base (TOUS les privilèges)

2. **Uploadez les fichiers** via le gestionnaire de fichiers cPanel
   dans `public_html/estimia/` (ou à la racine)

3. **Lancez l'installeur** : `https://votre-domaine.com/install/`

4. **Suivez le wizard** : ville cible, rayon, base de données, SMTP

5. **C'est prêt !** Accédez à l'admin : `/admin/`

## 📁 Structure

estimia/
├── index.php              # Page estimation publique
├── resultat.php           # Page résultat estimation
├── .htaccess              # Sécurité + URL rewriting
├── robots.txt
│
├── admin/                 # Back-office
│   ├── index.php          # Login
│   ├── dashboard.php      # Tableau de bord
│   ├── leads.php          # Liste des leads
│   ├── lead.php           # Détail lead + CRM
│   ├── analytics.php      # Statistiques
│   ├── map.php            # Carte des leads
│   ├── google-ads.php     # Guide Google Ads
│   ├── settings.php       # Paramètres
│   ├── export.php         # Exports CSV/PDF/SQL
│   └── webhooks.php       # Logs webhooks
│
├── install/               # Installeur (supprimable après)
│   ├── index.php
│   └── process.php
│
├── config/                # Configuration (généré à l'install)
│   ├── config.php
│   └── database.php
│
├── classes/               # Classes PHP
│   ├── Database.php
│   ├── Estimation.php
│   ├── Lead.php
│   ├── Mailer.php
│   ├── Settings.php
│   └── Webhook.php
│
├── includes/              # Fichiers inclus
│   ├── header.php
│   ├── footer.php
│   ├── admin-header.php
│   ├── admin-sidebar.php
│   ├── security.php
│   ├── error-handler.php
│   └── cookie-banner.php
│
├── templates/
│   └── emails/            # Templates emails HTML
│       ├── layout.html
│       ├── estimation-result.html
│       ├── estimation-detail.html
│       ├── rdv-confirmation.html
│       └── relance-j3.html
│
├── pages/                 # Pages publiques
│   ├── mentions-legales.php
│   ├── politique-confidentialite.php
│   ├── prix-m2.php
│   ├── 404.php
│   ├── 500.php
│   ├── maintenance.php
│   └── sitemap.php
│
├── cron/                  # Tâches planifiées
│   └── send-relances.php
│
├── sql/                   # Fichiers SQL
│   ├── install.sql
│   └── seed-settings.sql
│
├── assets/                # Fichiers statiques
│   ├── css/
│   ├── js/
│   └── img/
│
├── logs/                  # Logs (auto-créé)
└── installed.lock         # Verrou d'installation

## 📊 Fonctionnalités

### Public
- ✅ Estimation immobilière en 2 minutes
- ✅ Autocomplétion Google Maps
- ✅ Estimation détaillée multi-critères
- ✅ Prise de RDV intégrée
- ✅ Pages SEO prix au m² par ville
- ✅ RGPD compliant (consentement, cookies, désinscription)

### Admin / CRM
- ✅ Dashboard temps réel avec KPI
- ✅ Pipeline leads (Kanban)
- ✅ Lead scoring automatique
- ✅ Carte géographique des leads
- ✅ Analytics détaillées
- ✅ Export CSV / PDF / Backup SQL
- ✅ Paramètres complets (coefficients, SMTP, intégrations)
- ✅ Notifications email temps réel

### Google Ads
- ✅ Guide SOP complet intégré
- ✅ Générateur de mots-clés par ville
- ✅ Textes d'annonces prêts à copier
- ✅ Calculateur ROI Ads
- ✅ Stratégie par niveaux de conscience

### Multi-Tenant
- ✅ Installeur façon WordPress
- ✅ Compatible O2Switch (cPanel)
- ✅ Configuration ville + rayon
- ✅ Prix au m² par ville du rayon
- ✅ Webhooks pour intégrations externes

## ⚙️ Cron Jobs (O2Switch)

Dans cPanel > Tâches Cron, ajoutez :

# Relances emails - tous les jours à 10h
0 10 * * * /usr/local/bin/php /home/USER/public_html/estimia/cron/send-relances.php

## 🔐 Sécurité

- Protection CSRF sur tous les formulaires
- Rate limiting anti-spam
- Protection brute force login
- Headers de sécurité (CSP, X-Frame, etc.)
- Sanitization de tous les inputs
- Accès bloqué aux dossiers sensibles (.htaccess)
- Mots de passe hashés (bcrypt)
- Sessions sécurisées

## 📝 Licence

Usage commercial autorisé. Revente du code interdite.

## 🆘 Support

Pour toute question : [votre email]
