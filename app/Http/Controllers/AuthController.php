<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

//correo con clave de verificación
use Illuminate\Support\Str;
use App\Mail\RegistroCodigoCorreo;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function verificarCodigo(Request $request)
    {
        \Log::debug('Datos recibidos:', [
            'params' => $request->all(),
            'firma_valida' => $request->hasValidSignature(),
            'url_completa' => $request->fullUrl()
        ]);

        // 1. Validar firma (incluye tiempo de expiración)
        if (!$request->hasValidSignature()) {
            abort(401, 'Enlace inválido o expirado');
        }
    
        // 2. Obtener parámetros necesarios
        if (!$request->has(['id', 'hash', 'code'])) {
            abort(422, 'Parámetros incompletos');
        }
    
        // 3. Buscar usuario
        $user = User::findOrFail($request->id);
    
        // 4. Validar hash del email
        if (sha1($user->email) !== $request->hash) {
            abort(403, 'No autorizado');
        }

        if ($user->verification_code_expires_at < now()) {
            abort(410, 'El código ha expirado'); // 410 Gone
        }
    
        // 5. Validar código
        if ($user->verification_code !== $request->code) {
            abort(422, 'Código de verificación incorrecto');
        }
    
        // 6. Marcar como verificado
        $user->update([
            'verification_code' => null
        ]);
        $user->markEmailAsVerified();
        $user->syncRoles('User');
    
        return response()->json([
            'success' => true,
            'message' => 'Email verificado correctamente'
        ]);
    }

    public function reenviarCodigo(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validación de credenciales
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'mensaje' => 'Error en la validación',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Buscar y autenticar usuario
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['mensaje' => 'Credenciales incorrectas'], 401);
            }
    
            // Verificar si ya está activado
            if ($user->email_verified_at) {
                return response()->json(['mensaje' => 'Este usuario ya está verificado'], 400);
            }
    
            // Verificar código existente y vigente
            if ($user->verification_code && $user->verification_code_expires_at) {
                $expirationDate = Carbon::parse($user->verification_code_expires_at);
                
                if ($expirationDate > now()) {
                    $tiempoRestante = $expirationDate->diffInSeconds(now());
                    
                    return response()->json([
                        'mensaje' => 'Ya existe un código de verificación activo',
                        'intenta_nuevamente_en' => $tiempoRestante,
                        'disponible_en' => $expirationDate->toDateTimeString()
                    ], 429);
                }
            }
    
            // Generar nuevo código con expiración
            $newCode = Str::random(6);
            $expiration = now()->addMinutes(5);
            
            $user->update([
                'verification_code' => $newCode,
                'verification_code_expires_at' => $expiration // ¡No olvides esto!
            ]);
    
            // Generar URL firmada
            $verificationUrl = URL::temporarySignedRoute(
                'verificar-codigo',
                $expiration, // Mismo tiempo que el código
                [
                    'id' => $user->id,
                    'hash' => sha1($user->email),
                    'code' => $newCode
                ]
            );
    
            // Construir URL para frontend
            $frontendUri = config('app.frontend_uri') . '/verificacion?' . http_build_query([
                'code' => $newCode,
                'verify_url' => $verificationUrl,
                'expires_at' => $expiration->timestamp
            ]);
    
            // Enviar correo
            Mail::to($user->email)->send(new RegistroCodigoCorreo(
                $user, 
                'Nuevo código de verificación', 
                $newCode, 
                $frontendUri
            ));
    
            DB::commit();
    
            return response()->json([
                'mensaje' => 'Nuevo código enviado',
                'expira_en' => $expiration->toDateTimeString()
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al reenviar código: ' . $e->getMessage());
            return response()->json(['error' => 'Error en el servidor'], 500);
        }
    }

    public function entrar(Request $request)
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
        //$token = $user->createToken('auth_token')->plainTextToken;
        $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;
    
        // Obtener el rol del usuario
        $role = $user->roles->first()->name;
    
        // Retornar respuesta exitosa
        return response()->json([
            'mensaje' => 'Autenticación exitosa',
            'token' => $token,
            'role' => $role,
            'username' => $user->username 
        ], 200);
    }

    public function salir(Request $request)
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