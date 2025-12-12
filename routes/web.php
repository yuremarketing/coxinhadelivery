<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// API Routes (temporário - depois movemos para api.php)
Route::prefix('api')->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'online',
            'service' => 'CoxinhaDelivery API',
            'version' => '1.0.0',
            'timestamp' => now()->toDateTimeString(),
            'environment' => app()->environment()
        ]);
    });
    
    // Produtos
    Route::get('/produtos', [\App\Http\Controllers\API\ProdutoController::class, 'index']);
    Route::get("/produtos/categorias", [\App\Http\Controllers\API\ProdutoController::class, "categorias"]);
    Route::get("/produtos/{id}", [\App\Http\Controllers\API\ProdutoController::class, "show"]);
    
    // Pedidos (clientes)  ← MOVER DENTRO DO GRUPO
    Route::post('/pedidos', [\App\Http\Controllers\API\PedidoController::class, 'store']);
    Route::get('/pedidos/{codigo}', [\App\Http\Controllers\API\PedidoController::class, 'show']);
    
    // Admin Pedidos (sem auth por enquanto)
    Route::get('/admin/pedidos', [\App\Http\Controllers\API\PedidoController::class, 'index']);
    Route::put('/admin/pedidos/{id}/status', [\App\Http\Controllers\API\PedidoController::class, 'updateStatus']);
    Route::get('/admin/pedidos/{id}', [\App\Http\Controllers\API\PedidoController::class, 'adminShow']);
}); // ← Este fecha o grupo prefix('api')