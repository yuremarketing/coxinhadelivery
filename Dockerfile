FROM php:8.2-apache

RUN apt-get update && apt-get install -y     libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip curl git     && curl -fsSL https://deb.nodesource.com/setup_20.x | bash -     && apt-get install -y nodejs

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

# Criando a configuração do Apache diretamente
RUN echo '<VirtualHost *:80>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        Options Indexes FollowSymLinks\n        AllowOverride All\n        Require all granted\n    </Directory>\n    ErrorLog ${APACHE_LOG_DIR}/error.log\n    CustomLog ${APACHE_LOG_DIR}/access.log combined\n</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html

RUN composer install --no-interaction --optimize-autoloader --no-dev     && npm install     && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD php artisan migrate --force && apache2-foreground
