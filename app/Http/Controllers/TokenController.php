<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

class TokenController extends Controller
{
    // Método para crear o actualizar un token con token1 y token2
    public function store(Request $request)
    {
        // Valida los campos
        $request->validate([
            'token1' => 'required|string',
            'token2' => 'required|string',
        ]);

        try {
            // Actualiza o crea el registro del token según token1
            $token = Token::updateOrCreate(
                ['token1' => $request->token1],  // Buscar por token1
                ['token2' => $request->token2]   // Actualizar o crear con token2
            );

            return response()->json(['message' => 'Token updated or created successfully!', 'data' => $token], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error saving token', 'error' => $e->getMessage()], 500);
        }
    }

    // Método para mostrar token2 buscando por token1
    public function show(Request $request)
    {
        // Valida que token1 esté presente
        $request->validate([
            'token1' => 'required|string',
        ]);

        try {
            // Busca el token2 por token1
            $token = Token::where('token1', $request->token1)->select('token2')->first();

            // Si no se encuentra token
            if (!$token) {
                return response()->json(['message' => 'Token1 not found for this token2'], 404);
            }

            return response()->json(['message' => 'Token retrieved successfully', 'data' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving token', 'error' => $e->getMessage()], 500);
        }
    }
    public function obtenerTokenDeApi2()
    {
        $response = Http::post('URL_DE_API2/obtener-token', [
            'client_id' => 'tus_credentials',
            'client_secret' => 'tus_credentials',
        ]);

        if ($response->successful()) {
            Token::updateOrCreate(
                ['api_name' => 'API2'], // Condición para encontrar el registro
                [
                    'token' => $response->json('token'),
                    'expires_at' => now()->addMinutes(60), // Asumiendo que el token expira en 60 minutos
                ]
            );
        }
    }
}
