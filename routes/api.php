<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProdutoController;
use App\Http\Controllers\API\PedidoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\IsAdmin;

Route::get('/health', function () { 
    return response()->json(['status' => 'online']); 
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('produtos')->group(function () {
    Route::get('/', [ProdutoController::class, 'index']);
    Route::get('/{id}', [ProdutoController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/categorias', [ProdutoController::class, 'categorias']);
});

Route::prefix('pedidos')->group(function () {
    Route::post('/', [PedidoController::class, 'store']);
    Route::get('/{codigo}', [PedidoController::class, 'show']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
    });
    
    // TODAS as rotas de admin agora estão protegidas aqui dentro
    Route::middleware([IsAdmin::class])->prefix('admin/produtos')->group(function () {
        Route::post('/', [ProdutoController::class, 'store']);
        Route::put('/{id}', [ProdutoController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [ProdutoController::class, 'destroy'])->where('id', '[0-9]+');
    });
    
    Route::middleware([IsAdmin::class])->prefix('admin/pedidos')->group(function () {
        Route::get('/', [PedidoController::class, 'index']);
        Route::put('/{id}/status', [PedidoController::class, 'updateStatus']);
        Route::get('/{id}', [PedidoController::class, 'adminShow'])->where('id', '[0-9]+');
    });
});

Route::fallback(function () { 
    return response()->json(['message' => 'Endpoint nao encontrado'], 404); 
});
