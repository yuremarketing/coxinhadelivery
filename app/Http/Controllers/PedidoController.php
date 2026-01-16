<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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
                
                $pedido = Order::create([
                    'user_id' => $user->id, 
                    'status' => 'pendente'
                ]);

                // Cria os Itens
                foreach ($dados['itens'] as $item) {
                    OrderItem::create([
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
