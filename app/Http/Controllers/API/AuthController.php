<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registrar um novo usuário
     * POST /api/register
     */
    public function register(Request $request): JsonResponse
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cria o usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Cria token de acesso
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Login de usuário
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tentativa de autenticação
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        // Busca o usuário autenticado
        $user = User::where('email', $request->email)->firstOrFail();

        // Cria novo token (revoga tokens antigos se necessário)
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout do usuário (revoga token atual)
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoga o token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Obter dados do usuário autenticado
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Dados do usuário',
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }

    /**
     * Atualizar perfil do usuário
     * PUT /api/me
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Atualiza os campos fornecidos
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
