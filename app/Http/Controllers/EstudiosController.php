<?php

namespace App\Http\Controllers;

use App\Models\Estudio;
use App\Models\TiposDeEstudio;
use App\Models\Personal;
use Illuminate\Http\Request;

class EstudiosController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'tipos_de_estudios_id' => 'required|exists:tipos_de_estudios,id',
            'personal_id' => 'required|exists:personal,id'
        ]);

        $estudio = Estudio::create([
            'tipos_de_estudios_id' => $request->tipos_de_estudios_id,
            'personal_id' => $request->personal_id
        ]);

        return response()->json($estudio, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $estudio = Estudio::find($id);
            if (!$estudio) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $estudio = Estudio::all();
        }

        return response()->json($estudio, 200);
    }

    public function update(Request $request, $id)
    {
        $estudio = Estudio::find($id);
        if (!$estudio) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $request->validate([
            'tipos_de_estudios_id' => 'required|exists:tipos_de_estudios,id',
            'personal_id' => 'required|exists:personal,id'
        ]);

        $estudio->update($request->only(['tipos_de_estudios_id', 'personal_id']));
        return response()->json($estudio);
    }

    public function delete($id)
    {
        $estudio = Estudio::find($id);
        if (!$estudio) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $estudio->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    }
}
