<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Lista TODOS os pedidos (Painel do Dono)
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['erro' => 'Acesso não autorizado.'], 403);
        }

        // Traz pedidos ordenados + dados do cliente + itens
        $pedidos = Order::with(['user', 'itens.produto'])
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

        $pedido = Order::findOrFail($id);
        $pedido->update(['status' => $request->status]);

        return response()->json(['message' => 'Status atualizado!', 'pedido' => $pedido]);
    }
}
