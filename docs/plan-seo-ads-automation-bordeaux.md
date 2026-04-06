# Plan SEO + Google Ads + Automatisation (Bordeaux)

Ce document sert de plan d'exécution pour un site d'estimation immobilière en **PHP natif + MySQL**, orienté génération de leads vendeurs, avec extension vers acheteurs et investisseurs.

## 1) Arborescence SEO recommandée (silos)

### Silo 1 — Estimation (intention: obtenir un prix)
- `/estimation/`
- `/estimation/appartement/`
- `/estimation/maison/`
- `/estimation/loft/`
- `/estimation/{type-bien}/{ville}/`
- `/estimation/{type-bien}/{ville}/{quartier}/`

Exemples:
- `/estimation/appartement/bordeaux/chartrons/`
- `/estimation/maison/merignac/`
- `/estimation/loft/bordeaux/centre/`

### Silo 2 — Vendre (intention: mandat / prise de contact)
- `/vendre/`
- `/vendre/{type-bien}/{ville}/`
- `/vendre/{type-bien}/{ville}/{quartier}/`

Exemples:
- `/vendre/maison/merignac/`
- `/vendre/appartement/bordeaux/cauderan/`

### Silo 3 — Achat (intention: acquéreur)
- `/acheter/`
- `/acheter/{type-bien}/{ville}/`
- `/acheter/{type-bien}/{ville}/{quartier}/`

### Silo 4 — Investissement (intention: rendement)
- `/investir/`
- `/investir/{ville}/`
- `/investir/{ville}/{quartier}/`

### Silo 5 — Blog (capture long terme)
- `/blog/`
- `/blog/prix-immobilier-bordeaux-2026/`
- `/blog/meilleurs-quartiers-investir-bordeaux/`
- `/blog/rendement-locatif-merignac/`

## 2) Mapping mots-clés -> pages

### A. High intent (pages transactionnelles)
- `estimation gratuite appartement bordeaux` -> `/estimation/appartement/bordeaux/`
- `prix m2 bordeaux chartrons` -> `/estimation/appartement/bordeaux/chartrons/`
- `vendre maison merignac rapidement` -> `/vendre/maison/merignac/`

### B. Mid intent (comparaison / guide)
- `agence estimation bordeaux` -> `/estimation/`
- `combien vaut mon appartement bordeaux` -> `/estimation/appartement/bordeaux/`

### C. Long terme (blog)
- `prix immobilier bordeaux 2026` -> `/blog/prix-immobilier-bordeaux-2026/`
- `meilleurs quartiers pour investir à bordeaux` -> `/blog/meilleurs-quartiers-investir-bordeaux/`

## 3) Structure Google Ads (Search)

## Campagnes recommandées
1. `SEA | Estimation | Bordeaux`
2. `SEA | Vendre | Mérignac`
3. `SEA | Vendre | Bordeaux Quartiers`
4. `SEA | Investir | Bordeaux`

## Groupes d'annonces (ad groups)
- Par **intention**: Estimer / Vendre / Acheter / Investir
- Puis par **type de bien**: Appartement / Maison / Loft
- Puis par **zone**: Ville / Quartier

Exemple: `SEA | Estimation | Bordeaux`
- AG 1: `Appartement Bordeaux`
- AG 2: `Maison Bordeaux`
- AG 3: `Appartement Chartrons`

## Règles clés
- Correspondances: Exact + Expression en priorité.
- Mots-clés négatifs globaux: `location`, `stage`, `emploi`, `gratuit pdf`, etc.
- 1 landing page ultra-alignée par ad group.
- Extensions: accroches ("estimation en 2 min"), sitelinks (quartiers), extension lieu et appel.

## 4) Automatisation des pages en PHP natif

## 4.1 Modèle de table MySQL (lexique)

```sql
CREATE TABLE keyword_lexicon (
  id INT AUTO_INCREMENT PRIMARY KEY,
  intent ENUM('estimer','vendre','acheter','investir','blog') NOT NULL,
  property_type VARCHAR(50) NOT NULL,
  city VARCHAR(80) NOT NULL,
  district VARCHAR(80) DEFAULT NULL,
  main_keyword VARCHAR(255) NOT NULL,
  secondary_keywords JSON NULL,
  cpc_estimate DECIMAL(10,2) DEFAULT NULL,
  priority TINYINT DEFAULT 3,
  status ENUM('active','paused') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 4.2 Route unique + paramètres SEO

Créer un routeur (ou règle `.htaccess`) vers un contrôleur unique, puis charger un template selon `intent` + `type-bien`.

```php
<?php
// core/pages/landing-dynamic.php
$intent = $_GET['intent'] ?? 'estimer';
$type   = $_GET['type'] ?? 'appartement';
$city   = $_GET['city'] ?? 'bordeaux';
$area   = $_GET['district'] ?? null;

$kw = $keywordRepository->findBestKeyword($intent, $type, $city, $area);

$title = ucfirst($kw['main_keyword']) . ' | Estimation Immobilière Bordeaux';
$description = sprintf(
    "Obtenez une %s de votre %s à %s%s. Résultat rapide et gratuit.",
    $intent === 'estimer' ? 'estimation' : $intent,
    $type,
    ucfirst($city),
    $area ? ' (' . ucfirst($area) . ')' : ''
);
$h1 = ucfirst($kw['main_keyword']);
$canonical = 'https://www.exemple.fr/' . $intent . '/' . $type . '/' . $city . ($area ? '/' . $area : '') . '/';
```

## 4.3 Gabarits SEO automatiques

Règles simples:
- `title`: `[mot-clé principal] | [marque]`
- `meta description`: bénéfice + zone + CTA
- `H1`: exact match ou variante proche du mot-clé principal

## 5) Recommandations SEO technique

- Utiliser une **balise canonique** unique sur toutes les pages locales.
- Générer un **sitemap XML dynamique** pour les pages `intent/type/ville/quartier` actives.
- Mettre en place `schema.org`:
  - `RealEstateAgent` ou `LocalBusiness` sur pages locales.
  - `FAQPage` sur pages estimation/vendre avec questions utiles.
  - `BreadcrumbList` sur toutes les pages de silo.
- Performance:
  - images WebP + `loading="lazy"`
  - CSS critique inline sur templates SEO
  - cache HTTP (`Cache-Control`) et compression Brotli/Gzip

## 6) Stratégie éditoriale (blog)

### Clusters prioritaires
1. `Prix au m² Bordeaux` (update mensuelle)
2. `Investir par quartier` (guides durables)
3. `Vendre rapidement` (guides pratiques + checklist)

### Cadence minimale
- 2 articles/mois evergreen.
- 1 page data update/mois (prix, tendances, tension locative).

## 7) KPI à suivre (SEO + SEA)

- SEO:
  - leads organiques (formulaire estimation)
  - positions top 3/10 sur requêtes high intent
  - trafic non-marque sur silos estimation/vendre
- SEA:
  - taux de conversion par campagne et ad group
  - coût par lead (CPL)
  - Quality Score par groupe d'annonces

## 8) Plan d'implémentation en 30 jours

Semaine 1:
- finaliser taxonomy `intent/type/ville/quartier`
- préparer table `keyword_lexicon`
- définir templates SEO + variables

Semaine 2:
- déployer routeur dynamique + pages estimation/vendre
- publier 5 pages high intent (Bordeaux + Mérignac)

Semaine 3:
- lancer 2 campagnes Google Ads (Estimation + Vendre)
- brancher tracking conversions (form_submit, call_click)

Semaine 4:
- publier 2 articles blog
- optimiser annonces et pages selon premiers taux de conversion
