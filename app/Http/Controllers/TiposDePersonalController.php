<?php

namespace App\Http\Controllers;

use App\Models\TiposDePersonal;
use Illuminate\Http\Request;

class TiposDePersonalController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $tipo_de_personal = TiposDePersonal::create([
            'nombre' => $request->nombre
        ]);

        return response()->json($tipo_de_personal, 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $tipo_de_personal = TiposDePersonal::find($id);
            if (!$tipo_de_personal) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $tipo_de_personal = TiposDePersonal::all();
        }
    
        return response()->json($tipo_de_personal, 200);
    }
    
    public function update(Request $request, $id)
    {
        $tipo_de_personal = TiposDePersonal::find($id);
        $tipo_de_personal->update($request->all());
        return response()->json($tipo_de_personal);
    }

    public function delete($id)
    {
        $tipo_de_personal = TiposDePersonal::find($id);
        $tipo_de_personal->delete();
       return response()->json(['message' => 'Eliminado'], 204);
    }
}
