# Dockerfile para Laravel con PHP-FPM
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
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
    bash \
    shadow

# Instalar extensiones PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
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
        exif \
        opcache

# Instalar Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Configurar PHP para desarrollo
RUN echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    && echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

# Configurar OPcache para desarrollo
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar usuario para desarrollo
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 9000 para PHP-FPM
EXPOSE 9000

# Script de inicio
COPY docker/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

CMD ["/usr/local/bin/entrypoint.sh"]