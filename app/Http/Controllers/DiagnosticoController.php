<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiagnosticoController extends Controller
{
    public function index()
    {
        $diagnosticos = Diagnostico::all();
        return response()->json(['diagnosticos' => $diagnosticos], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dx' => 'required|string',
            'estatus' => 'required|in:sospechoso,confirmado,descartado'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $diagnostico = Diagnostico::create([
            'dx' => $request->dx,
            'estatus' => $request->estatus
        ]);

        return response()->json(['diagnostico' => $diagnostico], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $diagnostico = Diagnostico::find($id);
            if (!$diagnostico) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
            return response()->json(['diagnostico' => $diagnostico], 200);
        } else {
            $diagnosticos = Diagnostico::all();
            return response()->json(['diagnosticos' => $diagnosticos], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $diagnostico = Diagnostico::find($id);
        if (!$diagnostico) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'dx' => 'required|string',
            'estatus' => 'required|in:sospechoso,confirmado,descartado'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $diagnostico->update($request->only(['dx', 'estatus']));
        return response()->json(['mensaje' => 'Datos actualizados correctamente'], 200);
    }

    public function delete($id)
    {
        $diagnostico = Diagnostico::find($id);
        if (!$diagnostico) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $diagnostico->delete();
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }
}