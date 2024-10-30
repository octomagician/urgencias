<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use App\Models\Token;

//users
class UserController extends Controller
{ 
    public function create(/* RegisterUser */Request $request)
    {
        try { 
            $register = Http::withOptions([
                'verify' => false,
            ])->post('http://192.168.120.231:3325/register', [                         
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            $node1=$register->json();
        
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required | string | email|unique:users,email',
                'password' => 'required|string|min:8'
            ]);

            $user = User::create([ //se hace por separado y no con all para hashear la clave
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) 
            ]);

            return response()->json([
                'message' => 'Usuario creado',
                'user' => $user,
                'node' => $node1], 201 );   
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
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
