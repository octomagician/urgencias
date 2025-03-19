<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Http\Requests\HistorialRequest;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index()
    {
        $historial = Historial::all();
        return response()->json([
            'historial' => $historial
        ], 200);
    }

    public function create(HistorialRequest $request)
    {
        $historial = Historial::create($request->validated());
        return response()->json($historial, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $historial = Historial::find($id);
            if (!$historial) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
        } else {
            $historial = Historial::all();
        }

        return response()->json([
            'historial' => $historial
        ], 200);
    }

    public function update(HistorialRequest $request, $id)
    {
        $historial = Historial::find($id);
        if (!$historial) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $historial->update($request->validated());
        return response()->json(['mensaje' => 'Datos actualizados correctamente'], 200);
    }

    public function delete($id)
    {
        $historial = Historial::find($id);
        if (!$historial) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $historial->delete();
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }
}