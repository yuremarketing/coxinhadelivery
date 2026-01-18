FROM php:8.2-apache

# Instala dependências do sistema e ferramentas necessárias
RUN apt-get update && apt-get install -y     libpng-dev     libonig-dev     libxml2-dev     zip     unzip     curl     git

# Instala extensões do PHP essenciais para o Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer diretamente da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos do projeto para o servidor
COPY . /var/www/html

# Roda o comando para instalar as bibliotecas (Pasta Vendor)
# Usamos --no-dev para o servidor ficar mais leve
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ajusta as permissões para o Laravel funcionar corretamente
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Define a pasta public como a raiz do site
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

EXPOSE 80
