<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Http;

//users
class UserController extends Controller
{ 
    public function create(Request $request)
    {
        $register = Http::withOptions([
            'verify' => false,
        ])->post('https://9315-104-28-199-132.ngrok-free.app/register', [                         
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $node1=$register->json();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
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
            'node' => $node1], 201);    }

    public function read($id = null)
    {
        if ($id) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
        } else {
            $user = User::all();
        }
    
        return response()->json($user, 200);
    }
    
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
       return response()->json(['message' => 'Usuario eliminado'], 204);
    }

}
