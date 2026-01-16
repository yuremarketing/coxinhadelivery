#!/bin/bash
echo "=== ESTRUTURA ATUAL DA TABELA USERS ==="
./vendor/bin/sail artisan tinker --execute="foreach(DB::select('DESCRIBE users') as \$c) { echo \"Campo: {\$c->Field} | Tipo: {\$c->Type} | Nulo: {\$c->Null}\n\"; }"
