# Makefile para KraftDo NFC con FrankenPHP - OPTIMIZADO
# Comandos útiles para desarrollo y producción con optimizaciones Laravel/Filament

# Variables configurables
DOCKER_COMPOSE = docker compose
DOCKER_COMPOSE_DEV = docker compose -f docker-compose.dev.yml
DOCKER_COMPOSE_PROD = docker compose -f docker-compose.prod.yml
DOCKER_COMPOSE_HYBRID = docker compose -f docker-compose.hybrid.yml
COMPOSE_PROJECT_NAME ?= kraftdo-nfc
ENV_FILE ?= .env

# Detectar entorno automáticamente
CURRENT_ENV := $(shell if docker ps | grep -q "kraftdo-nfc-dev-php-fpm"; then echo "dev"; elif docker ps | grep -q "kraftdo-nfc-prod-php-fpm"; then echo "prod"; else echo "hybrid"; fi)
ACTIVE_COMPOSE := $(if $(filter dev,$(CURRENT_ENV)),$(DOCKER_COMPOSE_DEV),$(if $(filter prod,$(CURRENT_ENV)),$(DOCKER_COMPOSE_PROD),$(DOCKER_COMPOSE_HYBRID)))
SERVICE_NAME := $(if $(filter hybrid,$(CURRENT_ENV)),app,php-fpm)

