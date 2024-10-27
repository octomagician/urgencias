<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

class TokenController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'token1' => 'required|string',
                'token2' => 'required|string',
            ]);

            $token = Token::updateOrCreate(
                ['token1' => $request->token1],
                ['token2' => $request->token2]
            );

            return response()->json(['data' => $token], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request)
    {
        $request->validate([
            'token1' => 'required|string',
        ]);

        try {
            $token = Token::where('token1', $request->token1)->select('token2')->first();

            if (!$token) {
                return response()->json(['message' => 'Token1 not found for this token2'], 404);
            }

            return response()->json(['message' => 'Token retrieved successfully', 'data' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving token', 'error' => $e->getMessage()], 422);
        }
    }
    public function obtenerTokenDeApi2()
    {
        try { 
            $response = Http::post('URL_DE_API2/obtener-token', [
                'client_id' => 'tus_credentials',
                'client_secret' => 'tus_credentials',
            ]);

            if ($response->successful()) {
                Token::updateOrCreate(
                    ['api_name' => 'API2'],
                    [
                        'token' => $response->json('token'),
                        'expires_at' => now()->addMinutes(60),
                    ]
                );
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
