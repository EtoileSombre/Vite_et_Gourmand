# Makefile pour Vite & Gourmand

# .PHONY indique que ces noms ne sont pas des fichiers mais des commandes
.PHONY: dev dev-init dev-stop dev-logs prod-stop prod-logs deploy test test-verbose test-auth test-csrf test-price test-order test-coverage test-docker

# ENVIRONNEMENT DE DEV

# Lancer l'environnement de dev
# - docker compose 
# - -f docker-compose.dev.yml 
# - --env-file .env.dev 
# - up -d 
# - --build 
dev:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev up -d --build

# Initialiser les bases de donnees de dev
# - exec -T mysql : execute une commande dans le conteneur MySQL
# - Importe d'abord structure.sql (tables, relations)
# - Puis donnees.sql (donnees de test : menus, plats, utilisateurs...)
# - Enfin, cree les index MongoDB pour les statistiques
dev-init:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T mysql \
		sh -c 'mysql --force -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < /docker-entrypoint-initdb.d/structure.sql'
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T mysql \
		sh -c 'mysql --force -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < /docker-entrypoint-initdb.d/donnees.sql'
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T app \
		php /var/www/html/scripts/create-mongo-indexes.php

# Arreter tous les conteneurs de dev
# - down 
dev-stop:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev down

# Afficher les logs de dev en temps reel
# - logs -f : suit les logs en continu (Ctrl+C pour quitter)
# - Utile pour voir les erreurs PHP, les connexions, les requetes SQL...
dev-logs:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev logs -f


# ENVIRONNEMENT DE PRODUCTION

# Arreter tous les conteneurs de production
# - Utilise docker-compose.yml (config prod) et .env (variables prod)
prod-stop:
	@cd infra && docker compose down

# Afficher les logs de production en temps reel
prod-logs:
	@cd infra && docker compose logs -f


# TESTS UNITAIRES (PHPUnit)

# Lancer tous les tests (sortie minimale)
# - vendor/bin/phpunit : executable PHPUnit installe via Composer
test:
	@cd app && vendor/bin/phpunit

# Lancer tous les tests avec details lisibles
# - --testdox : affiche chaque test sous forme de jolies phrases :)
test-verbose:
	@cd app && vendor/bin/phpunit --testdox

# Teste uniquement l'authentification (login, inscription, deconnexion)
# - --filter : execute seulement les tests de la classe AuthenticationTest
test-auth:
	@cd app && vendor/bin/phpunit --testdox --filter AuthenticationTest

# Teste uniquement la protection CSRF (securite des formulaires)
test-csrf:
	@cd app && vendor/bin/phpunit --testdox --filter CsrfTest

# Teste uniquement les calculs de prix (tarifs, reductions, livraison)
test-price:
	@cd app && vendor/bin/phpunit --testdox --filter PriceCalculationTest

# Teste uniquement le workflow des commandes (creation, modification, annulation)
test-order:
	@cd app && vendor/bin/phpunit --testdox --filter OrderWorkflowTest

# Lancer les tests avec rapport de couverture de code
# - Indique le pourcentage du code source couvert par les tests
# - Necessite l'extension Xdebug
test-coverage:
	@cd app && XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text

# Lancer les tests directement dans le conteneur Docker
# - Execute PHPUnit dans les memes conditions que l'application
# - Meme version PHP, memes extensions, meme environnement
test-docker:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T app vendor/bin/phpunit --testdox

# DEPLOIEMENT

# Deployer le code de develop vers la production
# Etapes automatisees :
# 1. Verifie qu'on est sur la branche develop (securite)
# 2. Bascule sur main et y fusionne develop (merge)
# 3. Pousse main sur GitHub (git push)
# 4. Met a jour le code de production sur le serveur (git pull)
# 5. Reconstruit le conteneur de production (docker compose up --build)
# 6. Revient sur develop et pousse les modifications
deploy:
	@CURRENT_BRANCH=$$(git rev-parse --abbrev-ref HEAD); \
	if [ "$$CURRENT_BRANCH" != "develop" ]; then \
		echo "Erreur : vous devez etre sur la branche develop"; \
		exit 1; \
	fi; \
	git checkout main && \
	git merge develop && \
	git push origin main && \
	cd /opt/Vite_et_Gourmand && git pull origin main && cd - && \
	cd infra && docker compose up -d --build && cd .. && \
	git checkout develop && \
	git push origin develop
