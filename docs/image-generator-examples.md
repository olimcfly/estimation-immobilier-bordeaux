# Exemples d'Utilisation - Generateur d'Images

Guide pratique avec cas d'usage reels pour le generateur d'images sociales et IA du CMS Estimation Immobilier Bordeaux.

## Cas d'usage reels

### Cas 1 : Blog article - "Comment vendre sa maison en Tregor"

**Donnees d'entree :**

| Champ | Valeur |
|-------|--------|
| Persona | Vendeur Motive |
| Conscience | 3 |
| Localite | Lannion, Tregor |
| Titre | Vendre en Tregor, c'est possible! |
| Slogan | Decouvrez comment vendre votre bien au meilleur prix dans un marche porteur |
| CTA | Lancer mon estimation |

**Resultat :**

- Blog Illustration (1400x800) pour illustrer l'article
- Instagram post (1080x1080) pour promouvoir l'article
- Facebook (1200x628) pour le fil d'actualite
- LinkedIn (1200x628) pour B2B aupres d'autres agents
- GMB (1200x628) pour Google My Business

### Cas 2 : Campagne de lead generation - Proprietaires Inquiets

**Donnees d'entree :**

| Champ | Valeur |
|-------|--------|
| Persona | Proprietaire Inquiet |
| Conscience | 2 |
| Localite | Bordeaux |
| Titre | Vous ne savez pas a quel prix vendre? |
| Slogan | Une estimation transparente et sans engagement pour prendre la bonne decision |
| CTA | Obtenir mon avis de valeur gratuit |

**Utilisation :**

- Utiliser les 5 images dans une campagne Facebook ads
- Tester A/B : Vendeur Motive vs Proprietaire Inquiet
- Mesurer le CTR par format

### Cas 3 : Prelancement de secteur - Investisseur Strategiste

**Donnees d'entree :**

| Champ | Valeur |
|-------|--------|
| Persona | Investisseur Strategiste |
| Conscience | 4 |
| Localite | Aix-en-Provence, secteur Montagne Sainte-Victoire |
| Titre | Investir dans le neuf a Aix, une strategie gagnante |
| Slogan | Rentabilite, plus-value, fiscalite optimisee: tous les chiffres pour decider |
| CTA | Voir les opportunites d'investissement |

**Utilisation :**

- LinkedIn : cibler les investisseurs
- Blog : article detaille avec illustration
- Email : inclusion dans la newsletter professionnelle

### Cas 4 : Relance client - Famille Nouvelles Racines

**Donnees d'entree :**

| Champ | Valeur |
|-------|--------|
| Persona | Famille Nouvelles Racines |
| Conscience | 3 |
| Localite | Nantes, quartier Ile de Nantes |
| Titre | Trouver la maison familiale ideale |
| Slogan | Quartier dynamique, ecoles, transports: tout ce qu'il faut pour s'enraciner |
| CTA | Visiter nos biens disponibles |

**Utilisation :**

- SMS + image pour relancer les clients en attente
- Integrer dans les emails de campagne
- Post Facebook cible (parents, 30-50 ans)

### Cas 5 : Seniorite - Retraite Serein

**Donnees d'entree :**

| Champ | Valeur |
|-------|--------|
| Persona | Retraite Serein |
| Conscience | 4 |
| Localite | Cote d'Azur, Antibes |
| Titre | Un nouveau depart au soleil |
| Slogan | Vivre a la retraite avec serenite dans un cadre idyllique et securise |
| CTA | Explorer mes options de residence |

**Utilisation :**

- Campagne Google Ads ciblee (65+)
- Blog article "Bien-etre a la retraite"
- Partenaire agences specialisees retraite

---

## Automatisation avec n8n

Creez un workflow n8n pour automatiser la generation :

```
trigger: Webhook POST
├── Extract data (persona, titre, slogan, cta, localite)
├── Call Generator API
│   └── POST /api/generate.php
│       ├── persona
│       ├── conscience
│       ├── localite
│       ├── titre
│       ├── slogan
│       └── cta
├── Save to Database
│   └── INSERT into generated_images
├── Upload to S3 (ou systeme fichiers)
│   ├── instagram.png
│   ├── facebook.png
│   ├── linkedin.png
│   ├── gmb.png
│   └── blog.png
├── Send Email Notification
│   └── Alerter quand generations pretes
└── Return JSON
    └── { status: "success", images_url: [...] }
```

**Webhook input format :**

```json
{
  "persona": "vendeur-motive",
  "consciousness": 3,
  "locality": "Lannion",
  "title": "Vendre votre maison",
  "slug": "vendre-votre-maison",
  "cta": "Obtenir une estimation",
  "website_id": 1
}
```

---

## Integration CMS IMMO LOCAL+

Dans `/admin/modules/generateur-images.php` :

