<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="CoxinhaDelivery API", version="1.0.0", description="API do sistema")
 * @OA\Server(url="http://localhost/api")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/auth/register",
     * tags={"Autenticação"},
     * summary="Registrar novo usuário",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password","password_confirmation"},
     * @OA\Property(property="name", type="string", example="João"),
     * @OA\Property(property="email", type="string", format="email", example="joao@teste.com"),
     * @OA\Property(property="password", type="string", format="password", example="12345678"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="12345678")
     * )
     * ),
     * @OA\Response(response=201, description="Criado com sucesso")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        // 1. Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 2. Criação do usuário no banco
        // Nota: O banco preenche 'is_admin' como false (0) automaticamente
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Criação do Token de acesso (Login automático após registro)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Retorno da resposta (O objeto $user já inclui o campo is_admin)
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
     * @OA\Post(
     * path="/auth/login",
     * tags={"Autenticação"},
     * summary="Login no sistema",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@coxinha.com"),
     * @OA\Property(property="password", type="string", format="password", example="password")
     * )
     * ),
     * @OA\Response(response=200, description="Login realizado")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Validação simples
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 2. Tenta autenticar com email e senha
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        // 3. Busca o usuário no banco
        $user = User::where('email', $request->email)->firstOrFail();
        
        // 4. Remove tokens antigos (Login limpo) e gera um novo
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Retorna dados + Token
        // O Front-end deve olhar 'user.is_admin' para saber se libera o painel administrativo
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
     * @OA\Post(
     * path="/auth/logout",
     * tags={"Autenticação"},
     * summary="Sair do sistema",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Logout realizado")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoga apenas o token que está sendo usado agora
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * @OA\Get(
     * path="/auth/me",
     * tags={"Autenticação"},
     * summary="Dados do usuário atual",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Dados retornados")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Dados do usuário',
            'data' => [
                'user' => $request->user() // Retorna nome, email e is_admin
            ]
        ]);
    }

    /**
     * @OA\Put(
     * path="/auth/me",
     * tags={"Autenticação"},
     * summary="Atualizar perfil",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Perfil atualizado")
     * )
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        // Validação (permite não enviar campos que não quer mudar)
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Atualiza apenas o que foi enviado
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'data' => ['user' => $user]
        ]);
    }
}
