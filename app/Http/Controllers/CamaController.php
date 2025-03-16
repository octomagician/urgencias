<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CamaController extends Controller
{
    public function index()
    {
        $camas = Cama::all();
        return response()->json(['camas' => $camas], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_cama' => 'required|integer|unique:camas,numero_cama',
            'area_id' => 'required|exists:areas,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $cama = Cama::create([
            'numero_cama' => $request->numero_cama,
            'area_id' => $request->area_id
        ]);

        return response()->json(['cama' => $cama], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $cama = Cama::find($id);
            if (!$cama) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
            return response()->json(['cama' => $cama], 200);
        } else {
            $camas = Cama::all();
            return response()->json(['camas' => $camas], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $cama = Cama::find($id);
        if (!$cama) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_cama' => 'required|integer|unique:camas,numero_cama,' . $cama->id,
            'area_id' => 'required|exists:areas,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $cama->update($request->only(['numero_cama', 'area_id']));
        return response()->json(['message' => 'Datos actualizados correctamente'], 200);
    }

    public function delete($id)
    {
        $cama = Cama::find($id);
        if (!$cama) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $cama->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    }
}