#!/bin/bash

echo "👔 Configurando Painel Administrativo..."

# 1. GARANTIR QUE O USUÁRIO É ADMIN
# Promove o usuário ID 1 (criado no seeder) para admin
echo "👮 Promovendo usuário ID 1 para Administrador..."
docker compose exec laravel.test php artisan tinker --execute="\$u = App\Models\User::find(1); if(\$u){ \$u->role = 'admin'; \$u->save(); echo 'Usuario promovido!'; } else { echo 'Usuario nao encontrado'; }"

# 2. CRIAR O CONTROLLER DO ADMINISTRADOR
cat << 'EOF' > app/Http/Controllers/AdminPedidoController.php
<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class AdminPedidoController extends Controller
{
    // Lista TODOS os pedidos (Painel do Dono)
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['erro' => 'Acesso não autorizado.'], 403);
        }

        // Traz pedidos ordenados + dados do cliente + itens
        $pedidos = Pedido::with(['user', 'itens.produto'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($pedidos);
    }

    // Atualiza Status (Cozinha -> Entrega)
    public function updateStatus(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['erro' => 'Acesso não autorizado.'], 403);
        }

        $request->validate([
            'status' => 'required|in:pendente,em_preparacao,saiu_entrega,concluido,cancelado'
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->update(['status' => $request->status]);

        return response()->json(['message' => 'Status atualizado!', 'pedido' => $pedido]);
    }
}
EOF

# 3. ADICIONAR AS ROTAS DE ADMIN
# Adiciona apenas se ainda não existirem
if ! grep -q "/admin/pedidos" routes/api.php; then
    echo "" >> routes/api.php
    echo "// --- ROTAS ADMINISTRATIVAS ---" >> routes/api.php
    echo "Route::middleware(['auth:sanctum'])->group(function () {" >> routes/api.php
    echo "    Route::get('/admin/pedidos', [\App\Http\Controllers\AdminPedidoController::class, 'index']);" >> routes/api.php
    echo "    Route::put('/admin/pedidos/{id}', [\App\Http\Controllers\AdminPedidoController::class, 'updateStatus']);" >> routes/api.php
    echo "});" >> routes/api.php
    echo "✅ Rotas de admin adicionadas."
else
    echo "⚠️ Rotas de admin já existiam."
fi

# 4. TESTAR
echo "---------------------------------------------------"
echo "🧪 Testando Painel Admin..."

# Login Admin
LOGIN_JSON=$(docker compose exec laravel.test curl -s -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -d '{"email": "admin@coxinha.com", "password": "password"}')

# Extrair Token (método compatível com python antigo e novo)
TOKEN=$(echo $LOGIN_JSON | python3 -c "import sys, json; print(json.load(sys.stdin)['access_token'])")

echo "🔑 Token Admin: ${TOKEN:0:10}..."

# Listar Pedidos
echo "1. Listando Pedidos..."
docker compose exec laravel.test curl -s -X GET http://localhost/api/admin/pedidos \
    -H "Authorization: Bearer $TOKEN" | python3 -m json.tool

# Mudar Status
echo "---------------------------------------------------"
echo "2. Movendo pedido 1 para 'em_preparacao'..."
docker compose exec laravel.test curl -s -X PUT http://localhost/api/admin/pedidos/1 \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"status": "em_preparacao"}' | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se o status mudou, você é o dono da loja!"
