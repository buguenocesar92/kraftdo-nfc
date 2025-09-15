# Dockerfile optimizado para Laravel NFC con FrankenPHP + Octane
FROM dunglas/frankenphp:php8.3-alpine AS base

# Instalar dependencias del sistema
RUN apk add --no-cache \
    curl \
    git \
    sqlite \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mysql-client \
    redis \
    netcat-openbsd \
    bash

# Instalar extensiones PHP necesarias para Laravel + Octane
RUN install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    gd \
    zip \
    mbstring \
    intl \
    bcmath \
    pcntl \
    posix \
    sockets \
    redis \
    opcache \
    exif

# Configurar PHP para Octane
RUN echo "memory_limit=512M" >> /usr/local/etc/php/php.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/php.ini && \
    echo "upload_max_filesize=50M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size=50M" >> /usr/local/etc/php/php.ini

# Configurar OPcache para producción con Octane
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar script de inicio en stage base (necesario para desarrollo)
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# ==========================================
# STAGE PARA DEPENDENCIAS PHP
# ==========================================
FROM base AS php-deps
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    && composer clear-cache

# ==========================================
# STAGE PARA ASSETS NODE.JS
# ==========================================
FROM node:20-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY . .
RUN npm run build && npm cache clean --force

# ==========================================
# STAGE FINAL OPTIMIZADO PARA OCTANE
# ==========================================
FROM base AS final

# Copiar dependencias y assets compilados
COPY --from=php-deps /app/vendor ./vendor
COPY --from=node-build /app/public/build ./public/build

# Copiar código fuente
COPY . .

# Instalar Laravel Octane si no está presente
RUN if ! php artisan list | grep -q octane; then \
        composer require laravel/octane --no-interaction; \
    fi

# Ejecutar scripts post-install
RUN composer run-script post-autoload-dump || true

# Crear directorios y configurar permisos
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database \
    && chmod -R 775 storage bootstrap/cache \
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

# Crear enlace simbólico de storage correctamente para FrankenPHP
RUN ln -sf /app/storage/app/public /app/public/storage

# Copiar configuración de FrankenPHP
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Copiar y configurar script de inicio
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Variables de entorno por defecto para Octane
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    OCTANE_SERVER=frankenphp \
    OCTANE_HOST=0.0.0.0 \
    OCTANE_PORT=80 \
    OCTANE_WORKERS=auto \
    OCTANE_MAX_REQUESTS=500 \
    SERVER_NAME=:80 \
    CADDY_GLOBAL_OPTIONS=""

# Health check optimizado para Octane
HEALTHCHECK --interval=30s --timeout=10s --start-period=90s --retries=3 \
    CMD curl -f http://localhost/health || curl -f http://localhost/ || exit 1

EXPOSE 80 443

# Usar nuestro script de inicio optimizado para Octane
ENTRYPOINT ["/usr/local/bin/start.sh"]