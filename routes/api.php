<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProdutoController;
use App\Http\Controllers\API\PedidoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui são registradas todas as rotas da API.
| Todas as rotas aqui são automaticamente prefixadas com '/api'
| e têm middleware 'api' aplicado.
|
*/

// Rota de health check - para verificar se a API está online
Route::get('/health', function () {
    return response()->json([
        'status' => 'online',
        'service' => 'CoxinhaDelivery API',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString(),
        'environment' => app()->environment()
    ]);
});

// Rotas de autenticação (públicas)
Route::prefix('auth')->group(function () {
    // POST /api/auth/register - Registrar novo usuário
    Route::post('/register', [AuthController::class, 'register']);
    
    // POST /api/auth/login - Login de usuário
    Route::post('/login', [AuthController::class, 'login']);
});

// Grupo de rotas para produtos (públicas - qualquer um pode acessar)
Route::prefix('produtos')->group(function () {
    
    // GET /api/produtos - Lista todos os produtos
    Route::get('/', [ProdutoController::class, 'index']);
    
    // GET /api/produtos/{id} - Mostra um produto específico
    Route::get('/{id}', [ProdutoController::class, 'show'])->where('id', '[0-9]+');
    
    // GET /api/produtos/categorias - Lista categorias disponíveis
    Route::get('/categorias', [ProdutoController::class, 'categorias']);
});

// Grupo de rotas para pedidos (clientes fazem pedidos aqui)
Route::prefix('pedidos')->group(function () {
    
    // POST /api/pedidos - Cria um novo pedido
    Route::post('/', [PedidoController::class, 'store']);
    
    // GET /api/pedidos/{codigo} - Acompanha um pedido pelo código
    Route::get('/{codigo}', [PedidoController::class, 'show']);
});

// Grupo de rotas protegidas (autenticadas com Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Rotas de autenticação protegidas
    Route::prefix('auth')->group(function () {
        // POST /api/auth/logout - Logout do usuário
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // GET /api/auth/me - Obter dados do usuário autenticado
        Route::get('/me', [AuthController::class, 'me']);
        
        // PUT /api/auth/me - Atualizar perfil do usuário
        Route::put('/me', [AuthController::class, 'updateProfile']);
    });
    
    // Rotas de administração de produtos (apenas admin)
    Route::middleware([IsAdmin::class])->prefix('admin/produtos')->group(function () {
        Route::post('/', [ProdutoController::class, 'store']);
        Route::put('/{id}', [ProdutoController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [ProdutoController::class, 'destroy'])->where('id', '[0-9]+');
    });
    
    // Rotas de administração de pedidos (apenas admin)
    Route::middleware([IsAdmin::class])->prefix('admin/pedidos')->group(function () {
        Route::get('/', [PedidoController::class, 'index']);
        Route::put('/{id}/status', [PedidoController::class, 'updateStatus']);
        Route::get('/{id}', [PedidoController::class, 'adminShow'])->where('id', '[0-9]+');
    });
});

// Rota fallback para endpoints não encontrados
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint não encontrado',
        'documentation' => '/api/documentation'
    ], 404);
});
