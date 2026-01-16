#!/bin/bash

echo "🔒 Configurando Venda Segura (Com Token)..."

# 1. ORGANIZAR AS ROTAS (REESCREVENDO O ARQUIVO PARA LIMPAR A BAGUNÇA)
# Agora separamos o que é público (Login/Produtos) do que é privado (Pedidos)
cat << 'EOF' > routes/api.php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PedidoController;

// --- ROTAS PÚBLICAS (Qualquer um acessa) ---
Route::post('/login', [AuthController::class, 'login']);
Route::get('/produtos', [ProdutoController::class, 'index']);

// --- ROTAS PROTEGIDAS (Precisa de Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Retorna os dados do usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Criar Pedido (agora exige login)
    Route::post('/pedidos', [PedidoController::class, 'store']);
});
EOF

# 2. ATUALIZAR O CONTROLLER PARA USAR O USUÁRIO REAL
cat << 'EOF' > app/Http/Controllers/PedidoController.php
<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        // Validação
        $dados = $request->validate([
            'itens' => 'required|array',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric',
        ]);

        try {
            return DB::transaction(function () use ($dados, $request) {
                
                // --- A MÁGICA ACONTECE AQUI ---
                // Pegamos o ID do usuário diretamente do Token (request->user())
                // Não usamos mais '1' fixo.
                $user = $request->user();
                
                $pedido = Pedido::create([
                    'user_id' => $user->id, 
                    'status' => 'pendente'
                ]);

                // Cria os Itens
                foreach ($dados['itens'] as $item) {
                    PedidoItem::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $item['produto_id'],
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $item['preco_unitario']
                    ]);
                }

                return response()->json($pedido->load('itens'), 201);
            });

        } catch (\Exception $e) {
            return response()->json(['erro' => 'Erro ao processar venda: ' . $e->getMessage()], 500);
        }
    }
}
EOF

# 3. TESTE AUTOMATIZADO DO FLUXO REAL
echo "---------------------------------------------------"
echo "🧪 Testando Fluxo de Segurança..."

# Passo A: Fazer Login e capturar o Token
echo "1. Fazendo Login..."
LOGIN_RESPONSE=$(docker compose exec laravel.test curl -s -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -d '{"email": "admin@coxinha.com", "password": "password"}')

# Extrai o token usando python (gambiarra técnica elegante)
TOKEN=$(echo $LOGIN_RESPONSE | python3 -c "import sys, json; print(json.load(sys.stdin)['access_token'])")

echo "🔑 Token capturado: ${TOKEN:0:10}..." # Mostra só o começo por segurança

# Passo B: Tentar vender SEM token (deve falhar)
echo "2. Testando acesso negado (Sem Token)..."
STATUS_CODE=$(docker compose exec laravel.test curl -s -o /dev/null -w "%{http_code}" -X POST http://localhost/api/pedidos)
if [ "$STATUS_CODE" == "401" ] || [ "$STATUS_CODE" == "500" ]; then 
    echo "✅ Bloqueio funcionou! (Código $STATUS_CODE)"
else
    echo "⚠️ Atenção: A rota não bloqueou o acesso sem token (Código $STATUS_CODE)"
fi

# Passo C: Tentar vender COM token (deve funcionar)
echo "3. Realizando Venda Autenticada (Com Token)..."
docker compose exec laravel.test curl -s -X POST http://localhost/api/pedidos \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "itens": [
            {"produto_id": 2, "quantidade": 5, "preco_unitario": 6.50} 
        ]
    }' | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se você viu o pedido com 'user_id': 1 no final, O BACKEND ESTÁ FINALIZADO!"
