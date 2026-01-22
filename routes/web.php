<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenderController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Artisan;

// --- AQUI ESTÁ A MUDANÇA: A Home agora mostra o Cardápio ---
Route::get('/', [ProdutoController::class, 'cardapio'])->name('cardapio');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/vender', [VenderController::class, 'index'])->name('vender.index');

// Google Auth
Route::get('/auth/google', function () { return Socialite::driver('google')->redirect(); })->name('auth.google');
Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::updateOrCreate(['email' => $googleUser->email], [
            'name' => $googleUser->name, 'password' => Hash::make(Str::random(24)), 'email_verified_at' => now(),
        ]);
        Auth::login($user);
        return redirect()->route('dashboard');
    } catch (\Exception $e) { return redirect('/login'); }
});

// --- ÁREA PROTEGIDA ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROTAS DE PRODUTOS COMPLETAS
    Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');
    Route::get('/produtos/criar', [ProdutoController::class, 'create'])->name('produtos.create');
    Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');
    
    // Novas rotas de Edição e Exclusão
    Route::get('/produtos/{id}/editar', [ProdutoController::class, 'edit'])->name('produtos.edit');
    Route::put('/produtos/{id}', [ProdutoController::class, 'update'])->name('produtos.update');
    Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');
});

Route::get('/configurar-servidor', function () {
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('storage:link');
    return "OK";
});

require __DIR__.'/auth.php';
