<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Valida os dados de entrada
        $credenciais = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tenta autenticar
        if (Auth::attempt($credenciais)) {
            $user = Auth::user();
            
            // Cria um Token (usando Laravel Sanctum)
            // O User precisa ter o trait HasApiTokens (padrão no Laravel)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login realizado com sucesso!',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        }

        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }
}
