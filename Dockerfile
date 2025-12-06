# Utilise l'image officielle PHP + Apache
FROM php:8.2-apache

# Installer les dépendances nécessaires pour PostgreSQL + MySQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    default-mysql-client \
    default-libmysqlclient-dev \
    unzip \
    git \
    libyaml-dev \
    && pecl install yamL \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-enable yaml \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Donner les droits à Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
