<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;

use App\Models\Token;

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

class UserController extends Controller
{ 
    use Notifiable, HasRoles;

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|in:user,administrador,guest',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación',
                    'errors' => $validator->errors()
                ], 422);
            }

        $user = User::create([
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole($request->role);

            if (!$user) {
                return response()->json([
                    'message' => 'No se pudo crear el usuario'
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
                    'message' => 'Usuario creado, favor de revisar su correo para seguir con el proceso.',
                    'user' => $user,
                ], 201);
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

}
