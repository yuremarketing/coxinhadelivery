# --- ETAPA 1: Construir o Front-end (Node.js) ---
FROM node:20 as node_stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- ETAPA 2: Configurar o Servidor (PHP) ---
FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip curl git

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ativar Mod Rewrite
RUN a2enmod rewrite

# Configuração do Apache (Para evitar o erro 404)
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copiar os arquivos do Laravel
COPY . /var/www/html

# --- A MÁGICA: Copiar os arquivos do Vite da Etapa 1 para cá ---
COPY --from=node_stage /app/public/build /var/www/html/public/build
# ---------------------------------------------------------------

# Instalar dependências do PHP (Composer)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ajustar Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD php artisan migrate --force && apache2-foreground
