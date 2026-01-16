#!/bin/bash

echo "🛠️ [Coxinha Delivery] - Estabilização de Ambiente e Correção de Manifest"

# 1. Remover vestígios de 'build' de produção
# Isso resolve o erro 'Vite manifest not found'
docker compose exec laravel.test rm -rf public/build
docker compose exec laravel.test rm -f public/hot

# 2. Forçar limpeza de processos zumbis na porta 5173
# Sem isso, o erro 'Port is already in use' voltará
docker compose exec laravel.test fuser -k 5173/tcp 2>/dev/null || echo "Porta livre."

# 3. Corrigir permissões de escrita para que o Vite possa criar arquivos temporários
docker compose exec -u root laravel.test chmod -R 777 storage bootstrap/cache public

# 4. Limpar cache do Laravel para forçar leitura do modo 'dev'
docker compose exec laravel.test php artisan view:clear
docker compose exec laravel.test php artisan config:clear

echo "------------------------------------------------"
echo "✅ AMBIENTE ESTABILIZADO!"
echo "Agora, execute o motor abaixo."
echo "------------------------------------------------"

# 5. Iniciar o motor Vite com host liberado
docker compose exec laravel.test npm run dev -- --host 0.0.0.0
