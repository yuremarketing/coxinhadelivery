#!/bin/bash
echo "=== 1. LIMPANDO LOGS ANTIGOS ==="
./vendor/bin/sail artisan tinker --execute="file_put_contents(storage_path('logs/laravel.log'), '')"

echo -e "\n=== 2. ESTRUTURA REAL DA TABELA PEDIDOS (DB) ==="
./vendor/bin/sail artisan tinker --execute="print_r(Schema::getColumnListing('pedidos'));"

echo -e "\n=== 3. ESTRUTURA REAL DA TABELA PEDIDO_ITENS (DB) ==="
./vendor/bin/sail artisan tinker --execute="print_r(Schema::getColumnListing('pedido_itens'));"

echo -e "\n=== 4. ÚLTIMO ERRO REGISTRADO NO LARAVEL (O CULPADO) ==="
./vendor/bin/sail artisan tinker --execute="echo file_get_contents(storage_path('logs/laravel.log'))"

echo -e "\n=== 5. CONTEÚDO ATUAL DA ROTA DE API ==="
cat routes/api.php
