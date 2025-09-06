# Stage 1: Build
FROM composer:2 AS builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

COPY . .

# Stage 2: Runtime
FROM php:8.2-apache

# Instala extensões necessárias do PHP e utilitários
RUN apt-get update && apt-get install -y \
    bash git unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copia arquivos do build
COPY --from=builder /app /var/www/html

# Copia exemplo de .env e gera APP_KEY automaticamente
COPY .env.example .env
RUN php artisan key:generate --force

# Habilita mod_rewrite e AllowOverride All
RUN a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Ajusta DocumentRoot para Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Define permissões corretas
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Expondo porta
EXPOSE 80

CMD ["apache2-foreground"]
