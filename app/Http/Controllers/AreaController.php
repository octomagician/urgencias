<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return response()->json(['areas' => $areas], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $area = Area::create([
            'nombre' => $request->nombre
        ]);

        return response()->json(['area' => $area], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $area = Area::find($id);
            if (!$area) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
            return response()->json(['area' => $area], 200);
        } else {
            $areas = Area::all();
            return response()->json(['areas' => $areas], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $area->update($validator->validated());

        return response()->json([
            'mensaje' => 'Datos actualizados correctamente',
            'area' => $area
        ], 200);
    }

    public function delete($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $area->delete();
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }
}