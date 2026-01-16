<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui definimos as rotas para o Coxinha Delivery.
| Substituímos a rota curinga por rotas controladas.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vender', [VenderController::class, 'index'])->name('vender.index');
