<div style="max-width: 960px;">

  <h2 style="margin-bottom: 0.5rem;"><i class="fas fa-bullhorn" style="color: var(--primary);"></i> Guide Google Ads & Pages de Destination</h2>
  <p class="muted" style="margin-bottom: 2.5rem;">Bonnes pratiques, tracking UTM, et configuration de vos campagnes Google Ads.</p>

  <!-- ═══════════════ SECTION 1 : PAGES DISPONIBLES ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-file-alt"></i> Pages de destination disponibles</h3>
    <p style="margin-bottom: 1rem; font-size: 0.9rem; color: var(--muted);">
      Chaque landing page est optimisée pour un groupe de mots-clés spécifique. Utilisez l'URL correspondante dans vos annonces Google Ads.
    </p>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Page</th>
          <th>URL</th>
          <th>Mots-clés ciblés</th>
          <th>Objectif</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Estimation Nantes</strong></td>
          <td><code>/lp/estimation-nantes</code></td>
          <td>estimation immobilière nantes, estimer bien nantes, prix immobilier nantes</td>
          <td>Capture lead estimation</td>
        </tr>
        <tr>
          <td><strong>Vendre Maison</strong></td>
          <td><code>/lp/vendre-maison-nantes</code></td>
          <td>vendre maison nantes, vente maison nantes, mettre en vente maison nantes</td>
          <td>Capture lead vendeur maison</td>
        </tr>
        <tr>
          <td><strong>Avis de Valeur</strong></td>
          <td><code>/lp/avis-valeur-gratuit</code></td>
          <td>avis de valeur gratuit, avis de valeur nantes, estimation gratuite nantes</td>
          <td>Capture lead avis valeur</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-info">
      <strong><i class="fas fa-info-circle"></i> Note :</strong>
      Ces pages n'ont pas de menu de navigation pour éviter les distractions. Elles sont en <code>noindex, nofollow</code>
      pour ne pas interférer avec votre SEO. Seule la conversion compte.
    </div>
  </div>

  <!-- ═══════════════ SECTION 2 : UTM TRACKING ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-chart-pie"></i> Paramètres UTM & Tracking</h3>
    <p style="margin-bottom: 1rem; font-size: 0.9rem; color: var(--muted);">
      Les paramètres UTM permettent de tracer l'origine exacte de chaque lead dans votre CRM.
      Ils sont automatiquement capturés et sauvegardés dans les notes du lead.
    </p>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Paramètre</th>
          <th>Description</th>
          <th>Exemple</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><code>utm_source</code></td>
          <td>D'où vient le trafic (plateforme publicitaire)</td>
          <td><code>google</code></td>
        </tr>
        <tr>
          <td><code>utm_medium</code></td>
          <td>Type de canal / média</td>
          <td><code>cpc</code> (coût par clic)</td>
        </tr>
        <tr>
          <td><code>utm_campaign</code></td>
          <td>Nom de la campagne Google Ads</td>
          <td><code>estimation-nantes-2024</code></td>
        </tr>
        <tr>
          <td><code>utm_term</code></td>
          <td>Mot-clé qui a déclenché l'annonce</td>
          <td><code>estimation+immobiliere+nantes</code></td>
        </tr>
        <tr>
          <td><code>utm_content</code></td>
          <td>Variante de l'annonce (pour tests A/B)</td>
          <td><code>annonce-variante-a</code></td>
        </tr>
        <tr>
          <td><code>gclid</code></td>
          <td>Google Click ID (auto-tagging Google Ads)</td>
          <td><em>Automatique si activé</em></td>
        </tr>
      </tbody>
    </table>

    <h4 style="margin: 1.5rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">Exemple d'URL complète avec UTM :</h4>
    <div class="guide-code">
