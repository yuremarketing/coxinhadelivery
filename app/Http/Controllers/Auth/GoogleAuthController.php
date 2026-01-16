<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Http\JsonResponse;

class GoogleAuthController extends Controller
{
    public function handleGoogleCallback(): JsonResponse
    {
        try {
            // Em uma API pura, o Front-end envia um Token, mas aqui mantemos o Socialite
            // para validar o estado da autenticação vinda do Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'role' => 'cliente',
                ]
            );

            // GERAÇÃO DO TOKEN SANCTUM (Em vez de Session Login)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'must_verify_phone' => $user->telefone_verificado_at === null
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha na autenticação com Google',
                'details' => $e->getMessage()
            ], 401);
        }
    }
}
