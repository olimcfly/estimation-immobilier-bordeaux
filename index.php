<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EstimIA - <?= htmlspecialchars((string) CITY_NAME, ENT_QUOTES) ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; }
        h1 { margin-bottom: 0.5rem; }
        .subtitle { color: #666; margin-top: 0; }
        .field { margin: 1rem 0; }
        input, select { width: 100%; padding: 0.7rem; font-size: 1rem; }
        .warning-zone {
            display: none;
            margin-top: 0.5rem;
            background: #fff8db;
            border: 1px solid #f0d97a;
            color: #6f5a12;
            border-radius: 6px;
            padding: 0.75rem;
            font-size: 0.95rem;
        }
        button { padding: 0.8rem 1rem; font-size: 1rem; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Estimez votre bien immobilier à <?= htmlspecialchars((string) CITY_NAME, ENT_QUOTES) ?></h1>
    <p class="subtitle">Et dans un rayon de <?= (float) CITY_RADIUS_KM ?> km autour</p>

    <form method="post" action="resultat.php">
        <div class="field">
            <label for="adresse">Adresse</label>
            <input id="adresse" name="adresse" type="text" required autocomplete="off">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <div id="zoneWarning" class="warning-zone"></div>
        </div>

        <div class="field">
            <label for="type_bien">Type de bien</label>
            <select id="type_bien" name="type_bien">
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
            </select>
        </div>

        <button type="submit">Lancer l'estimation</button>
    </form>

    <script>
    const centerLat = <?= (float) CITY_LAT ?>;
    const centerLng = <?= (float) CITY_LNG ?>;
    const radiusKm = <?= (float) CITY_RADIUS_KM ?>;

    let autocomplete;

    function haversineDistance(lat1, lng1, lat2, lng2) {
      const R = 6371;
      const toRad = (deg) => deg * (Math.PI / 180);
      const dLat = toRad(lat2 - lat1);
      const dLng = toRad(lng2 - lng1);
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
        + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2))
        * Math.sin(dLng / 2) * Math.sin(dLng / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }

    function initAutocomplete() {
      const input = document.getElementById('adresse');

      // Biais de recherche Google vers la zone
      const defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(centerLat - (radiusKm / 111), centerLng - (radiusKm / 85)),
        new google.maps.LatLng(centerLat + (radiusKm / 111), centerLng + (radiusKm / 85))
      );

      autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['address'],
        componentRestrictions: { country: 'fr' },
        bounds: defaultBounds,
        strictBounds: false, // Permettre hors zone mais prioriser la zone
        fields: ['address_components', 'geometry', 'formatted_address', 'place_id']
      });

      autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        const warningBox = document.getElementById('zoneWarning');
        warningBox.style.display = 'none';
        warningBox.textContent = '';

        if (!place.geometry || !place.geometry.location) {
          return;
        }

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        const distance = haversineDistance(centerLat, centerLng, lat, lng);
        if (distance > radiusKm) {
          warningBox.textContent = `⚠️ Cette adresse est à ${distance.toFixed(1)} km de notre zone d'expertise. L'estimation sera moins précise.`;
          warningBox.style.display = 'block';
        }
      });
    }

    window.initAutocomplete = initAutocomplete;
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY&libraries=places&callback=initAutocomplete"></script>
</body>
</html>
