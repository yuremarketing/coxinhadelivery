#!/bin/bash

echo "🖥️  [Coxinha Delivery] - Ativando MODO CONSOLE F12 no Terminal"

# 1. Limpar bloqueios de porta anteriores
docker compose exec laravel.test fuser -k 5173/tcp 2>/dev/null

# 2. Corrigir o .env para garantir que o Vite não use HTTPS ou NGROK (causa do CORS)
# As imagens mostram erro de domínio inválido nos cookies
sed -i 's|APP_URL=.*|APP_URL=http://localhost|g' .env
sed -i 's|SESSION_DOMAIN=.*|SESSION_DOMAIN=localhost|g' .env
sed -i 's|SANCTUM_STATEFUL_DOMAINS=.*|SANCTUM_STATEFUL_DOMAINS=localhost:8000,localhost:5173,localhost|g' .env

# 3. Limpar Caches para garantir que o erro de Cookie desapareça
docker compose exec laravel.test php artisan optimize:clear

echo "------------------------------------------------"
echo "👀 MONITORAMENTO ATIVO: Olhe este terminal após dar F5"
echo "Se aparecer 'GET /resources/js/app.jsx', a conexão funcionou."
echo "------------------------------------------------"

# 4. Iniciar o motor com LOGS DE DEPURAÇÃO (Simula o Console do F12)
docker compose exec laravel.test npm run dev -- --host 0.0.0.0 --debug
