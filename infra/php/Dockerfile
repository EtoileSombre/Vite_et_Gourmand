FROM php:8.3-apache

# Dépendances pour extensions PHP
RUN apt-get update && apt-get install -y \
    git zip unzip libicu-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libzip-dev pkg-config libssl-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo_mysql mysqli intl gd opcache zip

# Apache + timezone (adapter si tu n'as pas /public)
RUN a2enmod rewrite \
 && sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
 && printf '<Directory "/var/www/html/public">\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/project.conf \
 && a2enconf project

RUN echo "date.timezone=Europe/Paris" > /usr/local/etc/php/conf.d/timezone.ini

WORKDIR /var/www/html
