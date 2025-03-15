<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;
use Exception; //para el trycatch

//para el correo con ruta firmada
use App\Mail\RegistroCorreo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;

//para los roles de usuario
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\Storage;
use App\Models\Persona;

class UserController extends Controller
{ 
    use Notifiable, HasRoles;

    public function create(UsuarioRequest $request){
    
        \DB::beginTransaction();

        try {
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
            ]);

            $user = User::create([
                'persona_id' => $persona->id,
                'tipo_id' => $request->tipo_id,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        
            $user->assignRole('guest');
        
            if (!$user) {
                return response()->json([
                    'mensaje' => 'No se pudo crear el usuario'
                ], 500);
            }

            if (isset($user)) { 

                $signedUrl = URL::temporarySignedRoute(
                    'activate.account', // nombre de la ruta
                    Carbon::now()->addMinutes(5),
                    ['user' => $user->id]
                );
        
                Mail::to($user->email)->send(new RegistroCorreo($user, 'Registro exitoso', $signedUrl));
                
                return response()->json([
                    'mensaje' => 'Usuario creado, favor de revisar su correo para seguir con el proceso.',
                    'user' => $user,
                ], 201);
            }
            } catch (\Exception $e) {
                // Revertir la transacción en caso de error
                \DB::rollBack();
    
                // Devolver una respuesta JSON de error
                return response()->json([
                    'mensaje' => 'No se pudo crear el usuario',
                    'error' => $e->getMessage(),
                ], 500);
            }
    }

    public function read($id = null)
    {
        try { 
            if ($id) {
                $user = User::find($id);
                if (!$user) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
            } else {
                $user = User::all();
            }
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }  
    }

    public function index()
    {
        try { 
            $users = User::all();
            return response()->json($users, 200);
        } catch (\Illuminate\Database\QueryException $e) { 
            return response()->json(['error' => 'Error al consultar los datos de la base de datos.'], 500);
        } catch (\Exception $e) { 
            return response()->json(['error' => 'Ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.'], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try { 
            $user = User::find($id);
            $user->update($request->all());
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } 
    }

    public function delete($id)
    {
        try { 
            $user = User::find($id);
            $user->delete();
            return response()->json(['message' => 'Usuario eliminado'], 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } 
    }

    public function uploadPP(Request $request)
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

       $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        /* $user = Auth::user(); */

        if ($user->profile_photo_path) {
            Storage::disk('spaces')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile_pictures', 'spaces');
        $user->update(['profile_photo_path' => $path]);

        $photoUrl = Storage::disk('spaces')->url($path);

        return response()->json(['message' => 'Foto de perfil actualizada', 'photo_url' => $photoUrl]);
    }

    public function deletePP()
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        if ($user->profile_photo_path) {
            Storage::disk('spaces')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
            return response()->json(['message' => 'Foto de perfil eliminada']);
        }
        return response()->json(['error' => 'No hay foto de perfil para eliminar'], 404);
    }

    public function downloadPP()
    {
        $user = Auth::user();

        if (!$user->profile_photo_path || !Storage::disk('spaces')->exists($user->profile_photo_path)) {
            return response()->json(['error' => 'Foto de perfil no encontrada'], 404);
        }

        $fileContent = Storage::disk('spaces')->get($user->profile_photo_path);

        return response($fileContent)->header('Content-Type', 'image/png');
    }

}
