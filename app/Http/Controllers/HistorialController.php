<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\Ingreso;
use App\Models\Personal;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'ingreso_id' => 'required|exists:ingresos,id',
            'personal_id' => 'required|exists:personal,id',
            'presion' => 'required|string|max:10',
            'temperatura' => 'required|numeric|between:0,100.00',
            'glucosa' => 'required|numeric|between:0,999.99',
            'sintomatologia' => 'required|string',
            'observaciones' => 'nullable|string'
        ]);

        $historial = Historial::create([
            'ingreso_id' => $request->ingreso_id,
            'personal_id' => $request->personal_id,
            'presion' => $request->presion,
            'temperatura' => $request->temperatura,
            'glucosa' => $request->glucosa,
            'sintomatologia' => $request->sintomatologia,
            'observaciones' => $request->observaciones
        ]);

        return response()->json($historial, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $historial = Historial::find($id);
            if (!$historial) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $historial = Historial::all();
        }

        return response()->json($historial, 200);
    }

    public function update(Request $request, $id)
    {
        $historial = Historial::find($id);
        if (!$historial) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $request->validate([
            'ingreso_id' => 'required|exists:ingresos,id',
            'personal_id' => 'required|exists:personal,id',
            'presion' => 'required|string|max:10',
            'temperatura' => 'required|numeric|between:0,100.00',
            'glucosa' => 'required|numeric|between:0,999.99',
            'sintomatologia' => 'required|string',
            'observaciones' => 'nullable|string'
        ]);

        $historial->update($request->only([
            'ingreso_id',
            'personal_id',
            'presion',
            'temperatura',
            'glucosa',
            'sintomatologia',
            'observaciones'
        ]));

        return response()->json($historial);
    }

    public function delete($id)
    {
        $historial = Historial::find($id);
        if (!$historial) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $historial->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    }
}