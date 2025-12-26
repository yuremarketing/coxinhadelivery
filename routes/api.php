<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;

// Definimos a rota de POST sem o bloqueio de autenticação
Route::post('/pedidos', [PedidoController::class, 'store']);