<span class="string">https://estimation-immobilier-nantes.fr/lp/estimation-nantes</span>
  <span class="tag">?utm_source=</span><span class="string">google</span>
  <span class="tag">&amp;utm_medium=</span><span class="string">cpc</span>
  <span class="tag">&amp;utm_campaign=</span><span class="string">estimation-nantes-2024</span>
  <span class="tag">&amp;utm_term=</span><span class="string">estimation+immobiliere+nantes</span>
  <span class="tag">&amp;utm_content=</span><span class="string">annonce-variante-a</span>
    </div>

    <div class="guide-tip guide-tip-success">
      <strong><i class="fas fa-check-circle"></i> Auto-capture :</strong>
      Les UTM sont automatiquement capturés à l'arrivée sur la page et sauvegardés en session.
      Ils sont inclus dans les notes de chaque lead créé depuis une landing page Google Ads.
      Vous pouvez les voir dans le détail de chaque lead dans le CRM (onglet Leads).
    </div>
  </div>

  <!-- ═══════════════ SECTION 3 : CONFIG GOOGLE ADS ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-cog"></i> Configuration dans Google Ads</h3>

    <h4 style="margin: 1rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">1. Activer l'auto-tagging (gclid)</h4>
    <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 1rem;">
      Dans Google Ads &rarr; Paramètres du compte &rarr; Cochez <strong>"Marquage automatique"</strong>.
      Cela ajoute automatiquement le paramètre <code>gclid</code> à chaque clic, ce qui permet le suivi des conversions.
    </p>

    <h4 style="margin: 1rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">2. Configurer les modèles de suivi (Tracking Template)</h4>
    <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 0.5rem;">
      Au niveau de la campagne ou du groupe d'annonces, configurez le modèle de suivi :
    </p>
    <div class="guide-code">
<span class="comment">-- Modèle de suivi (Tracking Template) --</span>
<span class="string">{lpurl}?utm_source=google&amp;utm_medium=cpc&amp;utm_campaign={campaignid}&amp;utm_term={keyword}&amp;utm_content={creative}</span>
    </div>

    <h4 style="margin: 1rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">3. URL finale dans l'annonce</h4>
    <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 0.5rem;">
      Dans le champ "URL finale" de chaque annonce, utilisez l'URL de la landing page correspondante :
    </p>
    <div class="guide-code">
<span class="comment">-- Campagne "Estimation Nantes" --</span>
<span class="string">https://estimation-immobilier-nantes.fr/lp/estimation-nantes</span>

<span class="comment">-- Campagne "Vendre Maison" --</span>
<span class="string">https://estimation-immobilier-nantes.fr/lp/vendre-maison-nantes</span>

<span class="comment">-- Campagne "Avis de Valeur" --</span>
<span class="string">https://estimation-immobilier-nantes.fr/lp/avis-valeur-gratuit</span>
    </div>

    <h4 style="margin: 1rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">4. Configurer le suivi de conversion</h4>
    <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 1rem;">
      Créez une action de conversion dans Google Ads &rarr; Outils &rarr; Conversions :
    </p>
    <ul class="guide-checklist">
      <li><i class="fas fa-check-circle"></i> <strong>Catégorie :</strong> Demande de formulaire (Lead)</li>
      <li><i class="fas fa-check-circle"></i> <strong>Source :</strong> Site Web</li>
      <li><i class="fas fa-check-circle"></i> <strong>Méthode :</strong> Balise Google (gtag.js) ou Google Tag Manager</li>
      <li><i class="fas fa-check-circle"></i> <strong>Page de conversion :</strong> La page "Merci" (après soumission du formulaire)</li>
      <li><i class="fas fa-check-circle"></i> <strong>Modèle d'attribution :</strong> Basé sur les données (recommandé par Google)</li>
    </ul>

    <div class="guide-tip guide-tip-warning">
      <strong><i class="fas fa-exclamation-triangle"></i> Important :</strong>
      Dans le fichier <code>app/views/landing/layout.php</code>, décommentez les balises Google Ads en haut du &lt;head&gt;
      et remplacez <code>AW-XXXXXXXXX</code> par votre ID de conversion Google Ads.
      Sur la page Merci (<code>app/views/landing/pages/merci.php</code>), activez l'événement de conversion.
    </div>
  </div>

  <!-- ═══════════════ SECTION 4 : PIXEL GOOGLE ADS ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-code"></i> Installation du pixel Google Ads</h3>

    <h4 style="margin: 1rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">Option A : Google Tag (gtag.js) direct</h4>
    <div class="guide-code">
