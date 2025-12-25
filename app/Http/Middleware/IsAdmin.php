<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * O Porteiro da Loja.
     * Regras da Casa:
     * 1. Admin: Entra em tudo, manda em tudo.
     * 2. Funcionário: Trabalha na cozinha (vê e atualiza pedidos).
     * - PROIBIDO: Ver Dashboard (dinheiro).
     * - PROIBIDO: Cancelar pedidos (delete).
     * 3. Cliente: Não entra na área administrativa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Quem é você? Faça login.'], 401);
        }

        // 1. O Dono (Admin) tem chave mestra.
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 2. O Funcionário (Carlos)
        if ($user->role === 'funcionario') {
            
            // Tentou entrar no escritório (Dashboard)? BARRA.
            if ($request->is('api/admin/dashboard')) {
                return response()->json(['message' => 'Opa! Sem olhar o caixa, beleza?'], 403);
            }

            // Tentou cancelar pedido (DELETE)? BARRA.
            if ($request->method() === 'DELETE') {
                return response()->json(['message' => 'Só o patrão pode cancelar pedido. Chame o gerente.'], 403);
            }

            // É pra trabalhar (GET ou PUT/PATCH)? LIBERA.
            return $next($request);
        }

        // 3. Cliente tentando entrar na cozinha
        return response()->json(['message' => 'Acesso negado. Área restrita.'], 403);
    }
}
