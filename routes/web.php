<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenderController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vender', [VenderController::class, 'index'])->name('vender.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

// --- ROTA DE LIMPEZA (Útil para emergências) ---
Route::get('/limpar-cache', function() {
    \Artisan::call('config:clear');
    \Artisan::call('cache:clear');
    return 'Cache limpo!';
});

// --- ROTAS DE PERFIL (CORREÇÃO DO ERRO) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
