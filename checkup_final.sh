#!/bin/bash
echo "=== 1. CONTEÚDO DA MIGRATION DE ROLE ==="
cat database/migrations/*_alter_users_table_add_role_field.php

echo -e "\n=== 2. ESTRUTURA DA TABELA DE PEDIDOS (Para ver o campo telefone) ==="
./vendor/bin/sail artisan tinker --execute="foreach(DB::select('DESCRIBE pedidos') as \$c) { echo \"Campo: {\$c->Field} | Tipo: {\$c->Type}\n\"; }"

echo -e "\n=== 3. CONTEÚDO DA MIGRATION DE PEDIDOS ==="
cat database/migrations/*_create_pedidos_table.php

echo -e "\n=== 4. VERIFICANDO SE O CAMPO 'IS_ADMIN' AINDA EXISTE FISICAMENTE ==="
./vendor/bin/sail artisan tinker --execute="echo Schema::hasColumn('users', 'is_admin') ? 'SIM, is_admin existe' : 'NÃO existe';"
