<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenderController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

// --- MUDANÇA AQUI: Redireciona a home direto para o login ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/vender', [VenderController::class, 'index'])->name('vender.index');

// --- LOGIN COM GOOGLE ---
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::updateOrCreate([
            'email' => $googleUser->email,
        ], [
            'name' => $googleUser->name,
            'password' => Hash::make(Str::random(24)),
            'email_verified_at' => now(),
        ]);
        Auth::login($user);
        return redirect('/dashboard');
    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'Erro Google: ' . $e->getMessage());
    }
});

// --- ROTAS DE PERFIL ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\ProdutoController;

Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');
Route::get('/produtos/criar', [ProdutoController::class, 'create'])->name('produtos.create');
Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');

// --- ROTA DE EMERGÊNCIA PARA CONFIGURAR O SERVIDOR (SEM SHELL) ---
use Illuminate\Support\Facades\Artisan;

Route::get('/configurar-servidor', function () {
    try {
        // 1. Rodar as migrações (criar tabelas)
        Artisan::call('migrate', ['--force' => true]);
        $migracao = Artisan::output();

        // 2. Criar o link das imagens
        Artisan::call('storage:link');
        $link = Artisan::output();

        return "<h1>SUCESSO! SERVIDOR CONFIGURADO.</h1>
                <hr>
                <h3>Migração:</h3> <pre>$migracao</pre>
                <h3>Storage Link:</h3> <pre>$link</pre>
                <hr>
                <p>Agora pode acessar <a href='/produtos'>/produtos</a> que vai funcionar!</p>";

    } catch (\Exception $e) {
        return "<h1>ERRO CRÍTICO</h1> <pre>" . $e->getMessage() . "</pre>";
    }
});
