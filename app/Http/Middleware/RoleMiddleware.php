<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Si no se pasan roles, denegar acceso
        if (empty($roles)) {
            Log::info("Acceso denegado: No se especificaron roles.");
            throw UnauthorizedException::forRoles($roles);
        }

        // Obtiene el usuario autenticado
        $user = $request->user();
        //dd($user);

        // Si no hay usuario autenticado, denegar acceso
        if (!$user) {
            Log::info("Acceso denegado: Usuario no autenticado.");
            throw UnauthorizedException::forRoles($roles);
        }

        // Verifica si el usuario tiene al menos uno de los roles requeridos
        foreach ($roles as $role) {
            if ($user->hasRole(trim($role))) { // Usa trim para eliminar espacios en blanco
                Log::info("Acceso permitido para el rol: $role. Usuario: " . $user->name);
                return $next($request);
            }
        }

        // Si el usuario no tiene ninguno de los roles, denegar acceso
        Log::info("Acceso denegado para los roles requeridos: " . implode(', ', $roles) . ". Usuario actual: " . $user->name);
        throw UnauthorizedException::forRoles($roles);
    }
}