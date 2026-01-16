#!/bin/bash

echo "🛠️  [Coxinha Delivery] - RESTAURAÇÃO TOTAL DE AMBIENTE"

# 1. Ajuste de Identidade (.env)
# Resolve o Crash de conexão recusada no navegador
sed -i 's|APP_URL=.*|APP_URL=http://localhost|g' .env
echo "VITE_PORT=5173" >> .env
echo "VITE_HOST=0.0.0.0" >> .env

# 2. Limpeza de "Cicatrizes" (Caches e Builds antigos)
docker compose exec laravel.test php artisan optimize:clear
docker compose exec laravel.test rm -rf public/build node_modules/.vite

# 3. Alinhamento da View e do React
# Garante que o ID 'app' seja o mesmo no HTML e no JS
cat << 'EOF' > resources/views/welcome.blade.php
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Coxinha Delivery</title>
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
EOF

# 4. Matar processos zumbis na porta 5173
docker compose exec laravel.test fuser -k 5173/tcp 2>/dev/null || echo "Porta livre."

echo "------------------------------------------------"
echo "✅ PROJETO RESTAURADO E SINCRONIZADO!"
echo "Execute o comando final abaixo."
echo "------------------------------------------------"

docker compose exec laravel.test npm run dev -- --host 0.0.0.0
