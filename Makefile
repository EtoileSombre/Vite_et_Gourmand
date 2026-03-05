# Makefile pour Vite & Gourmand

.PHONY: start stop logs ps

## start: Démarrer les containers
start:
	@cd infra && docker compose up -d

## stop: Arrêter les containers
stop:
	@cd infra && docker compose down

## logs: Voir les logs
logs:
	@cd infra && docker compose logs -f

## ps: État des containers
ps:
	@cd infra && docker compose ps

