FROM php:8.2-apache

RUN apt-get update && apt-get install -y     libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip curl git     && curl -fsSL https://deb.nodesource.com/setup_20.x | bash -     && apt-get install -y nodejs

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite
COPY . /var/www/html

RUN composer install --no-interaction --optimize-autoloader --no-dev     && npm install     && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ajuste crucial para as rotas funcionarem
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf /etc/apache2/sites-available/*.conf

EXPOSE 80

CMD php artisan migrate --force && apache2-foreground
