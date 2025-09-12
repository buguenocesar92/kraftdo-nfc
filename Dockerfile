# Multi-stage build optimizado para Laravel NFC App con soporte multi-arch y Octane
# Stage 1: Base image con extensiones PHP (PHP 8.3 CLI para Octane)
FROM --platform=${BUILDPLATFORM:-linux/amd64} php:8.3-cli-alpine AS base

# Variables para soporte multi-arch
ARG TARGETPLATFORM
ARG BUILDPLATFORM
ARG TARGETOS
ARG TARGETARCH

# Instalar dependencias del sistema y extensiones PHP incluyendo Swoole
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        linux-headers \
        gcc \
        g++ \
        make \
        autoconf \
    && apk add --no-cache \
        sqlite \
        sqlite-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        zip \
        libzip-dev \
        oniguruma-dev \
        curl-dev \
        icu-dev \
        libxml2-dev \
        postgresql-dev \
        openssl-dev \
        c-ares-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        gd \
        zip \
        mbstring \
        curl \
        opcache \
        intl \
        xml \
        soap \
        bcmath \
        pcntl \
        posix \
        sockets \
    && pecl install redis swoole \
    && docker-php-ext-enable redis swoole \
    && echo "swoole.use_shortname=off" >> /usr/local/etc/php/conf.d/swoole.ini \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

# Configurar PHP optimizado para producción
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configuraciones adicionales de seguridad
RUN echo 'expose_php = Off' >> /usr/local/etc/php/conf.d/security.ini \
    && echo 'display_errors = Off' >> /usr/local/etc/php/conf.d/security.ini \
    && echo 'log_errors = On' >> /usr/local/etc/php/conf.d/security.ini \
    && echo 'error_log = /var/log/php_errors.log' >> /usr/local/etc/php/conf.d/security.ini

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Stage 2: Dependencias PHP optimizadas
FROM base AS php-deps

WORKDIR /app

# Copiar archivos de dependencias primero para mejor cache
COPY composer.json composer.lock ./

# Instalar dependencias con optimizaciones avanzadas
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --classmap-authoritative \
        --no-scripts \
        --no-interaction \
        --prefer-dist \
    && composer clear-cache

# Stage 3: Assets build optimizado con soporte multi-arch
FROM --platform=${BUILDPLATFORM:-linux/amd64} node:20-alpine AS node-build

WORKDIR /app

# Copiar archivos de configuración primero para mejor cache
COPY package.json package-lock.json ./
COPY vite.config.js tailwind.config.js postcss.config.js ./

# Instalar dependencias de Node (cache layer separado)
RUN npm ci --only=production --no-audit --no-fund

# Copiar archivos fuente solo cuando sea necesario
COPY resources/ ./resources/
COPY public/ ./public/

# Build de assets con optimizaciones
RUN npm run build && npm cache clean --force

# Stage 4: Imagen final optimizada
FROM base AS final

# Instalar solo dependencias de runtime necesarias para Octane
RUN apk add --no-cache \
        nginx \
        supervisor \
        git \
        netcat-openbsd \
        shadow \
        bash \
        dcron \
        logrotate \
        tini \
    && rm -rf /var/cache/apk/*

# Configurar Nginx y Supervisor
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear usuario de aplicación
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -D -S -G www www

WORKDIR /var/www/html

# Copiar dependencias primero (cambios menos frecuentes)
COPY --from=php-deps --chown=www:www /app/vendor ./vendor

# Copiar assets compilados (cambios menos frecuentes)
COPY --from=node-build --chown=www:www /app/public/build ./public/build

# Copiar archivos de configuración específicos primero
COPY --chown=www:www composer.json composer.lock package.json ./
COPY --chown=www:www artisan ./
COPY docker/healthcheck.php ./public/health.php

# Copiar código fuente al final (cambios más frecuentes)
COPY --chown=www:www app/ ./app/
COPY --chown=www:www bootstrap/ ./bootstrap/
COPY --chown=www:www config/ ./config/
COPY --chown=www:www database/ ./database/
COPY --chown=www:www public/ ./public/
COPY --chown=www:www resources/ ./resources/
COPY --chown=www:www routes/ ./routes/
COPY --chown=www:www storage/ ./storage/

# Ejecutar scripts de Composer
RUN composer run-script post-autoload-dump

# Configurar permisos con seguridad mejorada y directorios para Octane
RUN mkdir -p storage bootstrap/cache \
    && mkdir -p storage/app/octane-uploads \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx \
    && mkdir -p /var/lib/nginx/tmp/client_body \
    && mkdir -p /var/lib/nginx/tmp/fastcgi \
    && mkdir -p /var/lib/nginx/tmp/proxy \
    && mkdir -p /var/lib/nginx/tmp/scgi \
    && mkdir -p /var/lib/nginx/tmp/uwsgi \
    && chown -R www:www /var/www/html \
    && chown -R www:www /var/lib/nginx/tmp \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache storage/app/octane-uploads \
    && chmod -R 755 /var/lib/nginx/tmp \
    && chmod 755 /var/lib/nginx

# Variables de entorno optimizadas para Octane
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    OCTANE_SERVER=swoole \
    OCTANE_HOST=0.0.0.0 \
    OCTANE_PORT=8080 \
    OCTANE_WORKERS=auto

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:8080/health || exit 1

# Exponer puerto
EXPOSE 8080

# Script de inicio
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/usr/bin/tini", "--", "/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]