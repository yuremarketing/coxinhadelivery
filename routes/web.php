<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;

Route::get('/', [PedidoController::class, 'vender'])->name('vender');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('produtos', ProdutoController::class);
    
    // Rota da Cozinha e a rota de Concluir com o nome correto
    Route::get('/pedidos', [AdminPedidoController::class, 'cozinha'])->name('admin.pedidos.cozinha');
    Route::patch('/pedidos/{pedido}/concluir', [AdminPedidoController::class, 'concluir'])->name('pedidos.concluir');
});

require __DIR__.'/auth.php';
