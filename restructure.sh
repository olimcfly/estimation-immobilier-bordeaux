#!/usr/bin/env bash
set -euo pipefail

mkdir -p app/views/layouts app/views/pages app/controllers routes public/assets/css

create_file_if_missing() {
  local file="$1"
  local content="$2"

  if [[ ! -f "$file" ]]; then
    printf "%s\n" "$content" > "$file"
    echo "Créé: $file"
  else
    echo "Déjà présent: $file"
  fi
}

create_file_if_missing "app/views/layouts/header.php" '<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? "Estimation Immobilier Bordeaux", ENT_QUOTES, "UTF-8") ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main>'

create_file_if_missing "app/views/layouts/footer.php" '</main>
</body>
</html>'

create_file_if_missing "app/views/pages/home.php" '<section class="section"><div class="container"><h1>Accueil</h1></div></section>'
create_file_if_missing "app/views/pages/about.php" '<section class="section"><div class="container"><h1>À propos</h1></div></section>'
create_file_if_missing "app/views/pages/services.php" '<section class="section"><div class="container"><h1>Services</h1></div></section>'
create_file_if_missing "app/views/pages/contact.php" '<section class="section"><div class="container"><h1>Contact</h1></div></section>'

create_file_if_missing "app/controllers/PageController.php" '<?php

class PageController {}
'

create_file_if_missing "routes/web.php" "<?php\n"

if [[ -f "app-css-complete-final.css" ]]; then
  cp app-css-complete-final.css public/assets/css/app.css
  echo "CSS copié vers public/assets/css/app.css"
else
  echo "WARN: app-css-complete-final.css absent, copie CSS ignorée"
fi

echo "Restructuration terminée."
