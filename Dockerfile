# =============================================================================
# KRAFTDO NFC - Imagen específica para el sistema NFC
# =============================================================================
# Extiende: kraftdo-base con funcionalidad específica del NFC

# -----------------------------------------------------------------------------
# Etapa 1: Construcción con Composer
# -----------------------------------------------------------------------------
FROM composer:2 AS composer-build
WORKDIR /app
# Copiar archivos de configuración de Composer
COPY composer.json composer.lock ./
# Instalar dependencias de PHP (solo producción) ignorando platform requirements
RUN composer install --no-dev --no-scripts --no-autoloader \
    --ignore-platform-req=ext-intl --ignore-platform-req=ext-sockets --ignore-platform-req=ext-redis
# Copiar código fuente y generar autoloader optimizado con archivos helpers
COPY . ./
# Generar autoloader final optimizado incluyendo helpers.php
RUN composer dump-autoload --optimize \
    --ignore-platform-req=ext-intl --ignore-platform-req=ext-sockets --ignore-platform-req=ext-redis

# Verificar que helpers.php está incluido en el autoloader
RUN echo "=== VERIFICANDO HELPERS.PHP EN AUTOLOADER ===" && \
    grep -l "app/helpers.php" vendor/composer/autoload_files.php && \
    echo "✅ helpers.php incluido correctamente" || \
    (echo "❌ helpers.php NO encontrado en autoloader" && exit 1)

# -----------------------------------------------------------------------------
# Etapa 2: Construcción de assets con Node.js (después de Composer)
# -----------------------------------------------------------------------------
FROM node:20-alpine AS node-build
WORKDIR /app
# Copiar archivos de configuración de Node.js
COPY package.json package-lock.json ./
# Instalar dependencias de Node.js (incluyendo dev para build)
RUN npm ci
# Copiar archivos necesarios para Vite y Tailwind
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/
# Copiar archivos Blade y PHP para que Tailwind los escanee
COPY app/ ./app/
# Copiar vendor desde la etapa de Composer (necesario para flux.css)
COPY --from=composer-build /app/vendor ./vendor

# Set NODE_ENV for production build
ENV NODE_ENV=production

# Construir assets con debugging
RUN echo "Building assets with Vite..." && \
    npm run build && \
    echo "Build completed, checking output..." && \
    ls -la public/

# Verificar que se generaron los assets
RUN ls -la public/build/ && cat public/build/manifest.json

# -----------------------------------------------------------------------------
# Etapa 3: Imagen final extendiendo kraftdo-base externa
# -----------------------------------------------------------------------------
FROM ghcr.io/buguenocesar92/kraftdo-base:develop

# Configurar PHP con archivos optimizados específicos del CMS
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configurar PHP-FPM con archivo optimizado
COPY docker/php-fpm/pool.conf /usr/local/etc/php-fpm.d/www.conf

# Configurar Nginx específico para NFC
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copiar código desde etapa de Composer
COPY --from=composer-build --chown=nginx:nginx /app ./

# Crear directorio build antes de copiar assets
RUN mkdir -p public/build

# Copiar assets construidos desde etapa de Node.js
COPY --from=node-build --chown=nginx:nginx /app/public/build/ ./public/build/

# Verificar que los assets se copiaron (debug)
RUN echo "=== VERIFICANDO ASSETS COPIADOS ===" && \
    ls -la public/build/ && \
    echo "=== CONTENIDO DEL MANIFEST ===" && \
    cat public/build/manifest.json && \
    echo "=== FIN VERIFICACION ===" || echo "Warning: No build assets found"

# Crear directorios necesarios y establecer permisos
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chown -R nginx:nginx storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Copiar entrypoint específico del NFC
COPY docker/entrypoint-staging.sh /entrypoint-staging.sh
RUN chmod +x /entrypoint-staging.sh

ENTRYPOINT ["/entrypoint-staging.sh"]