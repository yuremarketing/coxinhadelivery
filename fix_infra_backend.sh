#!/bin/bash

echo "🏗️  Passo 1: Corrigindo Infraestrutura e Backend..."

# 1. Ajustar permissões para evitar erros de escrita do Docker
docker compose exec -u root laravel.test chmod -R 777 storage bootstrap/cache resources/js

# 2. Resetar o arquivo de rotas para o padrão SPA (Single Page Application)
# Isso remove o erro de "vender()" e entrega o controle ao React
cat << 'EOF' > routes/web.php
<?php
use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
EOF

# 3. Limpeza profunda de cache para o Laravel esquecer erros antigos
docker compose exec laravel.test php artisan optimize:clear

echo "✅ Backend e Permissões ajustados."
