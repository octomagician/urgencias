<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PacienteController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            'nacimiento' => 'required|date',
            'nss' => 'required|string|max:11|unique:pacientes',
            'direccion' => 'required|string|max:100',
            'tel_1' => 'required|string|max:20',
            'tel_2' => 'nullable|string|max:20',
        ]);

         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
         ]);

        $persona = Persona::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'sexo' => $request->sexo,
        ]);
    
         $paciente = Paciente::create([
            'persona_id' => $persona->id,
            'nacimiento' => $request->nacimiento,
            'nss' => $request->nss,
            'direccion' => $request->direccion,
            'tel_1' => $request->tel_1,
            'tel_2' => $request->tel_2,
        ]);

        return response()->json($user, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $px = Paciente::with('persona')->find($id);
            
            if (!$px) {
                return response()->json(['message' => 'Paciente no encontrado'], 404);
            }

            return response()->json([
                'paciente' => $px
            ], 200);

        } else {
            return response()->json(['message' => 'Servicio para consultarlos a todos no disponible'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nacimiento' => 'required|date',
            'nss' => 'required|string',
            'direccion' => 'required|string',
            'tel_1' => 'required|string',
            'tel_2' => 'nullable|string'
        ]);
    
        $px = Paciente::find($id);
        if (!$px) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }
    
        $px->update($request->only([
            'nacimiento',
            'nss',
            'direccion',
            'tel_1',
            'tel_2'
        ]));
    
        return response()->json(['message' => 'Paciente actualizado correctamente'], 200);
    }
    
    public function delete($id)
    {
        $px = Paciente::find($id);
        $px->delete();
       return response()->json(['message' => 'Paciente eliminado'], 204);
    }
}
