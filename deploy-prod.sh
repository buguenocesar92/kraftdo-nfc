#!/bin/bash
# ===========================================
# KRAFTDO NFC - PRODUCTION DEPLOYMENT SCRIPT
# ===========================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
COMPOSE_FILE="docker-compose.prod.yml"
ENV_FILE=".env.prod"
BACKUP_DIR="./backups/$(date +%Y%m%d_%H%M%S)"

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root (not recommended for production)
if [[ $EUID -eq 0 ]]; then
   log_warning "Running as root is not recommended for production deployment"
   read -p "Do you want to continue? (y/N): " -n 1 -r
   echo
   if [[ ! $REPLY =~ ^[Yy]$ ]]; then
       exit 1
   fi
fi

# Check prerequisites
log_info "Checking prerequisites..."

if ! command -v docker &> /dev/null; then
    log_error "Docker is not installed or not in PATH"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    log_error "Docker Compose is not installed or not in PATH"
    exit 1
fi

if [ ! -f "$ENV_FILE" ]; then
    log_error "Environment file $ENV_FILE not found!"
    log_info "Please copy .env.prod.example to $ENV_FILE and configure it"
    exit 1
fi

if [ ! -f "$COMPOSE_FILE" ]; then
    log_error "Docker Compose file $COMPOSE_FILE not found!"
    exit 1
fi

log_success "Prerequisites check passed"

# Load environment variables
log_info "Loading environment variables from $ENV_FILE..."
source $ENV_FILE
export $(grep -v '^#' $ENV_FILE | cut -d= -f1)

# Validate required environment variables
REQUIRED_VARS=(
    "APP_KEY"
    "APP_URL"
    "SESSION_DOMAIN"
    "REDIS_PASSWORD"
)

for var in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!var}" ]; then
        log_error "Required environment variable $var is not set"
        exit 1
    fi
done

log_success "Environment variables validated"

# Create necessary directories
log_info "Creating necessary directories..."
mkdir -p ./docker/data/redis
mkdir -p ./storage/logs
mkdir -p ./bootstrap/cache
mkdir -p $BACKUP_DIR

# Set proper permissions
chmod -R 755 ./storage
chmod -R 755 ./bootstrap/cache

log_success "Directory structure created"

# Backup current deployment (if exists)
if docker-compose -f $COMPOSE_FILE ps | grep -q "Up"; then
    log_info "Creating backup of current deployment..."
    
    # Backup Redis data
    docker-compose -f $COMPOSE_FILE exec redis redis-cli --rdb $BACKUP_DIR/redis_backup.rdb || true
    
    # Backup storage
    cp -r ./storage $BACKUP_DIR/ || true
    
    log_success "Backup created at $BACKUP_DIR"
fi

# Pull latest images
log_info "Pulling latest Docker images..."
docker-compose -f $COMPOSE_FILE pull

# Build and start services
log_info "Building and starting services..."
docker-compose -f $COMPOSE_FILE down --remove-orphans
docker-compose -f $COMPOSE_FILE build --no-cache
docker-compose -f $COMPOSE_FILE up -d

# Wait for services to be healthy
log_info "Waiting for services to be healthy..."
timeout=300  # 5 minutes timeout
elapsed=0
interval=10

while [ $elapsed -lt $timeout ]; do
    if docker-compose -f $COMPOSE_FILE ps | grep -E "(unhealthy|starting)" > /dev/null; then
        log_info "Services still starting... ($elapsed/$timeout seconds)"
        sleep $interval
        elapsed=$((elapsed + interval))
    else
        break
    fi
done

# Check if all services are healthy
if docker-compose -f $COMPOSE_FILE ps | grep -E "(unhealthy|exited)" > /dev/null; then
    log_error "Some services failed to start properly"
    docker-compose -f $COMPOSE_FILE ps
    exit 1
fi

log_success "All services are running"

# Run Laravel optimizations
log_info "Running Laravel optimizations..."
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan config:cache
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan route:cache
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan view:cache
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan event:cache

# Run database migrations (only if needed)
read -p "Do you want to run database migrations? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    log_info "Running database migrations..."
    docker-compose -f $COMPOSE_FILE exec php-fpm php artisan migrate --force
    log_success "Database migrations completed"
fi

# Clear and warm up cache
log_info "Warming up application cache..."
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan cache:clear
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan config:cache
docker-compose -f $COMPOSE_FILE exec php-fpm php artisan route:cache

# Test application health
log_info "Testing application health..."
sleep 10  # Wait for application to fully start

APP_URL_CHECK="${APP_URL:-http://localhost}"
if curl -f -s "$APP_URL_CHECK/health" > /dev/null; then
    log_success "Application health check passed"
else
    log_warning "Application health check failed - please verify manually"
fi

# Show deployment summary
log_success "🚀 Production deployment completed successfully!"
echo
echo "=== Deployment Summary ==="
echo "Environment: production"
echo "Services: $(docker-compose -f $COMPOSE_FILE ps --services | wc -l)"
echo "URL: $APP_URL"
echo "Backup: $BACKUP_DIR"
echo

# Show running services
echo "=== Running Services ==="
docker-compose -f $COMPOSE_FILE ps

# Show useful commands
echo
echo "=== Useful Commands ==="
echo "View logs: docker-compose -f $COMPOSE_FILE logs -f [service]"
echo "Shell access: docker-compose -f $COMPOSE_FILE exec php-fpm bash"
echo "Stop services: docker-compose -f $COMPOSE_FILE down"
echo "Update deployment: ./deploy-prod.sh"
echo

log_success "Deployment script completed successfully!"