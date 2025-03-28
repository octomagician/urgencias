<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Log as LogModel;

class EventosSSEController extends Controller
{
    public function streamEventos(Request $request)
    {
        set_time_limit(0); // Deshabilita el límite de tiempo
        ignore_user_abort(true); // Continúa la ejecución aunque el cliente se desconecte
    
        \Log::info('Headers recibidos:', $request->headers->all());
        \Log::info('Token recibido:', ['token' => $request->bearerToken()]);
    
        $user = $request->user();
        if (!$user) {
        \Log::error('Usuario no autenticado');
            return response()->json(['error' => 'No autorizado'], 401);
        }
    
        //return new StreamedResponse(function () use ($user) { //antes pasaba user
        return new StreamedResponse(function () use ($request) {  //ahora paso request
            $lastSentId = null;
            $iterationCount = 0; // Variable renombrada y definida correctamente
            
            while (true) {
                if (connection_aborted()) {
                    \Log::info("Cliente desconectado");
                    break;
                }
    
                try {
                    $query = LogModel::query();
                
                    if ($request->header('X-SSE-Filter')) {
                        $query->where('action', 'not like', '%LogController%');
                    }
    
                    if ($lastSentId) {
                        $query->where('_id', '>', $lastSentId);
                    }

                    // Consultar la base de datos solo cada 5 iteraciones
                    if ($iterationCount % 5 === 0) {
                        $query = LogModel::query();
                        
                        if ($lastSentId) {
                            $query->where('_id', '>', $lastSentId);
                        }
                        
                        $newLogs = $query->orderBy('_id', 'desc')
                                        ->limit(10)
                                        ->get();
                        
                        foreach ($newLogs as $log) {
                            echo "event: newLog\n";
                            echo "data: " . json_encode([
                                '_id' => $log->_id,
                                'action' => $log->action,
                                'user_id' => $log->user_id,
                                'details' => $log->details,
                                'created_at' => $log->created_at->toDateTimeString()
                            ]) . "\n\n";
                            $lastSentId = $log->_id;
                        }
                    } else {
                        // Enviar heartbeat para mantener la conexión
                        echo ": heartbeat\n\n";
                    }
                    
                    $iterationCount++;
                    ob_flush();
                    flush();
                    sleep(5); // Esperar 3 segundos entre iteraciones
                    
                } catch (\Exception $e) {
                    \Log::error("Error en SSE: " . $e->getMessage());
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }
}