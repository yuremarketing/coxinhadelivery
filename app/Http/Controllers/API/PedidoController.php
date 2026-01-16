<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lista os pedidos.
     * O Balcão (Carlos) vê os pendentes primeiro pra agilizar a cozinha.
     */
    public function index()
    {
        $pedidos = Order::with(['itens.produto', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pedidos);
    }

    /**
     * Cria um novo pedido.
     * O cliente manda os itens, e O SISTEMA calcula o preço. 
     * Nada de mandar preço do front, pra ninguém burlar.
     */
    public function store(Request $request)
    {
        // 1. Valida se mandou os itens
        $request->validate([
            'itens' => 'required|array',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Cria o pedido vazio
                $pedido = Order::create([
                    'user_id' => auth()->id(),
                    'status' => 'pendente',
                    'total' => 0, // Vamos calcular agora
                ]);

                $total = 0;

                // Processa cada item (Coxinha, Refri...)
                foreach ($request->itens as $item) {
                    $produto = Product::find($item['produto_id']);
                    $subtotal = $produto->preco * $item['quantidade'];
                    
                    $pedido->itens()->create([
                        'produto_id' => $produto->id,
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $produto->preco,
                    ]);

                    $total += $subtotal;
                }

                // Atualiza o total final no banco
                $pedido->update(['total' => $total]);

                return response()->json($pedido->load('itens.produto'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar pedido: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Onde o Carlos trabalha.
     * Ele muda de 'pendente' -> 'preparando' -> 'entrega'.
     */
    public function updateStatus(Request $request, $id)
    {
        $pedido = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pendente,preparando,entrega,concluido,cancelado'
        ]);

        $pedido->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status atualizado para ' . $request->status,
            'pedido' => $pedido
        ]);
    }

    /**
     * O Cofre do Patrão.
     * Mostra quanto vendeu hoje. O Middleware já bloqueia o Carlos de ver isso.
     */
    public function dashboard()
    {
        $hoje = now()->format('Y-m-d');

        $vendasHoje = Order::whereDate('created_at', $hoje)
            ->where('status', '<>', 'cancelado')
            ->sum('total');

        $pedidosPendentes = Order::where('status', 'pendente')->count();

        return response()->json([
            'faturamento_hoje' => $vendasHoje,
            'pedidos_na_fila' => $pedidosPendentes,
            'mensagem' => 'O olho do dono é que engorda o gado.'
        ]);
    }
}
