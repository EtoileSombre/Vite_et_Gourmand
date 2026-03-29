# Makefile pour Vite & Gourmand

.PHONY: dev dev-init dev-stop dev-logs prod-stop prod-logs deploy test test-verbose

# Lancer l'environnement de dev
dev:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev up -d --build

# Initialiser les bases de donnees
dev-init:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T mysql \
		sh -c 'mysql --force -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < /docker-entrypoint-initdb.d/structure.sql'
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T mysql \
		sh -c 'mysql --force -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < /docker-entrypoint-initdb.d/donnees.sql'
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T app \
		php /var/www/html/scripts/create-mongo-indexes.php

# Arreter le dev
dev-stop:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev down

# Voir les logs dev
dev-logs:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev logs -f

# Arreter la prod
prod-stop:
	@cd infra && docker compose down

# Voir les logs prod
prod-logs:
	@cd infra && docker compose logs -f

# Lancer les tests
test:
	@cd app && vendor/bin/phpunit

# Lancer les tests (details)
test-verbose:
	@cd app && vendor/bin/phpunit --testdox

# Deployer develop vers la production
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
