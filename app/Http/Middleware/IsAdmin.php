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
        // Verifica se o usuário está autenticado
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autenticado'
            ], 401);
        }

        // TODO: Adicionar lógica para verificar se o usuário é admin
        // Por enquanto, vamos permitir qualquer usuário autenticado
        // No futuro, podemos adicionar um campo 'is_admin' na tabela users
        
        return $next($request);
    }
}