<span class="comment">&lt;!-- Dans le &lt;head&gt; de layout.php --&gt;</span>
<span class="tag">&lt;script</span> async src="https://www.googletagmanager.com/gtag/js?id=<span class="string">AW-VOTRE-ID</span>"<span class="tag">&gt;&lt;/script&gt;</span>
<span class="tag">&lt;script&gt;</span>
  window.dataLayer = window.dataLayer || [];
  <span class="keyword">function</span> gtag(){dataLayer.push(arguments);}
  gtag(<span class="string">'js'</span>, <span class="keyword">new</span> Date());
  gtag(<span class="string">'config'</span>, <span class="string">'AW-VOTRE-ID'</span>);
<span class="tag">&lt;/script&gt;</span>

<span class="comment">&lt;!-- Sur la page Merci (événement de conversion) --&gt;</span>
<span class="tag">&lt;script&gt;</span>
  gtag(<span class="string">'event'</span>, <span class="string">'conversion'</span>, {
    <span class="string">'send_to'</span>: <span class="string">'AW-VOTRE-ID/VOTRE-LABEL'</span>,
    <span class="string">'value'</span>: 1.0,
    <span class="string">'currency'</span>: <span class="string">'EUR'</span>
  });
<span class="tag">&lt;/script&gt;</span>
    </div>

    <h4 style="margin: 1rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">Option B : Google Tag Manager (recommandé)</h4>
    <div class="guide-code">
<span class="comment">&lt;!-- Dans le &lt;head&gt; --&gt;</span>
<span class="tag">&lt;script&gt;</span>
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({<span class="string">'gtm.start'</span>:
  <span class="keyword">new</span> Date().getTime(),event:<span class="string">'gtm.js'</span>});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!=<span class="string">'dataLayer'</span>?<span class="string">'&amp;l='</span>+l:<span class="string">''</span>;j.async=true;
  j.src=<span class="string">'https://www.googletagmanager.com/gtm.js?id='</span>+i+dl;
  f.parentNode.insertBefore(j,f);})(window,document,<span class="string">'script'</span>,<span class="string">'dataLayer'</span>,<span class="string">'GTM-VOTRE-ID'</span>);
<span class="tag">&lt;/script&gt;</span>

