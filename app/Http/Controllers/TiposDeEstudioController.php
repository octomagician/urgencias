<?php

namespace App\Http\Controllers;

use App\Models\TiposDeEstudio;
use Illuminate\Http\Request;

class TiposDeEstudioController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $tipo_de_estudio = TiposDeEstudio::create([
            'nombre' => $request->nombre
        ]);

        return response()->json($tipo_de_estudio, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $tipo_de_estudio = TiposDeEstudio::find($id);
            if (!$tipo_de_estudio) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $tipo_de_estudio = TiposDeEstudio::all();
        }
    
        return response()->json($tipo_de_estudio, 200);
    }
    
    public function update(Request $request, $id)
    {
        $tipo_de_estudio = TiposDeEstudio::find($id);
        if (!$tipo_de_estudio) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $tipo_de_estudio->update($request->only(['nombre']));
        return response()->json($tipo_de_estudio);
    }

    public function delete($id)
    {
        $tipo_de_estudio = TiposDeEstudio::find($id);
        if (!$tipo_de_estudio) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $tipo_de_estudio->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    }
}
