<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Log as LogModel;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Excluir rutas específicas del logging
        if ($request->is('api/eventos-sse') || $request->is('api/logs*')) {
            return $next($request);
        }

        // Ejecutar la solicitud y obtener la respuesta
        $response = $next($request);

        // Verificar si hay un usuario autenticado
        if (Auth::check()) {
            $logData = [
                'action' => $request->route()?->getActionName() ?? 'unknown',
                'user_id' => Auth::id(),
                'details' => json_encode($request->all() ?: []),
            ];

            try {
                LogModel::create($logData);
            } catch (\Exception $e) {
                // Opcional: registrar el error de creación de log
                \Log::error('Error al crear log de actividad: '.$e->getMessage());
            }
        }

        return $response;
    }
}