# Colores para output
RED = \033[31m
GREEN = \033[32m
YELLOW = \033[33m
BLUE = \033[34m
MAGENTA = \033[35m
CYAN = \033[36m
WHITE = \033[37m
RESET = \033[0m
BOLD = \033[1m

# ==========================================
# COMANDOS PRINCIPALES OPTIMIZADOS
# ==========================================

.PHONY: help
help: ## 📚 Mostrar ayuda completa
	@echo "$(BOLD)$(GREEN)🚀 KraftDo NFC - Comandos Optimizados$(RESET)"
	@echo "$(CYAN)Entorno actual: $(CURRENT_ENV)$(RESET)"
	@echo ""
	@echo "$(BOLD)COMANDOS PRINCIPALES:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E "📚|🚀|🛠️|⚡" | grep -v "deploy-prod" | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(CYAN)%-25s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)DEPLOYMENT PRODUCCIÓN:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E "🏭|deploy-prod" | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(RED)%-25s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)OPTIMIZACIÓN & PERFORMANCE:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E "🔥|📊|🏎️|💾" | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(YELLOW)%-25s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)BASE DE DATOS:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E "🗄️|🔄" | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(MAGENTA)%-25s$(RESET) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)UTILIDADES:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E "🧹|🏥|🐛|📋|🔍" | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(WHITE)%-25s$(RESET) %s\n", $$1, $$2}'

# ==========================================
# INSTALACIÓN Y CONFIGURACIÓN OPTIMIZADA
# ==========================================

.PHONY: install
install: init optimize-install ## 🚀 Instalación completa optimizada
	@echo "$(BOLD)$(MAGENTA)🚀 Instalación completa con optimizaciones...$(RESET)"
	$(MAKE) dev-build-optimized
	$(MAKE) dev
	$(MAKE) setup-laravel-optimized
	@echo "$(BOLD)$(GREEN)🎉 Instalación completada y optimizada! Ve a http://localhost:8080$(RESET)"

.PHONY: init
init: ## 📚 Configuración inicial del proyecto
	@echo "$(BLUE)🚀 Configurando KraftDo NFC...$(RESET)"
	@if [ ! -f .env ]; then \
		echo "$(YELLOW)📄 Copiando .env.example a .env...$(RESET)"; \
		cp .env.example .env; \
	fi
	@chmod +x docker/start.sh
	@mkdir -p storage/{logs,framework/{cache,sessions,views},app/public}
	@mkdir -p bootstrap/cache
	@mkdir -p backups
	@echo "$(GREEN)✅ Configuración inicial completada$(RESET)"

.PHONY: optimize-install
optimize-install: ## ⚡ Optimizaciones durante instalación
	@echo "$(YELLOW)⚡ Aplicando optimizaciones de instalación...$(RESET)"
	@sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=redis/' .env 2>/dev/null || true
	@sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env 2>/dev/null || true
	@sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env 2>/dev/null || true
	@echo "OCTANE_SERVER=frankenphp" >> .env 2>/dev/null || true

# ==========================================
# ENTORNOS OPTIMIZADOS
# ==========================================

.PHONY: dev
dev: ## 🛠️ Desarrollo con optimizaciones
	@echo "$(BLUE)🛠️ Iniciando desarrollo OPTIMIZADO...$(RESET)"
	$(DOCKER_COMPOSE_DEV) up -d
	@sleep 5
	$(MAKE) cache-clear
	@echo "$(GREEN)✅ Desarrollo optimizado en http://localhost:8080$(RESET)"

.PHONY: dev-build-optimized
dev-build-optimized: ## 🔥 Build desarrollo con optimizaciones
	@echo "$(BLUE)🔥 Build optimizado para desarrollo...$(RESET)"
	$(DOCKER_COMPOSE_DEV) build --parallel --no-cache
	@echo "$(GREEN)✅ Build optimizado completado$(RESET)"

.PHONY: prod
prod: prod-pre-flight prod-optimize ## 🏭 Producción con optimizaciones completas
	@echo "$(RED)🏭 Iniciando producción OPTIMIZADA...$(RESET)"
	$(DOCKER_COMPOSE_PROD) up -d
	@sleep 10
	$(MAKE) health
	@echo "$(GREEN)✅ Producción optimizada iniciada$(RESET)"

.PHONY: prod-pre-flight
prod-pre-flight: ## 🔍 Verificaciones pre-producción
	@echo "$(YELLOW)🔍 Verificaciones pre-producción...$(RESET)"
	@if [ ! -f .env ]; then echo "$(RED)❌ Archivo .env no encontrado$(RESET)" && exit 1; fi
	@if grep -q "APP_DEBUG=true" .env; then echo "$(RED)❌ APP_DEBUG debe ser false en producción$(RESET)" && exit 1; fi
	@if grep -q "APP_ENV=local" .env; then echo "$(RED)❌ APP_ENV debe ser production$(RESET)" && exit 1; fi
	@echo "$(GREEN)✅ Verificaciones pre-producción completadas$(RESET)"

# ==========================================
# OPTIMIZACIONES LARAVEL & FILAMENT
# ==========================================

.PHONY: optimize
optimize: ## ⚡ Optimización completa Laravel/Filament
	@echo "$(BOLD)$(YELLOW)⚡ OPTIMIZACIÓN COMPLETA iniciada...$(RESET)"
	$(MAKE) cache-all
	$(MAKE) optimize-composer
	$(MAKE) optimize-laravel
	$(MAKE) optimize-filament
	$(MAKE) optimize-octane
	@echo "$(BOLD)$(GREEN)🚀 OPTIMIZACIÓN COMPLETA terminada$(RESET)"

.PHONY: cache-all
cache-all: ## 💾 Cachear todo (config, routes, views, components)
	@echo "$(YELLOW)💾 Cacheando configuraciones...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan config:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan route:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan view:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan event:cache
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan filament:cache-components
	@if command -v $(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan icons:cache >/dev/null 2>&1; then \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan icons:cache; \
	fi
	@echo "$(GREEN)✅ Cache completo aplicado$(RESET)"

.PHONY: cache-clear
cache-clear: ## 🧹 Limpiar todos los caches
	@echo "$(YELLOW)🧹 Limpiando caches...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan optimize:clear
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan filament:clear-cached-components
	@echo "$(GREEN)✅ Caches limpiados$(RESET)"

.PHONY: optimize-laravel
optimize-laravel: ## 🏎️ Optimizaciones específicas Laravel
	@echo "$(YELLOW)🏎️ Optimizando Laravel...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan optimize
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan storage:link
	@if [ "$(CURRENT_ENV)" = "prod" ]; then \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan queue:restart; \
	fi
	@echo "$(GREEN)✅ Laravel optimizado$(RESET)"

.PHONY: optimize-filament
optimize-filament: ## 🎨 Optimizaciones específicas Filament
	@echo "$(YELLOW)🎨 Optimizando Filament...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan filament:optimize
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan vendor:publish --tag=filament-assets --force
	@echo "$(GREEN)✅ Filament optimizado$(RESET)"

.PHONY: optimize-octane
optimize-octane: ## ⚡ Optimizaciones Octane/FrankenPHP
	@echo "$(YELLOW)⚡ Optimizando Octane...$(RESET)"
	@if [ "$(CURRENT_ENV)" != "hybrid" ]; then \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan octane:reload; \
	fi
	@echo "$(GREEN)✅ Octane optimizado$(RESET)"

.PHONY: optimize-composer
optimize-composer: ## 📦 Optimizar autoloader de Composer
	@echo "$(YELLOW)📦 Optimizando Composer...$(RESET)"
	@if [ "$(CURRENT_ENV)" = "prod" ]; then \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer install --no-dev --optimize-autoloader; \
	else \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer install --optimize-autoloader; \
	fi
	@echo "$(GREEN)✅ Composer optimizado$(RESET)"

# ==========================================
# MONITORING Y PERFORMANCE
# ==========================================

.PHONY: benchmark
benchmark: ## 📊 Benchmark de rendimiento
	@echo "$(BLUE)📊 Ejecutando benchmark...$(RESET)"
	@echo "$(YELLOW)Testing response time...$(RESET)"
	@curl -o /dev/null -s -w "Response time: %{time_total}s\nHTTP status: %{http_code}\n" http://localhost:8080/
	@echo "$(YELLOW)Testing concurrent requests...$(RESET)"
	@ab -n 100 -c 10 http://localhost:8080/ | grep -E "(Requests per second|Time per request)"

.PHONY: performance
performance: ## 🏎️ Análisis completo de performance
	@echo "$(BOLD)$(BLUE)🏎️ ANÁLISIS DE PERFORMANCE$(RESET)"
	@echo ""
	@echo "$(CYAN)1. Memory Usage:$(RESET)"
	@docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}" | grep nfc
	@echo ""
	@echo "$(CYAN)2. Octane Status:$(RESET)"
	@$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan octane:status || echo "Octane no disponible"
	@echo ""
	@echo "$(CYAN)3. Cache Status:$(RESET)"
	@$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan config:show cache || echo "Cache info no disponible"
	@echo ""
	@echo "$(CYAN)4. Queue Status:$(RESET)"
	@$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan queue:monitor redis:default --max-time=30 || echo "Queue no disponible"

.PHONY: monitor
monitor: ## 📊 Monitoreo en tiempo real
	@echo "$(BLUE)📊 Monitoreando contenedores...$(RESET)"
	@echo "$(YELLOW)Presiona Ctrl+C para salir$(RESET)"
	docker stats $(shell docker ps --filter "name=nfc-" --format "{{.Names}}")

.PHONY: health
health: ## 🏥 Verificar salud completa del sistema
	@echo "$(BLUE)🏥 Verificando salud del sistema...$(RESET)"
	@echo ""
	@echo "$(CYAN)Contenedores:$(RESET)"
	@docker ps --filter "name=nfc-" --format "  ✓ {{.Names}} - {{.Status}}"
	@echo ""
	@echo "$(CYAN)Servicios web:$(RESET)"
	@curl -s http://localhost:8080/health -w "  ✓ App: %{http_code} (%{time_total}s)\n" -o /dev/null || echo "  ❌ App: No disponible"
	@echo ""
	@echo "$(CYAN)Base de datos:$(RESET)"
	@$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan db:monitor --max-time=1000 || echo "  ❌ DB: Error de conexión"

# ==========================================
# BASE DE DATOS OPTIMIZADA
# ==========================================

.PHONY: migrate
migrate: ## 🗄️ Migrar base de datos (detecta entorno)
	@echo "$(BLUE)🗄️ Ejecutando migraciones en $(CURRENT_ENV)...$(RESET)"
	@if [ "$(CURRENT_ENV)" = "prod" ]; then \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate --force; \
	else \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate; \
	fi

.PHONY: migrate-fresh-safe
migrate-fresh-safe: ## 🔄 Reset seguro de base de datos
	@if [ "$(CURRENT_ENV)" = "prod" ]; then \
		echo "$(RED)❌ PELIGRO: Reset en producción bloqueado$(RESET)"; \
		echo "$(YELLOW)Usa 'make migrate-fresh-force' si estás seguro$(RESET)"; \
		exit 1; \
	fi
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate:fresh --seed

.PHONY: migrate-fresh-force
migrate-fresh-force: ## ⚠️ Reset FORZADO de base de datos (PELIGROSO)
	@echo "$(RED)⚠️ PELIGRO: Esto borrará TODA la base de datos!$(RESET)"
	@echo "$(YELLOW)Escribe 'DELETE_ALL_DATA' para continuar:$(RESET)"
	@read confirm && [ "$$confirm" = "DELETE_ALL_DATA" ] && \
		$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate:fresh --seed --force

.PHONY: db-backup
db-backup: ## 💾 Backup inteligente de base de datos
	@echo "$(YELLOW)💾 Creando backup de base de datos...$(RESET)"
	@mkdir -p backups
	@TIMESTAMP=$$(date +%Y%m%d-%H%M%S) && \
	if [ "$(CURRENT_ENV)" = "prod" ]; then \
		$(ACTIVE_COMPOSE) exec mysql mysqldump -u root -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE > backups/prod-db-$$TIMESTAMP.sql; \
	else \
		$(ACTIVE_COMPOSE) exec mysql mysqldump -u root -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE > backups/dev-db-$$TIMESTAMP.sql; \
	fi
	@echo "$(GREEN)✅ Backup completado en backups/$(RESET)"

.PHONY: db-optimize
db-optimize: ## 🏎️ Optimizar base de datos
	@echo "$(YELLOW)🏎️ Optimizando base de datos...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan db:wipe --drop-views
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan migrate --force
	$(ACTIVE_COMPOSE) exec mysql mysql -u root -p$$MYSQL_ROOT_PASSWORD -e "OPTIMIZE TABLE $$MYSQL_DATABASE.*;" $$MYSQL_DATABASE
	@echo "$(GREEN)✅ Base de datos optimizada$(RESET)"

# ==========================================
# DEVELOPMENT TOOLS OPTIMIZADOS
# ==========================================

.PHONY: dev-setup
dev-setup: ## 🛠️ Setup completo de desarrollo
	@echo "$(BLUE)🛠️ Setup completo de desarrollo...$(RESET)"
	$(MAKE) dev
	@sleep 5
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer install
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) npm install
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) npm run build
	$(MAKE) migrate
	$(MAKE) cache-clear
	@echo "$(GREEN)✅ Setup de desarrollo completado$(RESET)"

.PHONY: test-full
test-full: ## 🧪 Suite completa de tests
	@echo "$(BLUE)🧪 Ejecutando suite completa de tests...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan test --parallel
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) ./vendor/bin/php-cs-fixer fix --dry-run --diff || true
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) npm run test || true
	@echo "$(GREEN)✅ Tests completados$(RESET)"

.PHONY: lint-fix
lint-fix: ## 🔧 Arreglar código automáticamente
	@echo "$(YELLOW)🔧 Arreglando código...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer cs-fix
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) npm run lint:fix || true
	@echo "$(GREEN)✅ Código arreglado$(RESET)"

.PHONY: quality-check
quality-check: ## 🎯 Verificar calidad completa del código
	@echo "$(BOLD)$(BLUE)🎯 VERIFICACIÓN DE CALIDAD COMPLETA$(RESET)"
	@echo ""
	@echo "$(CYAN)1. Code Style Check:$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer cs-check || true
	@echo ""
	@echo "$(CYAN)2. Static Analysis:$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer phpstan || true
	@echo ""
	@echo "$(CYAN)3. Security Audit:$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer security || true
	@echo ""
	@echo "$(CYAN)4. Test Coverage:$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer test-coverage || true
	@echo "$(GREEN)✅ Verificación de calidad completada$(RESET)"

.PHONY: fix-all
fix-all: ## ⚡ Arreglar todos los problemas automáticamente
	@echo "$(YELLOW)⚡ Arreglando todos los problemas...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer cs-fix
	$(MAKE) cache-clear
	$(MAKE) optimize
	@echo "$(GREEN)✅ Todos los arreglos aplicados$(RESET)"

# ==========================================
# PRODUCTION DEPLOYMENT OPTIMIZADO
# ==========================================

.PHONY: deploy
deploy: pre-deploy prod-build-optimized prod backup-before-deploy ## 🚀 Deployment completo a producción
	@echo "$(BOLD)$(GREEN)🚀 Deployment completado exitosamente$(RESET)"

.PHONY: deploy-prod
deploy-prod: ## 🏭 Deployment automatizado con script optimizado
	@echo "$(BOLD)$(RED)🏭 Iniciando deployment automatizado a producción...$(RESET)"
	@if [ ! -f "./deploy-prod.sh" ]; then \
		echo "$(RED)❌ Script deploy-prod.sh no encontrado$(RESET)"; \
		exit 1; \
	fi
	@chmod +x ./deploy-prod.sh
	./deploy-prod.sh
	@echo "$(BOLD)$(GREEN)🎉 Deployment automatizado completado$(RESET)"

.PHONY: deploy-prod-quick
deploy-prod-quick: ## ⚡ Deployment rápido sin preguntas interactivas
	@echo "$(BOLD)$(YELLOW)⚡ Deployment rápido a producción...$(RESET)"
	@if [ ! -f ".env.prod" ]; then \
		echo "$(RED)❌ Archivo .env.prod no encontrado$(RESET)"; \
		echo "$(BLUE)💡 Copia .env.prod.example y configúralo$(RESET)"; \
		exit 1; \
	fi
	@echo "$(BLUE)🔍 Verificando prerequisitos...$(RESET)"
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod config > /dev/null
	@echo "$(BLUE)🏗️ Construyendo imágenes...$(RESET)"
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod build --no-cache
	@echo "$(BLUE)🚀 Desplegando servicios...$(RESET)"
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod down --remove-orphans
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod up -d
	@echo "$(BLUE)⚡ Optimizando Laravel...$(RESET)"
	@sleep 10
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod exec php-fpm php artisan config:cache
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod exec php-fpm php artisan route:cache
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod exec php-fpm php artisan view:cache
	@echo "$(GREEN)✅ Deployment rápido completado$(RESET)"

.PHONY: deploy-prod-check
deploy-prod-check: ## 🔍 Verificar deployment de producción
	@echo "$(BLUE)🔍 Verificando deployment de producción...$(RESET)"
	@if [ ! -f ".env.prod" ]; then \
		echo "$(RED)❌ Archivo .env.prod no encontrado$(RESET)"; \
		exit 1; \
	fi
	@source .env.prod && \
	echo "$(CYAN)Checking services...$(RESET)" && \
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod ps && \
	echo "" && \
	echo "$(CYAN)Testing health endpoint...$(RESET)" && \
	curl -f -s "$${APP_URL:-http://localhost}/health" && echo "  ✅ Health OK" || echo "  ❌ Health failed" && \
	echo "" && \
	echo "$(CYAN)Checking Laravel status...$(RESET)" && \
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod exec php-fpm php artisan about --only=environment,cache,queue

.PHONY: deploy-prod-logs
deploy-prod-logs: ## 📋 Ver logs del deployment de producción
	@echo "$(BLUE)📋 Logs de producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod logs -f --tail=50

.PHONY: deploy-prod-shell
deploy-prod-shell: ## 💻 Acceder al shell de producción
	@echo "$(BLUE)💻 Accediendo al shell de producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod exec php-fpm bash

.PHONY: deploy-prod-down
deploy-prod-down: ## 🛑 Parar deployment de producción
	@echo "$(YELLOW)🛑 Parando deployment de producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) --env-file .env.prod down
	@echo "$(GREEN)✅ Producción parada$(RESET)"

.PHONY: deploy-prod-backup
deploy-prod-backup: ## 💾 Backup específico de producción
	@echo "$(YELLOW)💾 Creando backup de producción...$(RESET)"
	@mkdir -p backups/prod
	@TIMESTAMP=$$(date +%Y%m%d-%H%M%S) && \
	if $(DOCKER_COMPOSE_PROD) --env-file .env.prod ps | grep -q redis; then \
		echo "$(BLUE)📦 Backing up Redis...$(RESET)"; \
		$(DOCKER_COMPOSE_PROD) --env-file .env.prod exec redis redis-cli --rdb /tmp/backup.rdb && \
		$(DOCKER_COMPOSE_PROD) --env-file .env.prod cp redis:/tmp/backup.rdb ./backups/prod/redis-$$TIMESTAMP.rdb; \
	fi
	@echo "$(BLUE)📁 Backing up storage...$(RESET)"
	@tar -czf ./backups/prod/storage-$$TIMESTAMP.tar.gz ./storage
	@echo "$(GREEN)✅ Backup de producción completado en backups/prod/$(RESET)"

.PHONY: pre-deploy
pre-deploy: ## 🔍 Verificaciones pre-deployment
	@echo "$(YELLOW)🔍 Verificaciones pre-deployment...$(RESET)"
	$(MAKE) test-full
	$(MAKE) cache-clear
	@if git status --porcelain | grep -q .; then \
		echo "$(RED)❌ Hay cambios sin commit$(RESET)" && exit 1; \
	fi
	@echo "$(GREEN)✅ Pre-deployment verificado$(RESET)"

.PHONY: prod-build-optimized
prod-build-optimized: ## 🏭 Build optimizado para producción
	@echo "$(RED)🏭 Build optimizado para producción...$(RESET)"
	$(DOCKER_COMPOSE_PROD) build --parallel --no-cache
	@echo "$(GREEN)✅ Build de producción completado$(RESET)"

.PHONY: backup-before-deploy
backup-before-deploy: ## 💾 Backup automático antes de deployment
	@echo "$(YELLOW)💾 Backup pre-deployment...$(RESET)"
	$(MAKE) db-backup
	@echo "$(GREEN)✅ Backup pre-deployment completado$(RESET)"

.PHONY: rollback
rollback: ## 🔄 Rollback rápido
	@echo "$(RED)🔄 Ejecutando rollback...$(RESET)"
	@echo "$(YELLOW)Listando backups disponibles:$(RESET)"
	@ls -la backups/ | tail -5
	@echo "$(YELLOW)Ingresa el nombre del backup para restaurar:$(RESET)"
	@read backup && \
	$(ACTIVE_COMPOSE) exec mysql mysql -u root -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE < backups/$$backup
	@echo "$(GREEN)✅ Rollback completado$(RESET)"

# ==========================================
# UTILITIES OPTIMIZADAS
# ==========================================

.PHONY: clean-all
clean-all: ## 🧹 Limpieza completa del sistema
	@echo "$(YELLOW)🧹 Limpieza completa...$(RESET)"
	$(DOCKER_COMPOSE_DEV) down -v --remove-orphans 2>/dev/null || true
	$(DOCKER_COMPOSE_PROD) down -v --remove-orphans 2>/dev/null || true
	$(DOCKER_COMPOSE_HYBRID) down -v --remove-orphans 2>/dev/null || true
	docker system prune -af --volumes
	@echo "$(GREEN)✅ Limpieza completada$(RESET)"

.PHONY: status
status: ## 📋 Estado completo del sistema
	@echo "$(BOLD)$(BLUE)📋 ESTADO DEL SISTEMA$(RESET)"
	@echo ""
	@echo "$(CYAN)Entorno actual: $(BOLD)$(CURRENT_ENV)$(RESET)"
	@echo ""
	@echo "$(CYAN)Contenedores:$(RESET)"
	@docker ps -a --filter "name=nfc-" --format "  {{.Names}} - {{.Status}}" || echo "  No hay contenedores"
	@echo ""
	@echo "$(CYAN)Imágenes:$(RESET)"
	@docker images --filter "reference=kraftdo-nfc*" --format "  {{.Repository}}:{{.Tag}} - {{.Size}}" || echo "  No hay imágenes"
	@echo ""
	@echo "$(CYAN)Volúmenes:$(RESET)"
	@docker volume ls --filter "name=kraftdo" --format "  {{.Name}}" || echo "  No hay volúmenes"

.PHONY: debug
debug: ## 🐛 Información completa de debug
	@echo "$(BOLD)$(CYAN)🐛 INFORMACIÓN DE DEBUG$(RESET)"
	@echo ""
	@echo "$(CYAN)Sistema:$(RESET)"
	@echo "  Docker: $(shell docker --version)"
	@echo "  Compose: $(shell docker compose version)"
	@echo "  Sistema: $(shell uname -a)"
	@echo ""
	@echo "$(CYAN)Proyecto:$(RESET)"
	@echo "  Directorio: $(PWD)"
	@echo "  Entorno detectado: $(CURRENT_ENV)"
	@echo "  Compose activo: $(ACTIVE_COMPOSE)"
	@echo ""
	@echo "$(CYAN)Logs recientes:$(RESET)"
	@$(ACTIVE_COMPOSE) logs --tail=10 app 2>/dev/null || echo "  No hay logs disponibles"

# ==========================================
# SHORTCUTS Y ALIASES OPTIMIZADOS
# ==========================================

.PHONY: up
up: ## 🚀 Iniciar (detecta mejor configuración)
	@if [ -f "docker-compose.hybrid.yml" ]; then \
		$(MAKE) hybrid; \
	elif [ -f ".env" ] && grep -q "APP_ENV=local" .env; then \
		$(MAKE) dev; \
	else \
		$(MAKE) prod; \
	fi

.PHONY: down
down: ## 🛑 Parar todos los contenedores
	@echo "$(YELLOW)🛑 Parando todos los contenedores...$(RESET)"
	$(DOCKER_COMPOSE_HYBRID) down 2>/dev/null || true
	$(DOCKER_COMPOSE_DEV) down 2>/dev/null || true
	$(DOCKER_COMPOSE_PROD) down 2>/dev/null || true
	@echo "$(GREEN)✅ Contenedores parados$(RESET)"

.PHONY: restart
restart: ## 🔄 Reinicio inteligente
	@echo "$(YELLOW)🔄 Reiniciando $(CURRENT_ENV)...$(RESET)"
	$(ACTIVE_COMPOSE) restart
	@sleep 5
	$(MAKE) health

.PHONY: shell
shell: ## 💻 Shell inteligente (detecta entorno)
	@echo "$(BLUE)💻 Accediendo a shell de $(CURRENT_ENV)...$(RESET)"
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) sh

.PHONY: logs
logs: ## 📝 Logs inteligentes (detecta entorno)
	$(ACTIVE_COMPOSE) logs -f

.PHONY: quick-fix
quick-fix: ## ⚡ Arreglo rápido de problemas comunes
	@echo "$(YELLOW)⚡ Aplicando arreglos rápidos...$(RESET)"
	$(MAKE) cache-clear
	$(MAKE) optimize-composer
	$(ACTIVE_COMPOSE) restart app
	@echo "$(GREEN)✅ Arreglos aplicados$(RESET)"

# ==========================================
# COMANDOS HÍBRIDOS (CONFIGURACIÓN ACTUAL)
# ==========================================

.PHONY: hybrid
hybrid: ## 🔄 Iniciar configuración híbrida optimizada
	@echo "$(BLUE)🔄 Iniciando híbrido OPTIMIZADO...$(RESET)"
	$(DOCKER_COMPOSE_HYBRID) up -d
	@sleep 5
	$(MAKE) cache-clear
	@echo "$(GREEN)✅ Híbrido optimizado en http://localhost:8080$(RESET)"

# Target por defecto
.DEFAULT_GOAL := help

# ==========================================
# CI/CD SPECIFIC COMMANDS
# ==========================================

.PHONY: ci-install
ci-install: ## 📦 CI: Install dependencies optimized for CI
	@echo "$(BLUE)📦 Installing CI dependencies...$(RESET)"
	composer install --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist --optimize-autoloader

.PHONY: ci-setup
ci-setup: ## 🔧 CI: Setup environment for testing
	@echo "$(BLUE)🔧 Setting up CI environment...$(RESET)"
	php artisan key:generate --force
	chmod -R 755 storage bootstrap/cache
	mkdir -p database
	touch database/database.sqlite
	php artisan migrate --env=testing --database=sqlite --force

.PHONY: ci-build-assets
ci-build-assets: ## 🏗️ CI: Build frontend assets
	@echo "$(BLUE)🏗️ Building frontend assets...$(RESET)"
	npm ci --prefer-offline --no-audit
	npm run build

.PHONY: ci-test-unit
ci-test-unit: ## 🧪 CI: Run unit tests with coverage
	@echo "$(BLUE)🧪 Running unit tests with coverage...$(RESET)"
	vendor/bin/pest tests/Unit/ --coverage --min=90 --coverage-clover=coverage.xml

.PHONY: ci-test-unit-dev
ci-test-unit-dev: ## 🧪 CI: Run unit tests with coverage (dev threshold)
	@echo "$(BLUE)🧪 Running unit tests with coverage (dev threshold)...$(RESET)"
	vendor/bin/pest tests/Unit/ --coverage --min=80 --coverage-clover=coverage.xml

.PHONY: ci-test-feature
ci-test-feature: ## 🧪 CI: Run feature tests
	@echo "$(BLUE)🧪 Running feature tests...$(RESET)"
	vendor/bin/pest tests/Feature/ --stop-on-failure

.PHONY: ci-test-full
ci-test-full: ci-test-unit ci-test-feature ## 🧪 CI: Run all tests
	@echo "$(GREEN)✅ All CI tests completed$(RESET)"

.PHONY: ci-quality-checks
ci-quality-checks: ## 🔍 CI: Run code quality checks
	@echo "$(BLUE)🔍 Running quality checks...$(RESET)"
	composer cs-check || true
	composer phpstan || true

.PHONY: ci-security-check
ci-security-check: ## 🔒 CI: Run security checks
	@echo "$(BLUE)🔒 Running security checks...$(RESET)"
	composer security || true

.PHONY: ci-quality-full
ci-quality-full: ## 🎯 CI: Run all quality checks
	@echo "$(BLUE)🎯 Running full quality suite...$(RESET)"
	composer quality || true

# ==========================================
# COMANDOS AVANZADOS DE ARTISAN
# ==========================================

artisan-%: ## 🎯 Ejecutar cualquier comando artisan (ej: make artisan-migrate)
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) php artisan $*

composer-%: ## 📦 Ejecutar cualquier comando composer (ej: make composer-install)
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) composer $*

npm-%: ## 📦 Ejecutar cualquier comando npm (ej: make npm-install)
	$(ACTIVE_COMPOSE) exec $(SERVICE_NAME) npm $*