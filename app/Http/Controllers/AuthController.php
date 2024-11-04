<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\TokenController;
use App\Models\Token;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Mail\RegistroCorreo;
use App\Mail\RegistroCorreoAdmin;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales inválidas',
            ], 401);
        }
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error',
                'error' => $validator->errors()
            ]);
        }

        $user = Auth::user();

        if ($user->email_verified_at === null) { 
            return response()->json(['message' => 'Cuenta no activada'], 403);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;   
        $token = Token::updateOrCreate(
            ['token1' => $token], 
            ['token2' => 'null']
        );

        return response()->json([
            'message' => 'Autenticación exitosa',
            'token1' => $token
        ]); 
    }

    public function activateAccount(Request $request, User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'La cuenta ya está activada'], 400);
        }
    
        try {
            DB::beginTransaction();
    
            $user->markEmailAsVerified();
    
            $adminEmail = User::where('role', 'Administrador')->first()->email;
            Mail::to($adminEmail)->send(new RegistroCorreoAdmin($user));
    
            DB::commit();
    
            return response()->json(['message' => 'Cuenta activada exitosamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al activar cuenta: " . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un problema al activar la cuenta'], 500);
        }
    }

    public function resendActivation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            if ($user->email_verified_at !== null) {
                return response()->json(['message' => 'La cuenta ya está activada'], 400);
            }
            
            $signedUrl = URL::temporarySignedRoute(
                'activate.account',
                Carbon::now()->addMinutes(5),
                ['user' => $user->id]
            );

            Mail::to($user->email)->send(new RegistroCorreo($user, 'Confirmación requerida', $signedUrl));

            return response()->json(['message' => 'Correo de activación reenviado']);
        }
        else 
        {  
            return response()->json(['message' => 'Credenciales inválidas'], 422);
        }
    }
}