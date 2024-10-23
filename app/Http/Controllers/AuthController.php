<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\TokenController; // Asegúrate de importar el TokenController

class AuthController extends Controller
{
    /**
     * Handle login request and return the user with a Sanctum token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Eliminar tokens antiguos si es necesario
        $user->tokens()->delete();

        // Crear un nuevo token de acceso para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        // Ahora, instanciar TokenController y llamar a la función create para obtener el token de API2
        $tokenController = new TokenController();
        $tokenRequest = new Request([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $tokenResponse = $tokenController->create($tokenRequest);

        // Procesar la respuesta de la función create
        if ($tokenResponse->getStatusCode() !== 200) {
            return response()->json([
                'message' => 'No se pudo obtener el token de API2.',
                'error' => $tokenResponse->json('error'),
            ], $tokenResponse->getStatusCode());
        }

        // Retornar la respuesta con el token de API1 y la respuesta de API2
        return response()->json([
            'token_1' => $token,  // Token para API1
            'message' => 'Token de API2 guardado exitosamente.', // Mensaje opcional
        ]);
    }
}
