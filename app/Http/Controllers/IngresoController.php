<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Http\Requests\IngresoRequest;

class IngresoController extends Controller
{
    public function index()
    {
        $ingresos = Ingreso::all();
        return response()->json([
            'ingresos' => $ingresos
        ], 200);
    }

    public function create(IngresoRequest $request)
    {
        $ingreso = Ingreso::create($request->validated());
        
        return response()->json([
            'mensaje' => 'Ingreso creado exitosamente',
            'ingreso' => $ingreso
        ], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $ingreso = Ingreso::find($id);
            if (!$ingreso) {
                return response()->json(['mensaje' => 'Ingreso no encontrado'], 404);
            }
            return response()->json([
                'ingreso' => $ingreso
            ], 200);
        }
        
        $ingresos = Ingreso::all();
        return response()->json([
            'ingresos' => $ingresos
        ], 200);
    }

    public function update(IngresoRequest $request, $id)
    {
        $ingreso = Ingreso::find($id);
        if (!$ingreso) {
            return response()->json(['mensaje' => 'Ingreso no encontrado'], 404);
        }

        $ingreso->update($request->validated());
        
        return response()->json([
            'mensaje' => 'Ingreso actualizado correctamente',
            'ingreso' => $ingreso
        ], 200);
    }

    public function delete($id)
    {
        $ingreso = Ingreso::find($id);
        if (!$ingreso) {
            return response()->json(['mensaje' => 'Ingreso no encontrado'], 404);
        }

        $ingreso->delete();
        return response()->json(['mensaje' => 'Ingreso eliminado'], 204);
    }
}