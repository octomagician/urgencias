<?php

namespace App\Http\Controllers;

use App\Models\Estudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstudiosController extends Controller
{
    public function index()
    {
        $estudios = Estudio::all();
        return response()->json([
            'estudios' => $estudios
        ], 200);
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
            return response()->json([
                'estudio' => $estudio
            ], 200);
        } else {
            $estudios = Estudio::all();
            return response()->json([
                'estudios' => $estudios
            ], 200);
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