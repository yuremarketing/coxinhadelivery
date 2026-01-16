#!/bin/bash

echo "🚑 Aplicando Correção Final (Padrão SPA + Docker Network)..."

# 1. CORRIGIR ROTA (Obrigando o Laravel a atuar como API Gateway para o React)
echo "🔗 Atualizando routes/web.php..."
cat << 'EOF' > routes/web.php
<?php
use Illuminate\Support\Facades\Route;

// Padrão SPA: Qualquer acesso que não seja API vai para o React
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
EOF

# 2. CONFIGURAR VITE PARA DOCKER (Expondo a porta do container corretamente)
echo "⚙️  Configurando Vite para aceitar conexão externa (Host 0.0.0.0)..."
cat << 'EOF' > vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0', // Necessário para Docker no Windows/WSL
        hmr: {
            host: 'localhost'
        },
        watch: {
            usePolling: true
        }
    }
});
EOF

# 3. LIMPEZA PROFUNDA DE CACHE
echo "🧹 Limpando caches do Framework..."
docker compose exec laravel.test php artisan route:clear
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan view:clear

echo "✅ ARQUITETURA AJUSTADA!"
