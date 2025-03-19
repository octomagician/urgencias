<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Persona;
use App\Http\Requests\PacienteRequest;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return response()->json([
            'pacientes' => $pacientes
        ], 200);
    }

    public function create(PacienteRequest $request)
    {
        // Crear la persona
        $persona = Persona::create($request->only([
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'sexo'
        ]));

        // Crear el paciente
        $paciente = Paciente::create([
            'persona_id' => $persona->id,
            'nacimiento' => $request->nacimiento,
            'nss' => $request->nss,
            'direccion' => $request->direccion,
            'tel_1' => $request->tel_1,
            'tel_2' => $request->tel_2,
        ]);

        return response()->json([
            'persona' => $persona,
            'paciente' => $paciente,
        ], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $paciente = Paciente::with('persona')->find($id);
            if (!$paciente) {
                return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
            }
            return response()->json([
                'paciente' => $paciente
            ], 200);
        } else {
            $pacientes = Paciente::all();
            return response()->json([
                'pacientes' => $pacientes
            ], 200);
        }
    }

    public function update(PacienteRequest $request, $id)
    {
        // Buscar el paciente
        $paciente = Paciente::find($id);
        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        // Actualizar el paciente
        $paciente->update($request->only([
            'nacimiento',
            'nss',
            'direccion',
            'tel_1',
            'tel_2'
        ]));

        return response()->json(['mensaje' => 'Datos actualizados correctamente'], 200);
    }

    public function delete($id)
    {
        $paciente = Paciente::find($id);
        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        $paciente->delete();
        return response()->json(null, 204);
    }
}