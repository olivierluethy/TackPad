# TackPad web image: PHP 8.3 + Apache, with the MySQL drivers the app needs.
FROM php:8.3-apache

# System libs + PHP extensions (PDO for the notes model, mysqli for auth).
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libzip-dev \
    && docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composer (from the official image) to install PHP dependencies.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first to leverage Docker layer caching.
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts

# Production PHP config (errors to the log, never into responses).
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-tackpad.ini

# Application code.
COPY . /var/www/html

# Allow .htaccess rewrites (front controller routing).
COPY docker/apache.conf /etc/apache2/conf-available/tackpad.conf
RUN a2enconf tackpad \
    && chown -R www-data:www-data /var/www/html

# Generates .env from container env vars, then runs Apache.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
