<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Verifica si el usuario autenticado tiene el rol necesario
        if (!$request->user() || !$request->user()->hasRole($role)) {
            // Registro de un mensaje en los logs para verificar que el middleware se ejecutó
            Log::info("Acceso denegado para el rol requerido: $role. Usuario actual: " . optional($request->user())->name);

            // Retorna una excepción o respuesta personalizada si el rol no es adecuado
            throw UnauthorizedException::forRoles([$role]);
        }

        // Log para saber que el middleware permitió el acceso
        Log::info("Acceso permitido para el rol: $role. Usuario: " . $request->user()->name);

        return $next($request);
    }

    /*     public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check() || Auth::user()->estatus !== $role) {
            return response()->json(['error' => 'Acceso denegado'], 403);
        }

        return $next($request);
    }  */
}
