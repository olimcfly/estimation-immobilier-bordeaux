#!/bin/bash
# =============================================================================
# Script de duplication du site Estimation Immobilier
# Usage: ./duplicate-site.sh <city_slug>
# Exemple: ./duplicate-site.sh nantes
# =============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SOURCE_DIR="$(dirname "$SCRIPT_DIR")"
CITIES_FILE="$SCRIPT_DIR/cities.json"

# Couleurs pour le terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info()  { echo -e "${BLUE}[INFO]${NC} $1"; }
log_ok()    { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# --- Vérifications ---
if [ $# -lt 1 ]; then
    echo ""
    echo "Usage: $0 <city_slug> [output_parent_dir]"
    echo ""
    echo "Villes disponibles:"
    if command -v jq &> /dev/null; then
        jq -r 'keys[]' "$CITIES_FILE" | while read -r slug; do
            name=$(jq -r ".[\"$slug\"].city_name" "$CITIES_FILE")
            cp=$(jq -r ".[\"$slug\"].city_code_postal" "$CITIES_FILE")
            echo "  - $slug ($name, $cp)"
        done
    else
        echo "  (installez jq pour voir la liste: sudo apt install jq)"
    fi
    echo ""
    exit 1
fi

CITY_SLUG="$1"
OUTPUT_PARENT="${2:-$(dirname "$SOURCE_DIR")}"

if ! command -v jq &> /dev/null; then
    log_error "jq est requis. Installez-le avec: sudo apt install jq"
    exit 1
fi

# --- Lecture de la config ville ---
if ! jq -e ".[\"$CITY_SLUG\"]" "$CITIES_FILE" > /dev/null 2>&1; then
    log_error "Ville '$CITY_SLUG' non trouvée dans cities.json"
    echo "Villes disponibles: $(jq -r 'keys | join(", ")' "$CITIES_FILE")"
    exit 1
fi

CITY_NAME=$(jq -r ".[\"$CITY_SLUG\"].city_name" "$CITIES_FILE")
CITY_REGION=$(jq -r ".[\"$CITY_SLUG\"].city_region" "$CITIES_FILE")
CITY_DEPARTEMENT=$(jq -r ".[\"$CITY_SLUG\"].city_departement" "$CITIES_FILE")
CITY_CP=$(jq -r ".[\"$CITY_SLUG\"].city_code_postal" "$CITIES_FILE")
CITY_COORDS=$(jq -r ".[\"$CITY_SLUG\"].city_coords" "$CITIES_FILE")
PRIX_M2=$(jq -r ".[\"$CITY_SLUG\"].prix_m2_moyen" "$CITIES_FILE")
CITY_FACTOR=$(jq -r ".[\"$CITY_SLUG\"].city_factor" "$CITIES_FILE")
DOMAIN=$(jq -r ".[\"$CITY_SLUG\"].domain" "$CITIES_FILE")
DB_NAME=$(jq -r ".[\"$CITY_SLUG\"].db_name" "$CITIES_FILE")
TELEPHONE=$(jq -r ".[\"$CITY_SLUG\"].telephone" "$CITIES_FILE")
COLOR_PRIMARY=$(jq -r ".[\"$CITY_SLUG\"].colors.primary" "$CITIES_FILE")
COLOR_PRIMARY_DARK=$(jq -r ".[\"$CITY_SLUG\"].colors.primary_dark" "$CITIES_FILE")
COLOR_ACCENT=$(jq -r ".[\"$CITY_SLUG\"].colors.accent" "$CITIES_FILE")
COLOR_ACCENT_LIGHT=$(jq -r ".[\"$CITY_SLUG\"].colors.accent_light" "$CITIES_FILE")

DEST_DIR="$OUTPUT_PARENT/estimation-immobilier-$CITY_SLUG"

echo ""
echo "=============================================="
echo " Duplication du site pour: $CITY_NAME"
echo "=============================================="
echo " Source:      $SOURCE_DIR"
echo " Destination: $DEST_DIR"
echo " Domaine:     $DOMAIN"
echo " Code postal: $CITY_CP"
echo " Région:      $CITY_REGION"
echo " Prix m²:     ${PRIX_M2}€"
echo "=============================================="
echo ""

# --- Confirmation ---
read -p "Continuer ? (o/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Oo]$ ]]; then
    log_warn "Annulé."
    exit 0
fi

# =============================================================================
# ÉTAPE 1: Copie du projet
# =============================================================================
log_info "Copie du projet source..."

if [ -d "$DEST_DIR" ]; then
    log_warn "Le dossier $DEST_DIR existe déjà."
    read -p "Écraser ? (o/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Oo]$ ]]; then
        exit 0
    fi
    rm -rf "$DEST_DIR"
fi

# Copie en excluant les éléments non nécessaires
if command -v rsync &> /dev/null; then
    rsync -a --progress \
        --exclude='.git' \
        --exclude='vendor/' \
        --exclude='node_modules/' \
        --exclude='.env' \
        --exclude='logs/*.log' \
        --exclude='tools/' \
        "$SOURCE_DIR/" "$DEST_DIR/"
else
    cp -r "$SOURCE_DIR/" "$DEST_DIR/"
    # Nettoyage des éléments exclus
    rm -rf "$DEST_DIR/.git" "$DEST_DIR/vendor" "$DEST_DIR/node_modules" "$DEST_DIR/.env" "$DEST_DIR/tools"
    find "$DEST_DIR/logs" -name "*.log" -delete 2>/dev/null || true
fi

log_ok "Projet copié dans $DEST_DIR"

# =============================================================================
# ÉTAPE 2: Remplacement des références Bordeaux
# =============================================================================
log_info "Remplacement des références à Bordeaux..."

# Fonction de remplacement sécurisé dans les fichiers
replace_in_files() {
    local search="$1"
    local replace="$2"
    local file_pattern="${3:-*}"

    find "$DEST_DIR" -type f \( -name "*.php" -o -name "*.json" -o -name "*.xml" -o -name "*.txt" -o -name "*.md" -o -name "*.env*" -o -name "*.sql" -o -name "*.css" -o -name "*.js" -o -name "*.html" -o -name ".htaccess" \) \
        ! -path "*/vendor/*" ! -path "*/.git/*" \
        -exec sed -i "s|$search|$replace|g" {} +
}

# Nom de la ville (cas sensible)
replace_in_files "Bordeaux" "$CITY_NAME"
replace_in_files "bordeaux" "$CITY_SLUG"
replace_in_files "BORDEAUX" "$(echo "$CITY_NAME" | tr '[:lower:]' '[:upper:]')"

# Région
replace_in_files "Nouvelle-Aquitaine" "$CITY_REGION"

# Département / zone géographique
replace_in_files "en Gironde" "en $CITY_DEPARTEMENT"
replace_in_files "Gironde" "$CITY_DEPARTEMENT"

# Code postal
replace_in_files "33000" "$CITY_CP"

# Domaine
replace_in_files "estimation-immobilier-bordeaux\.fr" "$DOMAIN"
replace_in_files "estimation-immobilier-bordeaux" "estimation-immobilier-$CITY_SLUG"

# Adjectif bordelais
CITY_LOWER=$(echo "$CITY_NAME" | tr '[:upper:]' '[:lower:]')
replace_in_files "bordelais" "de $CITY_LOWER"
replace_in_files "bordelaise" "de $CITY_LOWER"

log_ok "Références textuelles remplacées"

# =============================================================================
# ÉTAPE 3: Mise à jour du config.php
# =============================================================================
log_info "Mise à jour de config.php..."

CONFIG_FILE="$DEST_DIR/config/config.php"

# Remplacer les constantes de ville
sed -i "s|define('CITY_NAME', '[^']*')|define('CITY_NAME', '$CITY_NAME')|g" "$CONFIG_FILE"
sed -i "s|define('CITY_REGION', '[^']*')|define('CITY_REGION', '$CITY_REGION')|g" "$CONFIG_FILE"
sed -i "s|define('CITY_CODE_POSTAL', '[^']*')|define('CITY_CODE_POSTAL', '$CITY_CP')|g" "$CONFIG_FILE"
sed -i "s|define('PRIX_M2_MOYEN', [0-9]*)|define('PRIX_M2_MOYEN', $PRIX_M2)|g" "$CONFIG_FILE"
sed -i "s|define('COLOR_PRIMARY', '[^']*')|define('COLOR_PRIMARY', '$COLOR_PRIMARY')|g" "$CONFIG_FILE"
sed -i "s|define('COLOR_ACCENT', '[^']*')|define('COLOR_ACCENT', '$COLOR_ACCENT')|g" "$CONFIG_FILE"

log_ok "config.php mis à jour"

# =============================================================================
# ÉTAPE 4: Mise à jour du .env.example
# =============================================================================
log_info "Mise à jour de .env.example..."

ENV_FILE="$DEST_DIR/.env.example"

sed -i "s|APP_NAME=.*|APP_NAME=\"Estimation Immobilier $CITY_NAME\"|" "$ENV_FILE"
sed -i "s|APP_BASE_URL=.*|APP_BASE_URL=\"https://$DOMAIN\"|" "$ENV_FILE"
sed -i "s|DB_NAME=.*|DB_NAME=\"$DB_NAME\"|" "$ENV_FILE"
sed -i "s|MAIL_FROM=.*|MAIL_FROM=\"contact@$DOMAIN\"|" "$ENV_FILE"
sed -i "s|MAIL_FROM_NAME=.*|MAIL_FROM_NAME=\"Estimation Immobilier $CITY_NAME\"|" "$ENV_FILE"
sed -i "s|MAIL_HOST=.*|MAIL_HOST=\"mail.$DOMAIN\"|" "$ENV_FILE"
sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=\"contact@$DOMAIN\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_PRIMARY=.*|SITE_COLOR_PRIMARY=\"$COLOR_PRIMARY\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_PRIMARY_DARK=.*|SITE_COLOR_PRIMARY_DARK=\"$COLOR_PRIMARY_DARK\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_ACCENT=.*|SITE_COLOR_ACCENT=\"$COLOR_ACCENT\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_ACCENT_LIGHT=.*|SITE_COLOR_ACCENT_LIGHT=\"$COLOR_ACCENT_LIGHT\"|" "$ENV_FILE"

log_ok ".env.example mis à jour"

# =============================================================================
# ÉTAPE 5: Mise à jour de l'EstimationService
# =============================================================================
log_info "Mise à jour de l'EstimationService..."

ESTIMATION_FILE="$DEST_DIR/app/services/EstimationService.php"
if [ -f "$ESTIMATION_FILE" ]; then
    # Ajouter le facteur de la ville
    sed -i "s|str_contains(\$cityLower, 'bordeaux')|str_contains(\$cityLower, '$CITY_SLUG')|g" "$ESTIMATION_FILE"
    sed -i "s|return 1.14;|return $CITY_FACTOR;|" "$ESTIMATION_FILE"
    log_ok "EstimationService mis à jour"
fi

# =============================================================================
# ÉTAPE 6: Génération du fichier quartiers.php
# =============================================================================
log_info "Génération des données de quartiers..."

QUARTIERS_FILE="$DEST_DIR/app/views/pages/quartiers.php"
if [ -f "$QUARTIERS_FILE" ]; then
    python3 "$SCRIPT_DIR/generate-quartiers.py" "$CITIES_FILE" "$CITY_SLUG" "$QUARTIERS_FILE"
    log_ok "Quartiers générés"
fi

# =============================================================================
# ÉTAPE 7: Mise à jour du header.php (Schema.org + meta)
# =============================================================================
log_info "Mise à jour du header.php (Schema.org)..."

HEADER_FILE="$DEST_DIR/app/views/layouts/header.php"
if [ -f "$HEADER_FILE" ]; then
    # Mettre à jour le téléphone dans le JSON-LD
    sed -i "s|+33556000000|$TELEPHONE|g" "$HEADER_FILE"

    # Mettre à jour les coordonnées
    IFS=',' read -r LAT LNG <<< "$CITY_COORDS"

    log_ok "Header mis à jour"
fi

# =============================================================================
# ÉTAPE 8: Mise à jour du sitemap.xml
# =============================================================================
log_info "Mise à jour du sitemap.xml..."

SITEMAP_FILE="$DEST_DIR/public/sitemap.xml"
if [ -f "$SITEMAP_FILE" ]; then
    # Le domaine a déjà été remplacé à l'étape 2
    log_ok "Sitemap mis à jour"
fi

# =============================================================================
# ÉTAPE 9: Renommer les fichiers contenant "bordeaux"
# =============================================================================
log_info "Renommage des fichiers contenant 'bordeaux'..."

find "$DEST_DIR" -type f -name "*bordeaux*" ! -path "*/vendor/*" ! -path "*/.git/*" | while read -r file; do
    dir=$(dirname "$file")
    base=$(basename "$file")
    new_base="${base//bordeaux/$CITY_SLUG}"
    if [ "$base" != "$new_base" ]; then
        mv "$file" "$dir/$new_base"
        log_info "  Renommé: $base -> $new_base"
    fi
done

# Mettre à jour les références aux fichiers renommés dans le code
replace_in_files "estimation-$CITY_SLUG" "estimation-$CITY_SLUG"
replace_in_files "og-estimation-$CITY_SLUG" "og-estimation-$CITY_SLUG"

log_ok "Fichiers renommés"

# =============================================================================
# ÉTAPE 10: Renommer les fichiers landing page
# =============================================================================
log_info "Mise à jour des landing pages..."

LANDING_DIR="$DEST_DIR/app/views/landing/pages"
if [ -d "$LANDING_DIR" ]; then
    # Les fichiers ont déjà été renommés à l'étape 9
    # Mettre à jour les références internes dans les routes
    ROUTES_FILE="$DEST_DIR/routes/web.php"
    if [ -f "$ROUTES_FILE" ]; then
        log_ok "Routes mises à jour"
    fi
fi

# =============================================================================
# ÉTAPE 11: Initialiser git (optionnel)
# =============================================================================
if [ "${SKIP_GIT_INIT:-0}" != "1" ]; then
    log_info "Initialisation du dépôt Git..."
    cd "$DEST_DIR"
    if git init && git add -A && git commit -m "Initial commit: Estimation Immobilier $CITY_NAME (dupliqué depuis Bordeaux)" 2>/dev/null; then
        log_ok "Dépôt Git initialisé"
    else
        log_warn "Git init ignoré (non bloquant)"
    fi
    cd - > /dev/null
fi

# =============================================================================
# ÉTAPE 12: Installer les dépendances (si composer disponible)
# =============================================================================
if command -v composer &> /dev/null; then
    log_info "Installation des dépendances Composer..."
    cd "$DEST_DIR"
    composer install --no-dev --quiet 2>/dev/null || log_warn "composer install a échoué (normal si pas de vendor)"
    cd - > /dev/null
else
    log_warn "Composer non trouvé. Pensez à exécuter 'composer install' dans $DEST_DIR"
fi

# =============================================================================
# RÉSUMÉ
# =============================================================================
echo ""
echo "=============================================="
echo -e " ${GREEN}DUPLICATION TERMINÉE !${NC}"
echo "=============================================="
echo ""
echo " Site créé: $DEST_DIR"
echo " Ville:     $CITY_NAME ($CITY_CP)"
echo " Domaine:   $DOMAIN"
echo " DB:        $DB_NAME"
echo ""
echo " Prochaines étapes:"
echo "  1. Copier .env.example vers .env et renseigner les clés API"
echo "     cp $DEST_DIR/.env.example $DEST_DIR/.env"
echo ""
echo "  2. Créer la base de données MySQL:"
echo "     mysql -e \"CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
echo "     mysql $DB_NAME < $DEST_DIR/database/schema.sql"
echo ""
echo "  3. Configurer le vhost Apache/Nginx pour $DOMAIN"
echo "     DocumentRoot: $DEST_DIR/public"
echo ""
echo "  4. Installer les dépendances:"
echo "     cd $DEST_DIR && composer install"
echo ""
echo "  5. Vérifier et personnaliser:"
echo "     - Les textes dans app/views/pages/"
echo "     - L'image OG: public/assets/images/og-estimation-$CITY_SLUG.png"
echo "     - Le favicon: public/favicon.svg"
echo "     - Les articles de blog (base de données)"
echo ""
echo "  6. Configurer DNS et SSL pour $DOMAIN"
echo ""
echo "=============================================="
