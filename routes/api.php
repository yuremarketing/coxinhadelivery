<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PedidoController;
use App\Http\Controllers\API\LojaConfigController;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS (Acesso Livre)
|--------------------------------------------------------------------------
*/

// Cadastro e Login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// O App consulta isso antes de qualquer coisa pra saber se a loja tá on
Route::get('/loja-config', [LojaConfigController::class, 'index']);

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (Precisa de Login - Token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Sair do sistema
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ver meus dados
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | ÁREA DA EQUIPE (Admin e Funcionários)
    |--------------------------------------------------------------------------
    | O Middleware 'is_admin' controla quem faz o quê aqui dentro.
    */
    Route::middleware('is_admin')->group(function () {
        
        // Dashboard Financeiro (Bloqueado para funcionários no Middleware)
        Route::get('/admin/dashboard', [PedidoController::class, 'dashboard']);

        // Configurações da Loja (Atualizar Link YouTube / Abrir e Fechar)
        Route::put('/admin/loja-config', [LojaConfigController::class, 'update']);

        // Rotas de Pedidos (Listar, Criar, Atualizar)
        Route::apiResource('pedidos', PedidoController::class);
        
        // Rota específica para mudar status (Cozinha -> Entrega)
        Route::patch('/pedidos/{id}/status', [PedidoController::class, 'updateStatus']);
    });
});
