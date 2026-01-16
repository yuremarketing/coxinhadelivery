<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\Admin\ProdutoController as AdminProdutoController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;

// --- ROTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);
Route::get('/produtos', [ProdutoController::class, 'index']);

// --- ROTAS PROTEGIDAS (Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/pedidos', [PedidoController::class, 'store']);
});

// --- ROTAS ADMINISTRATIVAS ---
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/pedidos', [AdminPedidoController::class, 'index']);
    Route::put('/admin/pedidos/{id}', [AdminPedidoController::class, 'updateStatus']);
    Route::post('/admin/produtos', [AdminProdutoController::class, 'store']);
});
