FROM php:8.2-cli
WORKDIR /app
COPY ..
RUN apt-get update && apt-get install -y \
  git \
  unzip \
  libzip-dev \
  && docker-php-ext-install zip
COPY --from=composer:latest /usr/bin/
composer /usr/bin/composer
RUN composer install
CMD php -S 0.0.0.0:$PORT -t public
