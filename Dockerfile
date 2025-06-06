FROM php:7.4-fpm-alpine

# RUN apt-get update && apt-get install -y \
#     git \
#     curl \
#     zip \
#     unzip \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www
RUN composer install
EXPOSE 7000

CMD ["php", "-S", "0.0.0:7000", "-t", "/var/www/public"]