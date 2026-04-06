# Plan SEO/SEA + Automatisation – Bordeaux

## 1. Objectifs
- **SEO** : Générer 50% du trafic via les pages "Estimation" et le blog.
- **Google Ads** : Cibler 20 mots-clés high intent (ex: "estimation appartement Bordeaux Chartrons").
- **Automatisation** : Créer 50+ pages en 1 semaine via des templates PHP.

## 2. Lexique de mots-clés

| Mot-clé | Volume/mois | Intent | Page cible |
|---|---:|---|---|
| estimation appartement bordeaux chartrons | 1200 | High | `/estimation/appartement/bordeaux/chartrons/` |
| vendre maison mérignac | 800 | High | `/vendre/maison/merignac/` |
| prix m2 bordeaux 2024 | 2500 | SEO | `/blog/prix-m2-bordeaux-2024/` |

## 3. Implémentation technique

### Classes SEO
- `core/classes/SEO/KeywordManager.php`
  - Lecture des mots-clés en base `seo_keywords` par `type_bien`, `ville`, `quartier`.
  - Génération d'un `title` SEO à partir du mot-clé principal.
- `core/classes/SEO/PageGenerator.php`
  - Génération du HTML d'une page estimation depuis `core/templates/seo/estimation.php`.
  - Injection `TITLE`, `H1`, `META_DESCRIPTION`, `KEYWORDS`.
- `core/classes/SEO/SchemaMarkup.php`
  - Génération `RealEstateAgent` et `FAQPage`.
- `core/classes/SEO/GoogleAdsAPI.php`
  - Construction d'événements de conversion (pré-intégration API).

### Routes
- `/estimation/{type}/{ville}/{quartier?}` -> `core/pages/estimation.php`
- `/blog/{slug}` -> `core/pages/blog.php`
- `/vendre/{type}/{ville}` -> `core/pages/vente.php`

### Templates
- `core/templates/seo/meta.php`
- `core/templates/seo/schema.php`
- `core/templates/seo/estimation.php`

### Configuration locale
- `site-specific/config/site.php`
  - villes/quartiers/types de biens ciblés
  - paramètres Google Ads (`client_id`, `conversion_id`)

## 4. Scripts d'automatisation

### Génération de pages
```bash
php core/scripts/generate_pages.php --type=appartement --ville=bordeaux --quartier=chartrons
```

### Import du lexique
```bash
php core/scripts/import_keywords.php --file=lexique.csv
```

## 5. Structure Google Ads
- Groupe "Estimation Bordeaux": `estimation appartement bordeaux`, `prix m2 bordeaux`.
- Groupe "Vente Mérignac": `vendre maison mérignac`, `agence immobilière mérignac`.
- Landing dédiée par groupe avec correspondance stricte intention -> page.

## 6. Suivi
- **SEO** : Google Search Console (impressions, CTR, positions).
- **Google Ads** : taux de conversion et CPL par mot-clé.
- **Automatisation** : logs de génération dans `var/logs/generation.log`.