<span class="comment">&lt;!-- L'événement dataLayer.push est déjà en place sur la page Merci --&gt;</span>
<span class="comment">&lt;!-- Configurez un déclencheur GTM sur l'événement 'lead_form_submit' --&gt;</span>
    </div>
  </div>

  <!-- ═══════════════ SECTION 5 : BONNES PRATIQUES ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-star"></i> Bonnes pratiques Quality Score Google Ads</h3>

    <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 1.5rem;">
      Le <strong>Quality Score</strong> (note de 1 à 10) détermine le coût et la position de vos annonces.
      Un score de <strong>7+</strong> réduit votre CPC de 20-30%. Voici les 3 composantes et comment les optimiser :
    </p>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Composante</th>
          <th>Poids</th>
          <th>Comment optimiser</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Taux de clic attendu (CTR)</strong></td>
          <td>~40%</td>
          <td>Titre d'annonce accrocheur, extensions d'annonce, correspondance mot-clé</td>
          <td>Viser "Au-dessus de la moyenne"</td>
        </tr>
        <tr>
          <td><strong>Pertinence de l'annonce</strong></td>
          <td>~25%</td>
          <td>Le mot-clé doit apparaître dans le titre ET la description de l'annonce</td>
          <td>Viser "Au-dessus de la moyenne"</td>
        </tr>
        <tr>
          <td><strong>Expérience page de destination</strong></td>
          <td>~35%</td>
          <td>Vitesse, mobile-first, cohérence avec l'annonce, contenu pertinent</td>
          <td>Viser "Au-dessus de la moyenne"</td>
        </tr>
      </tbody>
    </table>

    <h4 style="margin: 1.5rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">Checklist d'optimisation :</h4>

    <ul class="guide-checklist">
      <li><i class="fas fa-check-circle"></i> <strong>Cohérence mot-clé &rarr; annonce &rarr; page :</strong> Le titre H1 de la landing page reprend le mot-clé de l'annonce</li>
      <li><i class="fas fa-check-circle"></i> <strong>Vitesse de chargement &lt; 3 secondes :</strong> Images compressées, CSS/JS minifiés, pas de scripts lourds</li>
      <li><i class="fas fa-check-circle"></i> <strong>Mobile-first :</strong> 63% du trafic est mobile. Les pages sont responsive par défaut</li>
      <li><i class="fas fa-check-circle"></i> <strong>Pas de menu de navigation :</strong> Pas de distractions, un seul objectif = le formulaire</li>
      <li><i class="fas fa-check-circle"></i> <strong>CTA visible au-dessus de la ligne de flottaison :</strong> Le formulaire est visible sans scroller</li>
      <li><i class="fas fa-check-circle"></i> <strong>Preuve sociale :</strong> Témoignages, chiffres, étoiles pour rassurer le visiteur</li>
      <li><i class="fas fa-check-circle"></i> <strong>FAQ :</strong> Répond aux objections courantes et réduit l'anxiété</li>
      <li><i class="fas fa-check-circle"></i> <strong>Transparence :</strong> Mentions légales, politique de confidentialité, RGPD accessibles</li>
      <li><i class="fas fa-check-circle"></i> <strong>Formulaire court :</strong> Nom, email, téléphone uniquement (3 champs obligatoires)</li>
      <li><i class="fas fa-check-circle"></i> <strong>Tracking activé :</strong> UTM + gclid + pixel de conversion sur la page Merci</li>
    </ul>
  </div>

  <!-- ═══════════════ SECTION 6 : STRUCTURE ANNONCE ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-ad"></i> Structure recommandée des annonces</h3>

    <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 1rem;">
      Pour chaque page de destination, voici des exemples d'annonces Google Ads optimisées :
    </p>

    <h4 style="margin: 1.5rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">
      Campagne 1 : Estimation Immobilière Nantes
    </h4>
    <div class="guide-code">
<span class="keyword">Titre 1 :</span> <span class="string">Estimation Immobilière Nantes</span>
<span class="keyword">Titre 2 :</span> <span class="string">Gratuite en 60 Secondes</span>
<span class="keyword">Titre 3 :</span> <span class="string">Résultat Immédiat</span>

<span class="keyword">Description 1 :</span> <span class="string">Obtenez une estimation gratuite de votre bien à Nantes. Données du marché réel, résultat en 60 secondes. Sans engagement.</span>
<span class="keyword">Description 2 :</span> <span class="string">Plus de 2 400 estimations réalisées à Nantes. Note 4.8/5. Un expert vous rappelle sous 24h.</span>

<span class="keyword">URL finale :</span> <span class="string">https://estimation-immobilier-nantes.fr/lp/estimation-nantes</span>
    </div>

    <h4 style="margin: 1.5rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">
      Campagne 2 : Vendre Maison Nantes
    </h4>
    <div class="guide-code">
<span class="keyword">Titre 1 :</span> <span class="string">Vendez Votre Maison à Nantes</span>
<span class="keyword">Titre 2 :</span> <span class="string">Estimation Gratuite du Prix</span>
<span class="keyword">Titre 3 :</span> <span class="string">Accompagnement Expert</span>

