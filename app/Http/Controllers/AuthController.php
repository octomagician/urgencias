<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\TokenController;
use App\Models\Token;

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
        try { 
            $register = Http::withOptions([
                'verify' => false,
            ])->post('http://192.168.118.187:3325/login', [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            $node1 = $register->json();

            $token2 = $node1['token_2'];
            $token3 = $node1['token_3'];

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
            Http::withOptions([
                'verify' => false,
            ])->post('http://192.168.118.187:3325/token-command', [
                'token2' => $token2,
                'token3' => $token3
            ]);
            
            // Actualiza o crea el registro del token según token1
            $token = Token::updateOrCreate(
                ['token1' => $token],  // Buscar por token1
                ['token2' => $token2]   // Actualizar o crear con token2
            );

            // Retornar la respuesta con el token
            return response()->json([
                //'message' => 'Autenticación exitosa',
                'token1' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }  
    }
}
