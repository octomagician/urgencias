<?php

namespace App\Http\Controllers;

use App\Models\TiposDePersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TiposDePersonalController extends Controller
{
    public function index()
    {
        $tiposDePersonal = TiposDePersonal::all();
        return response()->json([
            'tipos_de_personal' => $tiposDePersonal
        ], 200);
    }

    public function create(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            Log::warning('Fallo en la validación', ['errors' => $validator->errors()]);
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear el tipo de personal
        $tipoDePersonal = TiposDePersonal::create([
            'nombre' => $request->nombre
        ]);

        Log::info('Tipo de personal creado exitosamente', ['tipo_de_personal' => $tipoDePersonal]);

        return response()->json([
            'tipo_de_personal' => $tipoDePersonal
        ], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $tipoDePersonal = TiposDePersonal::find($id);
            if (!$tipoDePersonal) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
            return response()->json([
                'tipo_de_personal' => $tipoDePersonal
            ], 200);
        } else {
            $tiposDePersonal = TiposDePersonal::all();
            return response()->json([
                'tipos_de_personal' => $tiposDePersonal
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar y actualizar el tipo de personal
        $tipoDePersonal = TiposDePersonal::find($id);
        if (!$tipoDePersonal) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $tipoDePersonal->update($request->only(['nombre']));

        return response()->json([
            'mensaje' => 'Datos actualizados correctamente',
            'tipo_de_personal' => $tipoDePersonal
        ], 200);
    }

    public function delete($id)
    {
        $tipoDePersonal = TiposDePersonal::find($id);
        if (!$tipoDePersonal) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $tipoDePersonal->delete();
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }
}