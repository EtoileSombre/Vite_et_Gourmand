# Makefile pour Vite & Gourmand

.PHONY: dev dev-init dev-stop dev-logs prod-stop prod-logs deploy test test-verbose

# ===== DÉVELOPPEMENT =====

dev:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev up -d --build

dev-init:
	@echo "⏳ Importation MySQL..."
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T mysql \
		sh -c 'mysql --force -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < /docker-entrypoint-initdb.d/structure.sql'
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T mysql \
		sh -c 'mysql --force -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < /docker-entrypoint-initdb.d/donnees.sql'
	@echo "⏳ Création des index MongoDB..."
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev exec -T app \
		php /var/www/html/scripts/create-mongo-indexes.php
	@echo "Bases de données initialisées."

dev-stop:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev down

dev-logs:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev logs -f

# ===== PRODUCTION =====

prod-stop:
	@cd infra && docker compose down

prod-logs:
	@cd infra && docker compose logs -f

# ===== TESTS =====

test:
	@cd app && vendor/bin/phpunit

test-verbose:
	@cd app && vendor/bin/phpunit --testdox

# ===== DÉPLOIEMENT =====

deploy:
	@CURRENT_BRANCH=$$(git rev-parse --abbrev-ref HEAD); \
	if [ "$$CURRENT_BRANCH" != "develop" ]; then \
		echo "Erreur : vous devez être sur la branche develop (actuellement sur $$CURRENT_BRANCH)"; \
		exit 1; \
	fi; \
	echo "🔀 Passage sur main et merge de develop..."; \
	git checkout main && \
	git merge develop && \
	echo "🚀 Rebuild des containers de production..." && \
	cd infra && docker compose up -d --build && cd .. && \
	echo "📤 Push sur GitHub..." && \
	git push origin main && \
	echo "↩️  Retour sur develop..." && \
	git checkout develop && \
	echo "Déploiement terminé !"

