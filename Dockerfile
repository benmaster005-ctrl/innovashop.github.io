###################
# Stage 1 — JS Build (Webpack Encore)
###################
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

###################
# Stage 2 — PHP Dependencies
###################
FROM composer:2.7 AS composer_builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs \
    --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

###################
# Stage 3 — Production Image
###################
FROM php:8.2-fpm-alpine AS production

# Extensions système nécessaires
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        opcache \
        mbstring \
    && rm -rf /var/cache/apk/*

# Configuration OPcache pour la production
RUN { \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.jit_buffer_size=256M'; \
    echo 'opcache.jit=1255'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# PHP config production
RUN { \
    echo 'expose_php=Off'; \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'max_execution_time=60'; \
    echo 'date.timezone=UTC'; \
} > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

# Copie des vendors (Composer)
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=composer_builder /app/composer.json ./composer.json
COPY --from=composer_builder /app/composer.lock ./composer.lock

# Copie des assets compilés (Webpack Encore)
COPY --from=node_builder /app/public/build ./public/build

# Copie du reste du projet
COPY . .

# Nettoyage & permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

# Config Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Config Supervisor (gère PHP-FPM + Nginx)
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script d'entrée
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
