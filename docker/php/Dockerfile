FROM php:8.2-apache

# Installer extensions nécessaires pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Activer mod_rewrite (optionnel)
RUN a2enmod rewrite

# Définir le dossier de travail
WORKDIR /var/www/html