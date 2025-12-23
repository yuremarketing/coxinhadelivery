<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado E se a coluna is_admin é verdadeira
        if ($request->user() && $request->user()->is_admin) {
            return $next($request);
        }

        // Se não for admin, retorna erro 403 (Proibido)
        return response()->json([
            'success' => false,
            'message' => 'Acesso negado. Esta rota é restrita a administradores.'
        ], 403);
    }
}
