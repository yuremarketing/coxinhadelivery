<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    /**
     * TELA DO ENTREGADOR
     * ---------------------------------------------------
     * Eu recebo o código (hash) pela URL.
     * Procuro no meu banco se esse código existe.
     * Se existir, eu abro a tela com os dados do pedido.
     */
    public function show($hash)
    {
        // Procuro o pedido que tem esse hash específico
        $pedido = Order::where('hash_entrega', $hash)->firstOrFail();

        // Mando para a tela de visualização do motoboy
        return view('entrega.show', compact('pedido'));
    }
}
