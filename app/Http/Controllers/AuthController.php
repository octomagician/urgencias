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
        $register = Http::withOptions([
            'verify' => false,
        ])->post('https://7fc8-2806-267-148b-15bf-125-e767-a22d-b16a.ngrok-free.app/login', [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $node1 = $register->json();

        // consegimos solo el token_2
        $token2 = $node1['token_2'];
        $token3 = $node1['token_3'];
        //$token4 = $node1['token_4'];
        

        //Http::post('https://fc3d-2806-267-1489-2a1-1d66-fa00-3a02-de6.ngrok-free.app/api/token-command', [
          //  'token3' => $token3,
            //'token4' => $token4,
       //]);
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
        ])->post('https://7fc8-2806-267-148b-15bf-125-e767-a22d-b16a.ngrok-free.app/token-command', [
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


    }
}
