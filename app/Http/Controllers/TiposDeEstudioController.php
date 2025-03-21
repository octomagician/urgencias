<?php

namespace App\Http\Controllers;

use App\Models\TiposDeEstudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiposDeEstudioController extends Controller
{
    public function index()
    {
        $tiposDeEstudio = TiposDeEstudio::all();
        return response()->json([
            'tipos-de-estudio' => $tiposDeEstudio
        ], 200);
    }

    public function create(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear el tipo de estudio
        $tipoDeEstudio = TiposDeEstudio::create([
            'nombre' => $request->nombre
        ]);

        return response()->json([
            'tipos-de-estudio' => $tipoDeEstudio
        ], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $tipoDeEstudio = TiposDeEstudio::find($id);
            if (!$tipoDeEstudio) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
            return response()->json([
                'tipos-de-estudio' => $tipoDeEstudio
            ], 200);
        } else {
            $tiposDeEstudio = TiposDeEstudio::all();
            return response()->json([
                'tipos-de-estudio' => $tiposDeEstudio
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar y actualizar el tipo de estudio
        $tipoDeEstudio = TiposDeEstudio::find($id);
        if (!$tipoDeEstudio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $tipoDeEstudio->update($request->only(['nombre']));

        return response()->json([
            'mensaje' => 'Datos actualizados correctamente',
            'tipos-de-estudio' => $tipoDeEstudio
        ], 200);
    }

    public function delete($id)
    {
        $tipoDeEstudio = TiposDeEstudio::find($id);
        if (!$tipoDeEstudio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $tipoDeEstudio->delete();
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }
}