<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\NfcToken;
use Symfony\Component\HttpFoundation\Response;

class TokenOwnershipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar en rutas de configuración de tokens
        if (!$request->route('tokenId')) {
            return $next($request);
        }

        $tokenId = $request->route('tokenId');
        $user = auth()->user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }

        // Verificar que el token existe y pertenece al usuario
        $token = NfcToken::find($tokenId);
        
        if (!$token) {
            abort(404, 'Token no encontrado.');
        }

        if ($token->user_id !== $user->id) {
            abort(403, 'No tienes permisos para acceder a este token.');
        }

        // Verificar permisos específicos
        if (!$user->can('configure_own_tokens')) {
            abort(403, 'No tienes permisos para configurar tokens.');
        }

        return $next($request);
    }
}