```php
<?php
// Charger le generateur depuis le dossier /generator/
session_start();
require_once '../config/database.php';
require_once '../auth/check.php';

$site_config = getSiteConfig($_SESSION['website_id']);

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/generator/styles.css">
    <style>
        /* Adapter le branding du CMS */
        :root {
            --color-primary: <?php echo $site_config['primary_color']; ?>;
            --bg-light: <?php echo $site_config['light_bg']; ?>;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="cms-container">
        <?php include '../components/sidebar.php'; ?>

        <main class="cms-main">
            <?php include '/generator/index.html'; ?>
        </main>
    </div>

    <script src="/generator/generator.js"></script>
    <script>
        // Surcharger la sauvegarde pour utiliser les API du CMS
        const originalGenerate = window.generateImages;
        window.generateImages = async function() {
            await originalGenerate();

            // Sauvegarder dans la BD du CMS
            fetch('/admin/api/generateur-images.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'save_generation',
                    ...currentGeneration,
                    website_id: <?php echo $_SESSION['website_id']; ?>
                })
            });
        };
    </script>
</body>
</html>
```

---

## Dashboard de tracking

Afficher l'historique des generations via `/admin/social-images` ou via le endpoint API.

**Donnees trackees :**

| Champ | Description |
|-------|-------------|
| Date | Date/heure de generation |
| Template | Type de template (estimation, conseil, stat, story, paysage) |
| Filename | Nom du fichier genere |
| Taille | Poids du fichier en Ko |

L'interface d'administration integre deja une galerie des images sauvegardees avec :
- Apercu visuel
- Nom de fichier et metadonnees
- Telechargement direct
- Suppression

---

## Email integration

Inclure l'image dans un email de campagne :

```html
<!-- Email template -->
<table width="600">
    <tr>
        <td>
            <h1>Vendre votre maison en Tregor</h1>
            <img src="https://site.com/assets/images/social/social-estimation-2026-03-21.png"
                  alt="Estimation immobiliere"
                  width="600"
                  style="max-width: 100%; border-radius: 8px;">
            <p>Decouvrez comment vendre au meilleur prix...</p>
            <a href="https://site.com/estimation?utm_source=email&utm_campaign=vendre"
                class="btn">Obtenir mon estimation</a>
        </td>
    </tr>
</table>
```

**Via Brevo (Sendinblue) :**

```javascript
// Envoyer avec image dynamique
const template = {
    to: contact.email,
    templateId: 123,
    params: {
        FIRST_NAME: contact.name,
        IMAGE_URL: `https://site.com/assets/images/social/social-estimation-${generationId}.png`,
        CTA_URL: `https://site.com/estimation?ref=${contact.id}`
    }
};
```

---

## Integration Airtable

Automatiser la generation via Airtable :

```javascript
// Webhook Airtable -> n8n -> Generateur

const airtable = require('airtable');
const base = new airtable.base(process.env.AIRTABLE_API_KEY);

base('Campagnes')
    .select({
        filterByFormula: 'AND({Status} = "A generer", {Images} = BLANK())'
    })
    .eachPage((records, fetchNextPage) => {
        records.forEach(record => {
            generateImages({
                persona: record.fields['Persona'],
                consciousness: record.fields['Conscience'],
                locality: record.fields['Localite'],
                title: record.fields['Titre'],
                slug: record.fields['Slug'],
                cta: record.fields['CTA']
            }).then(images => {
                // Update Airtable
                record.update({
                    'Images Generated': new Date().toISOString(),
                    'Instagram URL': images.instagram,
                    'Facebook URL': images.facebook,
                    'LinkedIn URL': images.linkedin,
                    'GMB URL': images.gmb,
                    'Blog URL': images.blog,
                    'Status': 'Genere'
                });
            });
        });
        fetchNextPage();
    });
```

---

## Performance optimizations

### Lazy loading des images

```javascript
// Ne generer que si visible dans le viewport
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            generateFormat(entry.target.dataset.format);
            observer.unobserve(entry.target);
        }
    });
});

document.querySelectorAll('[data-format]').forEach(el => {
    observer.observe(el);
});
```

### Caching des personas

```javascript
// LocalStorage
localStorage.setItem('lastPersona', form.persona.value);
form.persona.value = localStorage.getItem('lastPersona') || '';
```

### Pre-generation batch

```javascript
async function batchGenerate(campaigns) {
    for (const campaign of campaigns) {
        await generateImages(campaign);
        // Delai pour ne pas surcharger
        await new Promise(resolve => setTimeout(resolve, 500));
    }
}
```

---

## KPI a tracker

1. **Taux d'utilisation** : nombre de generations par mois
2. **Format populaire** : quel template est le plus telecharge (estimation, conseil, stat, story, paysage)
3. **Type d'image** : IA (OpenAI) vs Social (HTML templates)
4. **CTR des images** : quelle image genere le plus de clics dans les campagnes
5. **Taux de conversion** : Generation -> Lead -> Client

---

## Templates disponibles dans le CMS

Le generateur social integre 5 templates HTML-to-PNG :

| Template | Dimensions | Usage |
|----------|-----------|-------|
| Estimation/Prix | 1080x1080 | Instagram, post carre |
| Conseil/Tips | 1080x1080 | Instagram, tips immobiliers |
| Chiffre Cle/Stat | 1080x1080 | Instagram, statistiques marche |
| Story (9:16) | 1080x1920 | Instagram/Facebook stories |
| Paysage (16:9) | 1200x628 | Facebook, LinkedIn, GMB |

Le generateur IA (OpenAI gpt-image-1) supporte :

| Taille | Usage |
|--------|-------|
| 1024x1024 | Post carre, profil |
| 1536x1024 | Paysage, banniere |
| 1024x1536 | Portrait, story |

Types de prompts SEO pre-configures : estimation, interieur, quartier, blog, cta.
