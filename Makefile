# Makefile pour Vite & Gourmand

.PHONY: dev dev-stop dev-logs dev-ps prod prod-stop prod-logs prod-ps deploy

# ===== DÉVELOPPEMENT (branche develop) =====

## dev: Démarrer l'environnement de développement
dev:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev up -d --build

## dev-stop: Arrêter l'environnement de développement
dev-stop:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev down

## dev-logs: Voir les logs de développement
dev-logs:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev logs -f

## dev-ps: État des containers de développement
dev-ps:
	@cd infra && docker compose -f docker-compose.dev.yml --env-file .env.dev ps

# ===== PRODUCTION (branche main) =====

## prod: Démarrer l'environnement de production
prod:
	@cd infra && docker compose up -d --build

## prod-stop: Arrêter l'environnement de production
prod-stop:
	@cd infra && docker compose down

## prod-logs: Voir les logs de production
prod-logs:
	@cd infra && docker compose logs -f

## prod-ps: État des containers de production
prod-ps:
	@cd infra && docker compose ps

# ===== DÉPLOIEMENT (develop → main → prod) =====

## deploy: Déployer develop en production (merge + rebuild)
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
	echo "↩️  Retour sur develop..." && \
	git checkout develop && \
	echo "Déploiement terminé !"

