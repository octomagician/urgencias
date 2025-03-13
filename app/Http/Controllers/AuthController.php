<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Mail\RegistroCorreo;
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
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Si la validación falla, retornar errores
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error',
                'error' => $validator->errors()
            ], 400);
        }
    
        // Intentar autenticar al usuario
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'mensaje' => 'Credenciales inválidas',
            ], 401);
        }
    
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Verificar si el correo electrónico está verificado
        if ($user->email_verified_at === null) {
            return response()->json(['mensaje' => 'Cuenta no activada'], 403);
        }
    
        // Crear un token de acceso para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Obtener el rol del usuario
        $role = $user->roles->first()->name;
    
        // Retornar respuesta exitosa
        return response()->json([
            'mensaje' => 'Autenticación exitosa',
            'token' => $token,
            'role' => $role
        ], 200);
    }
    
    public function activateAccount(Request $request, User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json(['mensaje' => 'La cuenta ya está activada'], 400);
        }
    
        try {
            DB::beginTransaction();
    
            $user->markEmailAsVerified();
    
            $user->removeRole('guest'); 
            $user->assignRole('Administrador');
    
            //$adminEmail = User::role('Administrador')->first()->email;
            //Mail::to($adminEmail)->send(new RegistroCorreoAdmin($user));
    
            DB::commit();
    
            return response()->json(['mensaje' => 'Cuenta activada exitosamente']);
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
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            if ($user->email_verified_at !== null) {
                return response()->json(['mensaje' => 'La cuenta ya está activada'], 400);
            }
            
            $signedUrl = URL::temporarySignedRoute(
                'activate.account',
                Carbon::now()->addMinutes(1),
                ['user' => $user->id]
            );

            Mail::to($user->email)->send(new RegistroCorreo($user, 'Confirmación requerida', $signedUrl));

            return response()->json(['mensaje' => 'Correo de activación reenviado']);
        }
        else 
        {  
            return response()->json(['mensaje' => 'Credenciales inválidas'], 422);
        }
    }

    public function authorizeUserRole(Request $request, User $user)
    {
/*         dd(auth()->user()->roles->pluck('name'));
        dd(auth()->user()->getAllPermissions()->pluck('name'));
        $adminRole = Spatie\Permission\Models\Role::findByName('Administrador');
        dd($adminRole->permissions);

        if (auth()->user()->cannot('authorize roles')) {
            return response()->json(['mensaje' => 'No autorizado'], 403);
        } */

        try {
            DB::beginTransaction();

            $user->removeRole('guest'); 
/*             $user->assignRole($user->requested_role); */

            DB::commit();

            return response()->json(['mensaje' => 'Rol de usuario autorizado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al autorizar rol de usuario: " . $e->getmensaje());
            return response()->json(['error' => 'Ocurrió un problema al autorizar el rol'], 500);
        }
    }

    // -------------------------------------------------------------------

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada correctamente.'], 200);
    }

    public function resetPassword(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();
    
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string', // Contraseña actual
            'new_password' => 'required|string|min:8|confirmed', // Nueva contraseña
        ]);
    
        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta',
            ], 401);
        }
    
        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        // Devolver una respuesta exitosa
        return response()->json([
            'message' => 'Contraseña cambiada correctamente',
        ], 200);
    }
}