<span class="keyword">Description 1 :</span> <span class="string">Vendez votre maison au meilleur prix. Estimation gratuite basée sur le marché de nantes actuel. Expert local.</span>
<span class="keyword">Description 2 :</span> <span class="string">Fixez le bon prix dès le départ. Rappel expert sous 24h. Service 100% gratuit, sans engagement.</span>

<span class="keyword">URL finale :</span> <span class="string">https://estimation-immobilier-nantes.fr/lp/vendre-maison-nantes</span>
    </div>

    <h4 style="margin: 1.5rem 0 0.8rem; font-size: 0.95rem; font-weight: 700;">
      Campagne 3 : Avis de Valeur Gratuit
    </h4>
    <div class="guide-code">
<span class="keyword">Titre 1 :</span> <span class="string">Avis de Valeur Gratuit Nantes</span>
<span class="keyword">Titre 2 :</span> <span class="string">Sans Engagement</span>
<span class="keyword">Titre 3 :</span> <span class="string">Expert Immobilier Local</span>

<span class="keyword">Description 1 :</span> <span class="string">Recevez un avis de valeur gratuit pour votre bien à Nantes. Analyse experte basée sur le marché actuel.</span>
<span class="keyword">Description 2 :</span> <span class="string">Idéal pour vente, succession ou divorce. Avis professionnel affiné par un expert. Résultat sous 24h.</span>

