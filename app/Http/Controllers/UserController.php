<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Http\Requests\SelfUsuarioRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;
//use Exception; //para el trycatch

//para el correo con ruta firmada
use App\Mail\RegistroCorreo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;

//correo con clave de verificación
use Illuminate\Support\Str;
use App\Mail\RegistroCodigoCorreo;

//para los roles de usuario
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\Storage;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('persona')->get();
            return response()->json([
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(UsuarioRequest $request) 
    {
        DB::beginTransaction();
        try {
            // Crear la persona
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
            ]);
    
            // Crear el usuario
            $user = User::create([
                'persona_id' => $persona->id,
                'tipo_id' => $request->tipo_id,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'verification_code' => Str::random(6),
                'verification_code_expires_at' => now()->addMinutes(5)
            ]);
    
            // Asignar rol de invitado
            $user->assignRole('guest');
    
            // Generar URL firmada para verificación
            $verificationUrl = URL::temporarySignedRoute(
                'verificar-codigo', // Nombre de la ruta de verificación en tu API
                Carbon::now()->addMinutes(5), // Tiempo de expiración
                [
                    'id' => $user->id,
                    'hash' => sha1($user->email),
                    'code' => $user->verification_code
                ] //pasas el id y el hash como medida de seguridad estandar
            );
    
            // Obtengo la URI del front desde app.php http://localhost:4200
            // le añado los parámetros de la url firmada del backend
            // y el código de verificación. Lo paso para que se muestre en el correo por si el enlace falla
            // y para que el front pueda autocompletar el formulario
            $frontendUri = config('app.frontend_uri') . '/verificacion?'
                . http_build_query([
                'code' => $user->verification_code,
                'verify_url' => $verificationUrl,
                'expires_at' => Carbon::now()->addMinutes(5)->timestamp,
                ]);
    
            // Enviar correo de verificación
            Mail::to($user->email)->send(new RegistroCodigoCorreo(
                $user, 
                'Registro exitoso', 
                $user->verification_code, 
                $frontendUri
            ));
    
            DB::commit();

            \Log::debug('Datos mandados al front:', [
                'frontendUri' =>  $frontendUri,
                'code' => $user->verification_code,
                'verify_url' => $verificationUrl,
                'expires_at' => Carbon::now()->addMinutes(5)->timestamp,
            ]);
    
            return response()->json([
                'mensaje' => 'Usuario creado, favor de revisar su correo para seguir con el proceso.',
                'user' => $user,
                'persona' => $persona,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al crear el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function read($id = null)
    {
        try {
            if ($id) {
                $user = User::with('persona')->findOrFail($id);
                return response()->json([
                    'user' => $user,
                ], 200);
            } else {
                $users = User::with('persona')->get();
                return response()->json([
                    'users' => $users,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el usuario: ' . $e->getMessage()
            ], 404);
        }
    }

    public function update(UsuarioRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $persona = $user->persona;

            // Actualizar la persona
            $persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
            ]);

            // Actualizar el usuario
            $user->update([
                'tipo_id' => $request->tipo_id,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            DB::commit();

            return response()->json([
                'mensaje' => 'Usuario y persona actualizados correctamente.',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $persona = $user->persona;

            // Eliminar el usuario y la persona
            $user->delete();
            $persona->delete();

            DB::commit();

            return response()->json([
                'mensaje' => 'Usuario y persona eliminados correctamente.'
            ], 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function readSelf()
{
    try {
        $user = auth()->user()->load('persona');
        
        return response()->json([
            'user' => $user,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al obtener el usuario: ' . $e->getMessage()
        ], 404);
    }
}

    public function updateSelf(SelfUsuarioRequest $request)
{
    DB::beginTransaction();
    try {
        $user = auth()->user();
        $persona = $user->persona;

        // Actualizar la persona
        $persona->update($request->only([
            'nombre', 
            'apellido_paterno', 
            'apellido_materno', 
            'sexo'
        ]));

        // Preparar datos del usuario
        $userData = $request->only([
            'tipo_id',
            'username',
            'email'
        ]);

        // Actualizar contraseña solo si se proporcionó
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Actualizar el usuario
        $user->update($userData);

        DB::commit();

        return response()->json([
            'mensaje' => 'Usuario y persona actualizados correctamente.',
            'user' => $user->fresh(),
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error al actualizar el usuario: ' . $e->getMessage()
        ], 500);
    }
}

public function deleteSelf()
{
    DB::beginTransaction();
    try {
        // Obtener el usuario autenticado
        $user = auth()->user();
        $persona = $user->persona;

        // Eliminar el usuario y la persona
        $user->delete();
        $persona->delete();

        DB::commit();

        return response()->json([
            'mensaje' => 'Usuario y persona eliminados correctamente.'
        ], 204);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error al eliminar el usuario: ' . $e->getMessage()
        ], 500);
    }
}
}