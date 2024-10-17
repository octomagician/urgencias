<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Persona;
use App\Models\TipoDePersonal;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',

            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',

            'tipo_id' => 'required|exists:tipos_de_personal,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password) 
        ]);

        $persona = Persona::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'sexo' => $request->sexo,
            'users_id' => $user->id,
        ]);

        $personal = Personal::create([
            'persona_id' => $persona->id,
            'tipo_id' => $request->tipo_id,
        ]);

        return response()->json([
            'user' => $user,
            'persona' => $persona,
        'personal' => $personal,],
     201);
    }

public function read($id = null)
{
    if ($id) {
        $personal = Personal::with(['persona', 'tipoDePersonal'])->find($id);
        
        if (!$personal) {
            return response()->json(['message' => 'Personal no encontrado'], 404);
        }

        return response()->json([
            'personal' => $personal
        ], 200);
    } else {
        return response()->json(['message' => 'Servicio para consultarlos a todos no disponible'], 404);
    }
}

public function update(Request $request, $id)
{
    $request->validate([
        'tipo_id' => 'required|exists:tipos_de_personal,id',
    ]);

    $personal = Personal::find($id);
    if (!$personal) {
        return response()->json(['message' => 'Personal no encontrado'], 404);
    }

    $personal->update($request->only([
        'tipo_id'
    ]));
}

public function delete($id)
{
    $personal = Personal::find($id);
    if (!$personal) {
        return response()->json(['message' => 'Personal no encontrado'], 404);
    }
    $personal->delete();
    return response()->json(['message' => 'Personal eliminado'], 204);
}

}
