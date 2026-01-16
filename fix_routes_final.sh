#!/bin/bash

echo "🔧 Corrigindo o roteamento para o React..."

# 1. REESCREVER AS ROTAS WEB (routes/web.php)
# Isso diz: "Qualquer coisa que não for API, mande para o React (welcome view)"
cat << 'EOF' > routes/web.php
<?php

use Illuminate\Support\Facades\Route;

// Rota principal: Carrega o React
Route::get('/', function () {
    return view('welcome');
});

// Fallback: Se o usuário digitar /login ou /admin direto no navegador,
// o Laravel não vai dar 404, vai carregar o React para ele tratar a rota.
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
EOF

# 2. LIMPAR TODOS OS CACHES DO LARAVEL
# O Laravel adora "lembrar" de erros antigos. Vamos forçar o esquecimento.
echo "🧹 Limpando cache de rotas e configurações..."
docker compose exec laravel.test php artisan route:clear
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan cache:clear
docker compose exec laravel.test php artisan view:clear

echo "✅ Rotas Corrigidas!"
echo "O Laravel agora está configurado para servir apenas o React."
