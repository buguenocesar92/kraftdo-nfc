# Makefile para KraftDo NFC con FrankenPHP
# Comandos útiles para desarrollo y producción

# Variables
DOCKER_COMPOSE = docker compose
DOCKER_COMPOSE_DEV = docker compose -f docker-compose.dev.yml
DOCKER_COMPOSE_PROD = docker compose -f docker-compose.yml
APP_CONTAINER = nfc-app
APP_CONTAINER_DEV = nfc-app-dev

# Colores para output
RED = \033[31m
GREEN = \033[32m
YELLOW = \033[33m
BLUE = \033[34m
MAGENTA = \033[35m
CYAN = \033[36m
WHITE = \033[37m
RESET = \033[0m

# ==========================================
# COMANDOS PRINCIPALES
# ==========================================

.PHONY: help
help: ## Mostrar ayuda
	@echo "$(GREEN)KraftDo NFC - Comandos disponibles:$(RESET)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""

.PHONY: init
init: ## Configuración inicial del proyecto
	@echo "$(BLUE)🚀 Configurando KraftDo NFC...$(RESET)"
	@cp -n .env.example .env 2>/dev/null || true
	@chmod +x docker/start.sh
	@echo "$(GREEN)✅ Configuración inicial completada$(RESET)"

# ==========================================
# DESARROLLO
# ==========================================

.PHONY: dev
dev: ## Iniciar entorno de desarrollo
	@echo "$(BLUE)🛠️  Iniciando entorno de desarrollo...$(RESET)"
	$(DOCKER_COMPOSE_DEV) up -d
	@echo "$(GREEN)✅ Desarrollo iniciado en http://localhost:8080$(RESET)"

.PHONY: dev-build
dev-build: ## Construir imágenes de desarrollo
	@echo "$(BLUE)🔨 Construyendo imágenes de desarrollo...$(RESET)"
	$(DOCKER_COMPOSE_DEV) build --no-cache

.PHONY: dev-logs
dev-logs: ## Ver logs de desarrollo
	$(DOCKER_COMPOSE_DEV) logs -f

.PHONY: dev-shell
dev-shell: ## Entrar al contenedor de desarrollo
	$(DOCKER_COMPOSE_DEV) exec app sh

.PHONY: dev-artisan
dev-artisan: ## Ejecutar comando artisan en desarrollo (usar: make dev-artisan CMD="migrate")
	$(DOCKER_COMPOSE_DEV) exec app php artisan $(CMD)

.PHONY: dev-composer
dev-composer: ## Ejecutar comando composer en desarrollo (usar: make dev-composer CMD="install")
	$(DOCKER_COMPOSE_DEV) exec app composer $(CMD)

.PHONY: dev-npm
dev-npm: ## Ejecutar comando npm en desarrollo (usar: make dev-npm CMD="install")
	$(DOCKER_COMPOSE_DEV) exec app npm $(CMD)

.PHONY: dev-test
dev-test: ## Ejecutar tests en desarrollo
	$(DOCKER_COMPOSE_DEV) exec app php artisan test

.PHONY: dev-migrate
dev-migrate: ## Ejecutar migraciones en desarrollo
	$(DOCKER_COMPOSE_DEV) exec app php artisan migrate

.PHONY: dev-fresh
dev-fresh: ## Reset completo de base de datos en desarrollo
	$(DOCKER_COMPOSE_DEV) exec app php artisan migrate:fresh --seed

.PHONY: dev-stop
dev-stop: ## Parar entorno de desarrollo
	$(DOCKER_COMPOSE_DEV) down

.PHONY: dev-clean
dev-clean: ## Limpiar entorno de desarrollo (incluyendo volúmenes)
	$(DOCKER_COMPOSE_DEV) down -v --remove-orphans
	docker system prune -f

# ==========================================
# PRODUCCIÓN
# ==========================================

.PHONY: prod
prod: ## Iniciar entorno de producción
	@echo "$(RED)🏭 Iniciando entorno de producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) up -d
	@echo "$(GREEN)✅ Producción iniciada$(RESET)"

.PHONY: prod-build
prod-build: ## Construir imágenes de producción
	@echo "$(RED)🔨 Construyendo imágenes de producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) build --no-cache

.PHONY: prod-logs
prod-logs: ## Ver logs de producción
	$(DOCKER_COMPOSE_PROD) logs -f

.PHONY: prod-shell
prod-shell: ## Entrar al contenedor de producción
	$(DOCKER_COMPOSE_PROD) exec app sh

.PHONY: prod-artisan
prod-artisan: ## Ejecutar comando artisan en producción (usar: make prod-artisan CMD="migrate")
	$(DOCKER_COMPOSE_PROD) exec app php artisan $(CMD)

.PHONY: prod-migrate
prod-migrate: ## Ejecutar migraciones en producción
	$(DOCKER_COMPOSE_PROD) exec app php artisan migrate --force

.PHONY: prod-optimize
prod-optimize: ## Optimizar aplicación para producción
	$(DOCKER_COMPOSE_PROD) exec app php artisan optimize
	$(DOCKER_COMPOSE_PROD) exec app php artisan filament:optimize

.PHONY: prod-stop
prod-stop: ## Parar entorno de producción
	$(DOCKER_COMPOSE_PROD) down

.PHONY: prod-backup-db
prod-backup-db: ## Hacer backup de base de datos de producción
	@echo "$(YELLOW)💾 Creando backup de base de datos...$(RESET)"
	mkdir -p backups
	$(DOCKER_COMPOSE_PROD) exec mysql mysqldump -u root -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE > backups/db-$(shell date +%Y%m%d-%H%M%S).sql
	@echo "$(GREEN)✅ Backup completado en backups/$(RESET)"

# ==========================================
# UTILIDADES
# ==========================================

.PHONY: install
install: init dev-build ## Instalación completa del proyecto
	@echo "$(MAGENTA)📦 Instalación completa iniciada...$(RESET)"
	$(MAKE) dev
	@echo "$(GREEN)🎉 Instalación completada! Ve a http://localhost:8080$(RESET)"

.PHONY: status
status: ## Ver estado de todos los contenedores
	@echo "$(BLUE)📊 Estado de contenedores:$(RESET)"
	docker ps -a --filter "name=nfc-" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

.PHONY: clean
clean: ## Limpiar sistema Docker completo
	@echo "$(YELLOW)🧹 Limpiando sistema Docker...$(RESET)"
	docker system prune -af --volumes
	@echo "$(GREEN)✅ Limpieza completada$(RESET)"

.PHONY: health
health: ## Verificar salud de servicios
	@echo "$(BLUE)🏥 Verificando salud de servicios...$(RESET)"
	@curl -s http://localhost:8080/health | jq . || echo "Servicio no disponible"

.PHONY: logs
logs: ## Ver logs combinados (desarrollo por defecto)
	@if [ -f docker-compose.override.yml ]; then \
		$(DOCKER_COMPOSE_DEV) logs -f; \
	else \
		$(DOCKER_COMPOSE_PROD) logs -f; \
	fi

# ==========================================
# DATABASE & MIGRATIONS
# ==========================================

.PHONY: migrate
migrate: ## Ejecutar migraciones (detecta entorno automáticamente)
	@if docker ps | grep -q nfc-app-dev; then \
		$(MAKE) dev-migrate; \
	else \
		$(MAKE) prod-migrate; \
	fi

.PHONY: seed
seed: ## Ejecutar seeders (detecta entorno automáticamente)
	@if docker ps | grep -q nfc-app-dev; then \
		$(DOCKER_COMPOSE_DEV) exec app php artisan db:seed; \
	else \
		$(DOCKER_COMPOSE_PROD) exec app php artisan db:seed; \
	fi

.PHONY: fresh
fresh: ## Reset completo de base de datos (detecta entorno)
	@if docker ps | grep -q nfc-app-dev; then \
		$(MAKE) dev-fresh; \
	else \
		@echo "$(RED)⚠️  PELIGRO: Esto borrará toda la base de datos de producción!$(RESET)"; \
		@echo "$(YELLOW)Escribe 'yes' para continuar:$(RESET)"; \
		@read confirm && [ "$$confirm" = "yes" ] && $(DOCKER_COMPOSE_PROD) exec app php artisan migrate:fresh --seed --force; \
	fi

# ==========================================
# TESTING & QUALITY
# ==========================================

.PHONY: test
test: ## Ejecutar tests
	@if docker ps | grep -q nfc-app-dev; then \
		$(MAKE) dev-test; \
	else \
		$(DOCKER_COMPOSE_PROD) run --rm app /usr/local/bin/start.sh test; \
	fi

.PHONY: test-coverage
test-coverage: ## Ejecutar tests con cobertura
	$(DOCKER_COMPOSE_DEV) exec app php artisan test --coverage

.PHONY: lint
lint: ## Ejecutar linters (PHP CS Fixer, ESLint)
	$(DOCKER_COMPOSE_DEV) exec app ./vendor/bin/php-cs-fixer fix --dry-run --diff
	$(DOCKER_COMPOSE_DEV) exec app npm run lint

.PHONY: fix
fix: ## Arreglar código automáticamente
	$(DOCKER_COMPOSE_DEV) exec app ./vendor/bin/php-cs-fixer fix
	$(DOCKER_COMPOSE_DEV) exec app npm run lint:fix

# ==========================================
# MONITORING & DEBUGGING
# ==========================================

.PHONY: monitor
monitor: ## Monitorear recursos de contenedores
	docker stats $(shell docker ps --filter "name=nfc-" --format "{{.Names}}")

.PHONY: debug
debug: ## Información de debug
	@echo "$(CYAN)🐛 Información de debug:$(RESET)"
	@echo "Docker version: $(shell docker --version)"
	@echo "Docker Compose version: $(shell docker compose version)"
	@echo "Contenedores activos:"
	@docker ps --filter "name=nfc-" --format "  - {{.Names}} ({{.Status}})"

# ==========================================
# DOCKER UTILITIES
# ==========================================

.PHONY: pull
pull: ## Actualizar imágenes Docker
	$(DOCKER_COMPOSE_DEV) pull
	$(DOCKER_COMPOSE_PROD) pull

.PHONY: restart
restart: ## Reiniciar contenedores (detecta entorno)
	@if docker ps | grep -q nfc-app-dev; then \
		$(DOCKER_COMPOSE_DEV) restart; \
	else \
		$(DOCKER_COMPOSE_PROD) restart; \
	fi

# ==========================================
# SHORTCUTS COMUNES
# ==========================================

.PHONY: up
up: dev ## Alias para 'make dev'

.PHONY: down
down: ## Parar todos los contenedores
	$(DOCKER_COMPOSE_DEV) down 2>/dev/null || true
	$(DOCKER_COMPOSE_PROD) down 2>/dev/null || true

.PHONY: build
build: ## Construir imágenes (detecta entorno)
	@if [ -f .env ] && grep -q "APP_ENV=local" .env; then \
		$(MAKE) dev-build; \
	else \
		$(MAKE) prod-build; \
	fi

.PHONY: shell
shell: ## Entrar al contenedor (detecta entorno)
	@if docker ps | grep -q nfc-app-dev; then \
		$(MAKE) dev-shell; \
	else \
		$(MAKE) prod-shell; \
	fi

# Default target
.DEFAULT_GOAL := help