.PHONY: help setup restructure css test server clean

# Couleurs
YELLOW := \033[0;33m
GREEN := \033[0;32m
RED := \033[0;31m
NC := \033[0m

help:
	@echo "$(YELLOW)===== COMMANDES DISPONIBLES =====$(NC)"
	@echo ""
	@echo "$(GREEN)make setup$(NC)          - Installation initiale complète"
	@echo "$(GREEN)make restructure$(NC)    - Restructurer le projet MVC"
	@echo "$(GREEN)make css$(NC)            - Copier le CSS complet"
	@echo "$(GREEN)make test$(NC)           - Vérifier la structure"
	@echo "$(GREEN)make server$(NC)         - Lancer un serveur de développement"
	@echo "$(GREEN)make clean$(NC)          - Nettoyer les fichiers temporaires"
	@echo ""

setup: restructure css
	@echo "$(GREEN)✓ Installation complète terminée !$(NC)"

restructure:
	@echo "$(YELLOW)Restructuration du projet...$(NC)"
	@bash restructure.sh

css:
	@echo "$(YELLOW)Copie du CSS complet...$(NC)"
	@if [ -f "app-css-complete-final.css" ]; then \
		cp app-css-complete-final.css public/assets/css/app.css; \
		echo "$(GREEN)✓ CSS copié dans public/assets/css/app.css$(NC)"; \
	else \
		echo "$(RED)✗ Fichier app-css-complete-final.css non trouvé$(NC)"; \
	fi

test:
	@echo "$(YELLOW)Vérification de la structure...$(NC)"
	@echo ""
	@echo "Fichiers critiques :"
	@test -f "app/views/layouts/header.php" && echo "$(GREEN)✓$(NC) header.php" || echo "$(RED)✗$(NC) header.php MANQUANT"
	@test -f "app/views/layouts/footer.php" && echo "$(GREEN)✓$(NC) footer.php" || echo "$(RED)✗$(NC) footer.php MANQUANT"
	@test -f "public/assets/css/app.css" && echo "$(GREEN)✓$(NC) app.css" || echo "$(RED)✗$(NC) app.css MANQUANT"
	@test -f "app/controllers/PageController.php" && echo "$(GREEN)✓$(NC) PageController.php" || echo "$(RED)✗$(NC) PageController.php MANQUANT"
	@test -f "routes/web.php" && echo "$(GREEN)✓$(NC) routes/web.php" || echo "$(RED)✗$(NC) routes/web.php MANQUANT"
	@echo ""
	@echo "Répertoires :"
	@test -d "app/views/pages" && echo "$(GREEN)✓$(NC) app/views/pages" || echo "$(RED)✗$(NC) app/views/pages MANQUANT"
	@test -d "app/views/layouts" && echo "$(GREEN)✓$(NC) app/views/layouts" || echo "$(RED)✗$(NC) app/views/layouts MANQUANT"
	@test -d "public/assets/css" && echo "$(GREEN)✓$(NC) public/assets/css" || echo "$(RED)✗$(NC) public/assets/css MANQUANT"
	@echo ""

server:
	@echo "$(YELLOW)Lancement du serveur PHP (port 8000)...$(NC)"
	@echo "$(GREEN)Accès : http://localhost:8000$(NC)"
	@echo "$(YELLOW)Appuyez sur Ctrl+C pour arrêter$(NC)"
	@echo ""
	php -S localhost:8000

clean:
	@echo "$(YELLOW)Nettoyage des fichiers temporaires...$(NC)"
	@find . -name "*.swp" -delete
	@find . -name "*.swo" -delete
	@find . -name ".DS_Store" -delete
	@find . -name "Thumbs.db" -delete
	@echo "$(GREEN)✓ Nettoyage terminé$(NC)"
