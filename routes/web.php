<?php

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

Route::get('/criar-admin-master', function () {
    User::firstOrCreate(
        ['email' => 'admin@teste.com'],
        [
            'name' => 'Yuri Admin',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]
    );
    return "Admin criado!";
});

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

require __DIR__.'/auth.php';
