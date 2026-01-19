<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenderController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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


Route::get('/criar-admin-master', function () {
    $user = User::create([
        'name' => 'Yuri Admin',
        'email' => 'admin@teste.com', // Use este e-mail para logar
        'password' => Hash::make('12345678'), // Sua senha
        'role' => 'admin', // Aqui definimos que ele é o patrão
    ]);

    return "Usuário Administrador criado com sucesso!";
});
require __DIR__.'/auth.php';