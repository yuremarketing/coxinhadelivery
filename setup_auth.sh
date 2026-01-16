#!/bin/bash

echo "🔐 Configurando Autenticação (Login)..."

# 1. CRIAR O AUTH CONTROLLER
cat << 'EOF' > app/Http/Controllers/AuthController.php
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
EOF

# 2. ADICIONAR ROTA DE LOGIN
# Adiciona apenas se não existir ainda para evitar duplicidade
if ! grep -q "/login" routes/api.php; then
    echo "" >> routes/api.php
    echo "// Rota de Autenticação" >> routes/api.php
    echo "Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);" >> routes/api.php
    echo "✅ Rota /login adicionada."
else
    echo "⚠️ Rota /login já existia."
fi

# 3. TESTAR O LOGIN
echo "---------------------------------------------------"
echo "🧪 Testando Login com o usuário Admin..."
echo "Email: admin@coxinha.com | Senha: password"

# Faz o POST para pegar o token
docker compose exec laravel.test curl -s -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "email": "admin@coxinha.com",
        "password": "password"
    }' | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se você ver um 'access_token' gigante acima, o sistema de segurança está PRONTO!"
