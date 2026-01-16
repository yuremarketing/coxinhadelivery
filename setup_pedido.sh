#!/bin/bash

echo "🚀 Configurando Venda de Coxinhas (Pedidos)..."

# 1. ATUALIZAR O CONTROLLER DE PEDIDOS
# Vamos criar o PedidoController.php com a lógica de receber JSON e salvar no banco
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
        // Validação básica (o correto seria usar FormRequest, mas vamos manter simples por enquanto)
        $dados = $request->validate([
            'itens' => 'required|array',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric',
        ]);

        try {
            // Inicia uma transação: ou salva tudo (pedido + itens) ou não salva nada
            return DB::transaction(function () use ($dados) {
                
                // 1. Cria o Pedido (assumindo usuário ID 1 por enquanto para teste)
                $pedido = Pedido::create([
                    'user_id' => 1, 
                    'status' => 'pendente'
                ]);

                // 2. Cria os Itens do Pedido
                foreach ($dados['itens'] as $item) {
                    PedidoItem::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $item['produto_id'],
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $item['preco_unitario']
                    ]);
                }

                // Retorna o pedido completo com os itens
                return response()->json($pedido->load('itens'), 201);
            });

        } catch (\Exception $e) {
            return response()->json(['erro' => 'Falha ao criar pedido: ' . $e->getMessage()], 500);
        }
    }
}
EOF

# 2. ADICIONAR A ROTA DE PEDIDOS
echo "" >> routes/api.php
echo "// Rota para criar pedidos (POST)" >> routes/api.php
echo "Route::post('/pedidos', [\App\Http\Controllers\PedidoController::class, 'store']);" >> routes/api.php

# 3. TESTAR A VENDA (POST)
echo "🧪 Simulando uma compra de Coxinhas..."
echo "Enviando: 2 Coxinhas Tradicionais (ID 1) e 1 Coca-Cola (ID 4)..."
echo "---------------------------------------------------"

# Envia um JSON via CURL simulando o Frontend
docker compose exec laravel.test curl -s -X POST http://localhost/api/pedidos \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "itens": [
            {"produto_id": 1, "quantidade": 2, "preco_unitario": 5.50},
            {"produto_id": 4, "quantidade": 1, "preco_unitario": 5.00}
        ]
    }' | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se apareceu um JSON com 'id' do pedido e status 'pendente', A VENDA OCORREU!"
