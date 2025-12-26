<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;
use App\Models\Pedido;

// Frente de Loja
Route::get('/', [PedidoController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');

// Cozinha
Route::get('/admin/pedidos', function () {
    $pedidos = Pedido::where('status', 'pendente')->with('itens.produto')->get();
    return view('admin.pedidos.cozinha', compact('pedidos'));
})->name('admin.pedidos.index');

Route::post('/admin/pedidos/{id}/status', function ($id) {
    $pedido = Pedido::findOrFail($id);
    $pedido->update(['status' => 'concluido']);
    return back();
})->name('admin.pedidos.status');
