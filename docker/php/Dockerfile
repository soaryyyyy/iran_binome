FROM php:8.2-apache

# Installer extensions nécessaires pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Activer mod_rewrite
RUN a2enmod rewrite

# Autoriser .htaccess (AllowOverride All)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Définir le dossier de travail
WORKDIR /var/www/html