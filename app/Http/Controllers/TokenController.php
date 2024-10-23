<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Token;
use Illuminate\Support\Carbon;

class TokenController extends Controller
{
    public function create(Request $request)
    {
        $response = Http::post('https://9315-104-28-199-132.ngrok-free.app/login', [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if ($response->successful()) {
            $token = $response->json('token');

            // Verifica si el token es null
            if (is_null($token)) {
                return response()->json(['error' => 'El token recibido es nulo.'], 500);
            }

            Token::updateOrCreate(
                ['api_name' => 'API2'],
                [
                    'token' => $token, // Usar la variable $token aquí
                    'expires_at' => Carbon::now()->addMinutes(120),
                ]
            );

            return response()->json(['message' => 'Token guardado exitosamente.'], 200);
        } else {
            // Captura el contenido de la respuesta para más detalles en caso de error
            $errorContent = $response->getBody()->getContents();
            return response()->json(['error' => 'Error al obtener el token de la API2.', 'details' => $errorContent], $response->status());
        }
    }

    public function read()
    {
        $token = Token::where('api_name', 'API2')->first();

        if ($token) {
            return response()->json([
                'token' => $token->token,
                'expires_at' => $token->expires_at,
            ], 200);
        } else {
            return response()->json(['error' => 'No se encontró un token para API2.'], 404);
        }
    }
}
