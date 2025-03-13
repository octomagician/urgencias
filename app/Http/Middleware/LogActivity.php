<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Log as LogModel; // Alias para el modelo Log
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Ejecutar la solicitud y obtener la respuesta
        $response = $next($request);

        // Registrar el log en MongoDB
        $logData = [
            'action' => $request->route()->getActionName(), // Acción realizada
            'user_id' => Auth::id(), // ID del usuario autenticado
            'details' => json_encode($request->all()), // Detalles de la solicitud
        ];

        LogModel::create($logData);

        return $response;
    }
}