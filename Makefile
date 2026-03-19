.PHONY: setup restructure css test server clean help

setup: restructure css test

restructure:
	bash ./restructure.sh

css:
	cp app-css-complete-final.css public/assets/css/app.css
	@echo "CSS copié dans public/assets/css/app.css"

test:
	@test -f app/views/layouts/header.php
	@test -f app/views/layouts/footer.php
	@test -f app/views/pages/home.php
	@test -f app/views/pages/about.php
	@test -f app/views/pages/services.php
	@test -f app/views/pages/contact.php
	@test -f app/controllers/PageController.php
	@test -f routes/web.php
	@test -f public/assets/css/app.css
	@echo "Structure validée ✅"

server:
	php -S localhost:8000 -t public

clean:
	rm -f app-css-complete-final.css
	@echo "Fichier temporaire supprimé: app-css-complete-final.css"

help:
	@echo "Commandes disponibles:"
	@echo "  make setup       -> Restructure + CSS + test"
	@echo "  make restructure -> Exécute restructure.sh"
	@echo "  make css         -> Copie app-css-complete-final.css vers public/assets/css/app.css"
	@echo "  make test        -> Vérifie les fichiers attendus"
	@echo "  make server      -> Lance un serveur local PHP"
	@echo "  make clean       -> Nettoie les fichiers générés"
