#!/bin/bash

echo "🛠️ [Coxinha Delivery] - Fase 77: Corrigindo Injeção do Vite"

# Passo 1: Reescrever o resources/views/app.blade.php
# Vamos remover os scripts manuais que estão dando erro de conexão
cat << 'EOF' > resources/views/app.blade.php
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Coxinha Delivery</title>
        
        {{-- Estas DIRETIVAS abaixo substituem os seus scripts manuais --}}
        {{-- Elas sabem lidar com o Docker e o localhost corretamente --}}
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
EOF

# Passo 2: Limpar o cache de visualização para o Laravel ler o novo arquivo
docker compose exec laravel.test php artisan view:clear

echo "------------------------------------------------"
echo "✅ ARQUIVO BLADE CORRIGIDO!"
echo "------------------------------------------------"
echo "CERTIFIQUE-SE QUE O MOTOR AINDA ESTÁ RODANDO NO OUTRO TERMINAL:"
echo "docker compose exec laravel.test npm run dev -- --host 0.0.0.0"
echo "------------------------------------------------"
