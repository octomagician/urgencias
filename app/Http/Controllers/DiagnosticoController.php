<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'dx' => 'required|string',
            'estatus' => 'required|in:sospechoso,confirmado,descartado'
        ]);

        $diagnostico = Diagnostico::create([
            'dx' => $request->dx,
            'estatus' => $request->estatus
        ]);

        return response()->json($diagnostico, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $diagnostico = Diagnostico::find($id);
            if (!$diagnostico) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $diagnostico = Diagnostico::all();
        }

        return response()->json($diagnostico, 200);
    }

    public function update(Request $request, $id)
    {
        $diagnostico = Diagnostico::find($id);
        if (!$diagnostico) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $request->validate([
            'dx' => 'required|string',
            'estatus' => 'required|in:sospechoso,confirmado,descartado'
        ]);

        $diagnostico->update($request->only(['dx', 'estatus']));
        return response()->json($diagnostico);
    }

    public function delete($id)
    {
        $diagnostico = Diagnostico::find($id);
        if (!$diagnostico) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $diagnostico->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    }
}