<span class="keyword">URL finale :</span> <span class="string">https://estimation-immobilier-nantes.fr/lp/avis-valeur-gratuit</span>
    </div>
  </div>

  <!-- ═══════════════ SECTION 7 : MOTS-CLÉS ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-search"></i> Suggestions de mots-clés par campagne</h3>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Campagne</th>
          <th>Mots-clés (Exact / Expression)</th>
          <th>Mots-clés négatifs</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Estimation Nantes</strong></td>
          <td>
            [estimation immobilière nantes]<br>
            [estimer mon bien nantes]<br>
            "estimation maison nantes"<br>
            "prix immobilier nantes"<br>
            [estimation appartement nantes]<br>
            "combien vaut ma maison nantes"
          </td>
          <td>location, louer, agent, recrutement, formation, emploi</td>
        </tr>
        <tr>
          <td><strong>Vendre Maison</strong></td>
          <td>
            [vendre maison nantes]<br>
            "vendre sa maison nantes"<br>
            [vente maison nantes]<br>
            "mettre en vente maison nantes"<br>
            "prix vente maison nantes"
          </td>
          <td>acheter, location, louer, construire, terrain, neuf</td>
        </tr>
        <tr>
          <td><strong>Avis de Valeur</strong></td>
          <td>
            [avis de valeur nantes]<br>
            [avis de valeur gratuit]<br>
            "avis de valeur immobilier nantes"<br>
            "estimation gratuite nantes"<br>
            [estimation bien immobilier gratuit nantes]
          </td>
          <td>location, louer, acheter, notaire (si non pertinent)</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-info">
      <strong><i class="fas fa-lightbulb"></i> Conseil :</strong>
      Commencez avec des mots-clés en correspondance exacte <code>[mot-clé]</code> et expression <code>"mot-clé"</code>
      pour maîtriser vos coûts. Élargissez progressivement une fois que vous avez des données de conversion.
    </div>
  </div>

  <!-- ═══════════════ SECTION 8 : BUDGET & ENCHÈRES ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-euro-sign"></i> Budget & Stratégie d'enchères recommandés</h3>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Phase</th>
          <th>Budget / jour</th>
          <th>Stratégie enchères</th>
          <th>Durée</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Phase 1 : Test</strong></td>
          <td>10-20 &euro; / jour</td>
          <td>Maximiser les clics (pour collecter des données)</td>
          <td>2-4 semaines</td>
        </tr>
        <tr>
          <td><strong>Phase 2 : Optimisation</strong></td>
          <td>20-50 &euro; / jour</td>
          <td>Maximiser les conversions (une fois 15+ conversions atteintes)</td>
          <td>4-8 semaines</td>
        </tr>
        <tr>
          <td><strong>Phase 3 : Scale</strong></td>
          <td>50+ &euro; / jour</td>
          <td>CPA cible (basé sur votre coût par lead idéal)</td>
          <td>Continu</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-warning">
      <strong><i class="fas fa-exclamation-triangle"></i> Attention :</strong>
      Ne passez en stratégie "Maximiser les conversions" qu'après avoir collecté au moins 15 conversions
      en 30 jours. Sinon l'algorithme Google n'a pas assez de données pour optimiser efficacement.
    </div>
  </div>

  <!-- ═══════════════ SECTION 9 : À NE PAS FAIRE ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-ban"></i> Erreurs à éviter</h3>

    <ul class="guide-checklist">
      <li><i class="fas fa-times-circle"></i> <strong>Envoyer vers la page d'accueil :</strong> Toujours utiliser une landing page dédiée, jamais la homepage</li>
      <li><i class="fas fa-times-circle"></i> <strong>Mots-clés en requête large :</strong> En phase de test, évitez la correspondance large qui gaspille le budget</li>
      <li><i class="fas fa-times-circle"></i> <strong>Pas de suivi de conversion :</strong> Sans tracking, impossible d'optimiser. Configurez le pixel AVANT de lancer</li>
      <li><i class="fas fa-times-circle"></i> <strong>Page lente :</strong> Si la page met plus de 3 secondes à charger, le Quality Score chute</li>
      <li><i class="fas fa-times-circle"></i> <strong>Titre d'annonce ≠ titre de page :</strong> L'incohérence fait baisser la pertinence et augmente le taux de rebond</li>
      <li><i class="fas fa-times-circle"></i> <strong>Formulaire trop long :</strong> Plus de 4-5 champs = chute des conversions</li>
      <li><i class="fas fa-times-circle"></i> <strong>Pas de test A/B :</strong> Créez au moins 2-3 variantes d'annonce par groupe pour laisser Google optimiser</li>
      <li><i class="fas fa-times-circle"></i> <strong>Ignorer les mots-clés négatifs :</strong> Ajoutez-les régulièrement pour bloquer le trafic non pertinent</li>
    </ul>
  </div>

  <!-- ═══════════════ SECTION 10 : MESURER LES RÉSULTATS ═══════════════ -->
  <div class="guide-section">
    <h3><i class="fas fa-tachometer-alt"></i> KPIs à suivre</h3>

    <table class="guide-table">
      <thead>
        <tr>
          <th>KPI</th>
          <th>Objectif</th>
          <th>Où le voir</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Quality Score</strong></td>
          <td>7+ / 10</td>
          <td>Google Ads &rarr; Mots-clés &rarr; Colonnes &rarr; Niveau de qualité</td>
        </tr>
        <tr>
          <td><strong>CTR (taux de clic)</strong></td>
          <td>> 3-5%</td>
          <td>Google Ads &rarr; Campagnes</td>
        </tr>
        <tr>
          <td><strong>Taux de conversion</strong></td>
          <td>> 5-10%</td>
          <td>Google Ads &rarr; Conversions (nécessite le pixel)</td>
        </tr>
        <tr>
          <td><strong>Coût par lead (CPA)</strong></td>
          <td>&lt; 20-40 &euro;</td>
          <td>Google Ads &rarr; Campagnes &rarr; Coût/conversion</td>
        </tr>
        <tr>
          <td><strong>ROAS</strong></td>
          <td>Positif</td>
          <td>Leads CRM &rarr; Valeur des mandats signés vs dépense Ads</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-success">
      <strong><i class="fas fa-check-circle"></i> Rappel :</strong>
      Tous les leads issus des landing pages Google Ads apparaissent dans votre CRM
      (<a href="/admin/leads">Admin &rarr; Leads</a>) avec les détails UTM dans les notes.
      Filtrez par source pour mesurer le ROI de chaque campagne.
    </div>
  </div>

</div>
