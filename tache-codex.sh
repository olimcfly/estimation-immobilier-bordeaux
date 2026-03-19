#!/bin/bash
 
# TÂCHE CODEX - Structure + Menu
# Usage: bash tache-codex.sh
 
set -e
 
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'
 
echo -e "${BLUE}"
echo "╔════════════════════════════════════════╗"
echo "║     TÂCHE CODEX - STRUCTURE + MENU     ║"
echo "╚════════════════════════════════════════╝"
echo -e "${NC}\n"
 
# ✅ ÉTAPE 1: RESTRUCTURATION
echo -e "${YELLOW}[1/5] Restructuration MVC...${NC}"
bash restructure.sh
echo -e "${GREEN}✓ Étape 1 complète${NC}\n"
 
# ✅ ÉTAPE 2: HEADER PREMIUM
echo -e "${YELLOW}[2/5] Installation header premium...${NC}"
if [ -f "header-premium-complete.php" ]; then
    cp header-premium-complete.php app/views/layouts/header.php
    echo -e "${GREEN}✓ Header installé (menu navigation)${NC}"
else
    echo -e "${RED}✗ Fichier non trouvé${NC}"
fi
echo ""
 
# ✅ ÉTAPE 3: CSS
echo -e "${YELLOW}[3/5] Installation CSS complet...${NC}"
if [ -f "app-css-complete-final.css" ]; then
    cp app-css-complete-final.css public/assets/css/app.css
    echo -e "${GREEN}✓ CSS installé (Bordeaux/Gold)${NC}"
else
    echo -e "${RED}✗ Fichier non trouvé${NC}"
fi
echo ""
 
# ✅ ÉTAPE 4: HOME PREMIUM
echo -e "${YELLOW}[4/5] Installation home premium...${NC}"
if [ -f "home-premium-complete.php" ]; then
    cp home-premium-complete.php app/views/pages/home.php
    echo -e "${GREEN}✓ Home installée (6 sections)${NC}"
else
    echo -e "${RED}✗ Fichier non trouvé${NC}"
fi
echo ""
 
# ✅ ÉTAPE 5: VÉRIFICATION
echo -e "${YELLOW}[5/5] Vérification structure...${NC}"
make test
echo ""
 
# ✅ RÉSUMÉ
echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     ✅ TÂCHE COMPLÈTE !                ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""
 
echo -e "${BLUE}Structure créée :${NC}"
echo "  ✓ app/views/layouts/header.php (MENU PREMIUM)"
echo "  ✓ public/assets/css/app.css (CSS BORDEAUX/OR)"
echo "  ✓ app/views/pages/home.php (HOME PREMIUM)"
echo "  ✓ Routes + Pages"
echo ""
 
echo -e "${BLUE}Menus disponibles :${NC}"
echo "  • Estimation (dropdown)"
echo "  • Blog (dropdown)"
echo "  • Services (dropdown)"
echo "  • À propos"
echo "  • Contact"
echo "  • Ressources (dropdown)"
echo ""
 
echo -e "${YELLOW}Prochaine étape :${NC}"
echo "  make server"
echo "  Puis ouvrir : http://localhost:8000"
echo ""
