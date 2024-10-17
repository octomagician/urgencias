<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $areas = Area::create([
            'nombre' => $request->nombre
        ]);

        return response()->json($areas, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $areas = Area::find($id);
            if (!$areas) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $areas = Area::all();
        }
    
        return response()->json($areas, 200);
    }
    
    public function update(Request $request, $id)
    {
        $areas = Area::find($id);
        $areas->update($request->all());
        return response()->json($areas);
    }

    public function delete($id)
    {
        $areas = Area::find($id);
        $areas->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    }
}

