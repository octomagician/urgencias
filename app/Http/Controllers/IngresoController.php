<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Paciente;
use App\Models\Diagnostico;
use App\Models\Cama;
use App\Models\Personal;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'pacientes_id' => 'required|exists:pacientes,id',
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'camas_id' => 'required|exists:camas,id',
            'personal_id' => 'required|exists:personal,id',
            'fecha_ingreso' => 'required|date',
            'motivo_ingreso' => 'required|string',
            'fecha_alta' => 'nullable|date'
        ]);

        $ingreso = Ingreso::create([
            'pacientes_id' => $request->pacientes_id,
            'diagnostico_id' => $request->diagnostico_id,
            'camas_id' => $request->camas_id,
            'personal_id' => $request->personal_id,
            'fecha_ingreso' => $request->fecha_ingreso,
            'motivo_ingreso' => $request->motivo_ingreso,
            'fecha_alta' => $request->fecha_alta
        ]);

        return response()->json($ingreso, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $ingreso = Ingreso::find($id);
            if (!$ingreso) {
                return response()->json(['message' => 'Ingreso no encontrado'], 404);
            }
        } else {
            $ingreso = Ingreso::all();
        }

        return response()->json($ingreso, 200);
    }

    public function update(Request $request, $id)
    {
        $ingreso = Ingreso::find($id);
        if (!$ingreso) {
            return response()->json(['message' => 'Ingreso no encontrado'], 404);
        }

        $request->validate([
            'pacientes_id' => 'required|exists:pacientes,id',
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'camas_id' => 'required|exists:camas,id',
            'personal_id' => 'required|exists:personal,id',
            'fecha_ingreso' => 'required|date',
            'motivo_ingreso' => 'required|string',
            'fecha_alta' => 'nullable|date'
        ]);

        $ingreso->update($request->only([
            'pacientes_id',
            'diagnostico_id',
            'camas_id',
            'personal_id',
            'fecha_ingreso',
            'motivo_ingreso',
            'fecha_alta'
        ]));

        return response()->json($ingreso);
    }

    public function delete($id)
    {
        $ingreso = Ingreso::find($id);
        if (!$ingreso) {
            return response()->json(['message' => 'Ingreso no encontrado'], 404);
        }

        $ingreso->delete();
        return response()->json(['message' => 'Ingreso eliminado'], 204);
    }
}
