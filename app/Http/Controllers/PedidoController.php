<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Pedido;
use App\Models\ItemPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function create() {
        return view('vender', ['produtos' => Produto::all()]);
    }

    public function store(Request $request) {
        // 1. Validação Melindrosa: Garante que os dados estão corretos antes de tocar no banco
        $validated = $request->validate([
            'cliente_nome' => 'required|string|max:255',
            'produto_id'   => 'required|exists:produtos,id',
        ]);

        // 2. Transação: Ou grava tudo (Pedido + Item) ou não grava nada se der erro
        return DB::transaction(function () use ($validated) {
            $produto = Produto::find($validated['produto_id']);

            $pedido = Pedido::create([
                'cliente_nome'     => $validated['cliente_nome'],
                'cliente_telefone' => '000000000',
                'status'           => 'pendente',
                'tipo'             => 'retirada',
                'valor_total'      => $produto->preco
            ]);

            ItemPedido::create([
                'pedido_id'      => $pedido->id,
                'produto_id'     => $produto->id,
                'quantidade'     => 1,
                'preco_unitario' => $produto->preco
            ]);

            // 3. Resposta Padrão REST: O servidor confirma o sucesso com dados JSON
            return response()->json([
                'status'  => 'success',
                'message' => 'Pedido enviado para a cozinha!',
                'data'    => [
                    'numero'  => substr($pedido->numero_pedido, -5),
                    'cliente' => $pedido->cliente_nome
                ]
            ], 201); // Código 201 significa "Criado com Sucesso"
        });
    }
}
