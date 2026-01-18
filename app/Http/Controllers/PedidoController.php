<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Produto;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        // Retorna os pedidos do usuário logado com os itens
        return $request->user()->pedidos()->with('itens')->get();
    }

    public function store(Request $request)
    {
        // 1. Validação (Apenas ID e Quantidade)
        $input = $request->validate([
            'itens' => 'required|array',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        // 2. Calcular o Total REAL (Segurança de Preço)
        $totalGeral = 0;
        $itensParaSalvar = [];

        foreach ($input['itens'] as $item) {
            $produto = Produto::find($item['produto_id']);
            
            if (!$produto) {
                continue; 
            }

            $precoReal = $produto->preco;
            $quantidade = $item['quantidade'];

            // Calculamos o total aqui no servidor
            $totalGeral += ($precoReal * $quantidade);

            // Preparamos os itens para salvar depois
            $itensParaSalvar[] = [
                'produto_id' => $produto->id,
                'quantidade' => $quantidade,
                'preco_unitario' => $precoReal 
            ];
        }

        // 3. Criar o Pedido (AGORA COM O CAMPO TOTAL)
        $pedido = $request->user()->pedidos()->create([
            'total' => $totalGeral, // <--- AQUI ESTAVA FALTANDO!
            'status' => 'pendente',
            'numero_pedido' => 'CX' . time() // Gera um número único simples
        ]);

        // 4. Salvar os Itens do pedido
        $pedido->itens()->createMany($itensParaSalvar);

        return response()->json($pedido, 201);
    }
}
