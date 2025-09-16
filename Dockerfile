# Multi-stage build para Laravel NFC App
# Stage 1: Base image con extensiones PHP
FROM php:8.2-fpm-alpine AS base

# Instalar dependencias del sistema y extensiones PHP
RUN apk add --no-cache \
    sqlite \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    curl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        gd \
        zip \
        mbstring \
        curl \
        opcache

# Configurar PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Stage 2: Dependencias PHP
FROM base AS php-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Stage 3: Assets build
FROM node:18-alpine AS node-build

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY . ./
RUN npm run build

# Stage 4: Imagen final
FROM base AS final

# Instalar nginx, supervisor y git para runtime
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    netcat-openbsd

# Configurar Nginx y Supervisor
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear usuario de aplicación
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -D -S -G www www

WORKDIR /var/www/html

# Copiar dependencias de PHP desde stage anterior
COPY --from=php-deps --chown=www:www /app/vendor ./vendor

# Copiar assets compilados desde stage anterior
COPY --from=node-build --chown=www:www /app/public/build ./public/build

# Copiar código fuente
COPY --chown=www:www . ./

# Ejecutar scripts de Composer
RUN composer run-script post-autoload-dump

# Configurar permisos
RUN mkdir -p storage bootstrap/cache \
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
    && chmod -R 777 storage bootstrap/cache \
    && chmod -R 755 /var/lib/nginx/tmp \
    && chmod 755 /var/lib/nginx

# Variables de entorno
ENV APP_ENV=local
ENV APP_DEBUG=true

# Script de inicio
COPY docker/entrypoint-staging.sh /entrypoint-staging.sh
RUN chmod +x /entrypoint-staging.sh

# Exponer puerto 80 para Nginx
EXPOSE 80

ENTRYPOINT ["/entrypoint-staging.sh"]
