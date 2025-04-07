<?php

namespace App\Http\Controllers;

use App\Models\Estudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\TiposDeEstudio;
use App\Models\Persona;

class EstudiosController extends Controller
{
    public function index()
    {
        $estudios = Estudio::all();
        $estudios = $estudios->map(function ($estudio) {
            $user = User::find($estudio->user_id);
            $persona = Persona::find($user->persona_id);
            $tipo = TiposDeEstudio::find($estudio->tipos_de_estudios_id);
            return [
                'id' => $estudio->id,
                'tipo_estudio' => $tipo->nombre,
                'personal' => $persona->nombre . ' ' . $persona->apellido_paterno . ' ' . $persona->apellido_materno,
                'fecha' => $estudio->created_at
            ];
        });
        return response()->json(['estudios' => $estudios], 200);
    }

    public function create(Request $request)
    {
        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'tipos_de_estudios_id' => 'required|exists:tipos_de_estudios,id',
            'user_id' => 'required|exists:users,id'
        ]);

        // Si la validación falla, retornar errores
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Crear el estudio
        $estudio = Estudio::create($validator->validated());

        return response()->json($estudio, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $estudio = Estudio::find($id);
            if (!$estudio) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
            $user = User::find($estudio->user_id);
            $persona = Persona::find($user->persona_id);
            $tipo = TiposDeEstudio::find($estudio->tipos_de_estudios_id);
            return [
                'id' => $estudio->id,
                'tipo_estudio' => $tipo->nombre,
                'personal' => $persona->nombre . ' ' . $persona->apellido_paterno . ' ' . $persona->apellido_materno,
                'fecha' => $estudio->created_at
            ];
        } else {
            $estudios = Estudio::all();
            $estudios = $estudios->map(function ($estudio) {
                $user = User::find($estudio->user_id);
                $persona = Persona::find($user->persona_id);
                $tipo = TiposDeEstudio::find($estudio->tipos_de_estudios_id);
                return [
                    'id' => $estudio->id,
                    'tipo_estudio' => $tipo->nombre,
                    'personal' => $persona->nombre . ' ' . $persona->apellido_paterno . ' ' . $persona->apellido_materno,
                    'fecha' => $estudio->created_at
                ];
            });
            return response()->json(['estudios' => $estudios], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $estudio = Estudio::find($id);
        if (!$estudio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'tipos_de_estudios_id' => 'required|exists:tipos_de_estudios,id',
            'user_id' => 'required|exists:users,id'
        ]);

        // Si la validación falla, retornar errores
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Actualizar el estudio
        $estudio->update($validator->validated());

        return response()->json(['mensaje' => 'Datos actualizados correctamente'], 200);
    }

    public function delete($id)
    {
        $estudio = Estudio::find($id);
        if (!$estudio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $estudio->delete();
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }
}