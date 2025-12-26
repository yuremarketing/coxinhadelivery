<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = now()->today();

        // Totais de hoje
        $vendasHoje = Pedido::where('status', 'finalizado')
                            ->whereDate('updated_at', $hoje)
                            ->sum('valor_total');

        $pedidosHoje = Pedido::whereDate('created_at', $hoje)->count();

        // Produtos mais vendidos (Top 3)
        $maisVendidos = PedidoItem::select('produto_id', DB::raw('SUM(quantidade) as total'))
            ->groupBy('produto_id')
            ->orderByDesc('total')
            ->with('produto')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact('vendasHoje', 'pedidosHoje', 'maisVendidos'));
    }
}
