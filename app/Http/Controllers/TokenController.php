<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;

class TokenController extends Controller
{
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
