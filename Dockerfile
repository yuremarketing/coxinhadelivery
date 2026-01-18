FROM php:8.2-apache

# Instala dependências do sistema e o Node.js
RUN apt-get update && apt-get install -y     libpng-dev libonig-dev libxml2-dev zip unzip curl git     && curl -fsSL https://deb.nodesource.com/setup_20.x | bash -     && apt-get install -y nodejs

# Instala extensões do PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ativa o mod_rewrite
RUN a2enmod rewrite

# Copia o projeto
COPY . /var/www/html

# Instala dependências do PHP e do JS, e gera os arquivos do Vite
RUN composer install --no-interaction --optimize-autoloader --no-dev     && npm install     && npm run build

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

EXPOSE 80
