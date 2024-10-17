<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Support\Facades\Http;

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
        $register = Http::post('http://192.168.1.13:3325/login', [                         
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $node1=$register->json();
        // Validar los datos de entrada
        // Validar los campos del formulario de login
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

        // Retornar la respuesta con el token
        return response()->json([
            'token_1' => $token,
            'node' => $node1
        ]);
    }
}
