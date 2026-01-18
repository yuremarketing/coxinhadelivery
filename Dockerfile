FROM php:8.2-apache

# Instala extensões necessárias para o Laravel
RUN apt-get update && apt-get install -y     libpng-dev     libonig-dev     libxml2-dev     zip     unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Ativa o mod_rewrite do Apache (essencial para o Laravel)
RUN a2enmod rewrite

# Copia o seu código para dentro do servidor
COPY . /var/www/html

# Ajusta as permissões para o Laravel conseguir escrever nas pastas de cache/logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Define a pasta pública do Laravel como a raiz do site
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

EXPOSE 80
