<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function cozinha() {
        // Busca pedidos pendentes com os itens e produtos carregados
        $pedidos = Pedido::with('itens.produto')->where('status', 'pendente')->latest()->get();
        return view('admin.pedidos.cozinha', compact('pedidos'));
    }

    public function alterarStatus(Pedido $pedido) {
        $pedido->update(['status' => 'concluido']);
        return back()->with('sucesso', 'Pedido finalizado!');
    }
}
