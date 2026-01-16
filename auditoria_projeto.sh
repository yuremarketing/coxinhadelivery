#!/bin/bash
echo "=== 1. ROTAS (WEB) ==="
cat routes/web.php
echo -e "\n=== 2. MODELO USER ==="
cat app/Models/User.php
echo -e "\n=== 3. TODAS AS MIGRATIONS (ESTRUTURA) ==="
ls database/migrations/
echo -e "\n=== 4. CONFIGURAÇÃO DE AMBIENTE (SEM SENHAS REAIS) ==="
grep -E "DB_|APP_URL|SESSION_DRIVER" .env
echo -e "\n=== 5. LISTA DE CONTROLLERS ==="
ls app/Http/Controllers/
