# Makefile para KraftDo NFC con configuración optimizada
# Comandos útiles para desarrollo y staging

# Variables configurables
DOCKER_COMPOSE = docker compose
DOCKER_COMPOSE_STAGING = docker compose -f docker-compose.staging.yml
DOCKER_COMPOSE_PROD = docker compose -f docker-compose.prod.yml
COMPOSE_PROJECT_NAME ?= kraftdo-nfc

# Detectar entorno automáticamente
CURRENT_ENV := $(shell if docker ps | grep -q "kraftdo-nfc-staging-web"; then echo "staging"; elif docker ps | grep -q "kraftdo-nfc-prod"; then echo "prod"; else echo "dev"; fi)
ACTIVE_COMPOSE := $(if $(filter staging,$(CURRENT_ENV)),$(DOCKER_COMPOSE_STAGING),$(if $(filter prod,$(CURRENT_ENV)),$(DOCKER_COMPOSE_PROD),$(DOCKER_COMPOSE)))
SERVICE_NAME := $(if $(filter staging,$(CURRENT_ENV)),web,$(if $(filter prod,$(CURRENT_ENV)),app,app))

# Colores para output
RED = \033[31m
GREEN = \033[32m
YELLOW = \033[33m
BLUE = \033[34m
CYAN = \033[36m
WHITE = \033[37m
RESET = \033[0m
BOLD = \033[1m

# ==========================================
# COMANDOS PRINCIPALES
# ==========================================

.PHONY: help
help: ## 📚 Mostrar ayuda
	@echo "$(BOLD)$(GREEN)🚀 KraftDo NFC - Comandos de Deploy$(RESET)"
	@echo "$(CYAN)Entorno actual: $(CURRENT_ENV)$(RESET)"
	@echo ""
	@echo "$(BOLD)COMANDOS PRINCIPALES:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'

.PHONY: dev
dev: ## 🛠️ Iniciar desarrollo
	@echo "$(BLUE)🛠️ Iniciando desarrollo...$(RESET)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✅ Desarrollo iniciado$(RESET)"

.PHONY: staging
staging: ## 🔄 Iniciar staging
	@echo "$(YELLOW)🔄 Iniciando staging...$(RESET)"
	$(DOCKER_COMPOSE_STAGING) down --remove-orphans || true
	$(DOCKER_COMPOSE_STAGING) build --no-cache
	$(DOCKER_COMPOSE_STAGING) up -d
	@echo "$(GREEN)✅ Staging iniciado$(RESET)"

.PHONY: prod
prod: ## 🏭 Iniciar producción
	@echo "$(RED)🏭 Iniciando producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) up -d
	@echo "$(GREEN)✅ Producción iniciada$(RESET)"

# ==========================================
# GESTIÓN DE SERVICIOS
# ==========================================

.PHONY: up
up: ## 🚀 Iniciar servicios
	@echo "$(BLUE)🚀 Iniciando servicios...$(RESET)"
	$(ACTIVE_COMPOSE) up -d

.PHONY: down
down: ## 🛑 Parar servicios
	@echo "$(YELLOW)🛑 Parando servicios...$(RESET)"
	$(ACTIVE_COMPOSE) down

.PHONY: restart
restart: ## 🔄 Reiniciar servicios
	@echo "$(YELLOW)🔄 Reiniciando servicios...$(RESET)"
	$(ACTIVE_COMPOSE) restart

.PHONY: rebuild
rebuild: ## 🔨 Rebuild completo
	@echo "$(BLUE)🔨 Rebuild completo...$(RESET)"
	$(ACTIVE_COMPOSE) down --remove-orphans
	$(ACTIVE_COMPOSE) build --no-cache
	$(ACTIVE_COMPOSE) up -d
	@echo "$(GREEN)✅ Rebuild completado$(RESET)"

# ==========================================
# GESTIÓN LARAVEL
# ==========================================

.PHONY: shell
shell: ## 💻 Acceder al shell del contenedor
	@echo "$(BLUE)💻 Accediendo al shell...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) sh

.PHONY: logs
logs: ## 📝 Ver logs
	$(ACTIVE_COMPOSE) logs -f

.PHONY: cache-clear
cache-clear: ## 🧹 Limpiar caches de Laravel
	@echo "$(YELLOW)🧹 Limpiando caches...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan config:clear
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan route:clear
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan view:clear
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan cache:clear
	@echo "$(GREEN)✅ Caches limpiados$(RESET)"

.PHONY: optimize
optimize: ## ⚡ Optimizar Laravel
	@echo "$(YELLOW)⚡ Optimizando Laravel...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan config:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan route:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan view:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan filament:assets
	@echo "$(GREEN)✅ Laravel optimizado$(RESET)"

.PHONY: migrate
migrate: ## 🗄️ Ejecutar migraciones
	@echo "$(BLUE)🗄️ Ejecutando migraciones...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate --force

.PHONY: migrate-fresh
migrate-fresh: ## 🔄 Reset base de datos
	@echo "$(RED)🔄 Reset de base de datos...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate:fresh --seed

# ==========================================
# MONITOREO Y DEBUG
# ==========================================

.PHONY: status
status: ## 📋 Estado del sistema
	@echo "$(BOLD)$(BLUE)📋 ESTADO DEL SISTEMA$(RESET)"
	@echo ""
	@echo "$(CYAN)Entorno actual: $(BOLD)$(CURRENT_ENV)$(RESET)"
	@echo ""
	@echo "$(CYAN)Contenedores:$(RESET)"
	@docker ps --filter "name=kraftdo-nfc" --format "  {{.Names}} - {{.Status}}"
	@echo ""

.PHONY: health
health: ## 🏥 Verificar salud del sistema
	@echo "$(BLUE)🏥 Verificando salud...$(RESET)"
	@echo "$(CYAN)Servicios web:$(RESET)"
	@if [ "$(CURRENT_ENV)" = "staging" ]; then \
		curl -s http://localhost:8084/health || echo "  ❌ Staging: No disponible"; \
	else \
		curl -s http://localhost:8080/health || echo "  ❌ App: No disponible"; \
	fi

.PHONY: clean
clean: ## 🧹 Limpieza completa
	@echo "$(YELLOW)🧹 Limpieza completa...$(RESET)"
	$(DOCKER_COMPOSE) down -v --remove-orphans 2>/dev/null || true
	$(DOCKER_COMPOSE_STAGING) down -v --remove-orphans 2>/dev/null || true
	$(DOCKER_COMPOSE_PROD) down -v --remove-orphans 2>/dev/null || true
	docker system prune -af
	@echo "$(GREEN)✅ Limpieza completada$(RESET)"

# ==========================================
# COMANDOS ARTISAN DIRECTOS
# ==========================================

artisan-%: ## 🎯 Ejecutar comando artisan (ej: make artisan-migrate)
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan $*

composer-%: ## 📦 Ejecutar comando composer (ej: make composer-install)
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer $*

# Target por defecto
.DEFAULT_GOAL := help