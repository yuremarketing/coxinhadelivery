<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    public function index()
    {
        $vendas = Order::orderBy('created_at', 'desc')->get();
        return view('admin.pedidos.historico', compact('vendas'));
    }

    public function storeAPI(Request $request)
    {
        try {
            Log::info('Dados brutos recebidos:', $request->all());

            // Criando o pedido respeitando as REGRAS DO BANCO (Null: NO)
            $pedido = Order::create([
                'numero_pedido'    => strtoupper(uniqid('PED-')),
                'cliente_nome'     => $request->cliente ?? $request->cliente_nome ?? 'Cliente Sem Nome',
                'cliente_telefone' => $request->telefone ?? '000000000', // REGRA: Não pode ser nulo
                'status'           => 'pendente', // REGRA: Não pode ser nulo
                'tipo'             => 'retirada', // REGRA: Deve ser 'retirada' ou 'entrega'
                'valor_total'      => $request->total ?? 0.00, // REGRA: Não pode ser nulo
                'observacoes'      => $request->observacoes ?? 'Order via Celular',
            ]);

            return response()->json(['success' => true, 'id' => $pedido->id], 201);

        } catch (\Exception $e) {
            Log::error("ERRO DE REGRA DE BANCO: " . $e->getMessage());
            return response()->json([
                'error' => 'Erro de validação no banco',
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }
}
