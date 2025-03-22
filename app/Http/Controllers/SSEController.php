<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SSEController extends Controller
{
    public function test()
    {
        return response()->stream(function () {
            $startTime = time();
            while (true) {
                // Verificar si el cliente se ha desconectado
                if (connection_aborted()) {
                    break;
                }

                // Enviar un mensaje al cliente
                echo "data: " . json_encode(['message' => 'Hello, world!']) . "\n\n";
                ob_flush();
                flush();

                // Limitar el tiempo de ejecución (opcional)
                if (time() - $startTime > 60) { // 60 segundos
                    break;
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
