<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    // Obtener todos los logs
    public function index()
    {
        $logs = Log::all();
        return response()->json($logs);
    }

    // Crear un nuevo log
    public function store(Request $request)
    {
        $log = Log::create($request->all());
        return response()->json($log, 201);
    }

    // Obtener un log por ID
    public function show($id)
    {
        $log = Log::find($id);
        return response()->json($log);
    }

    // Actualizar un log
    public function update(Request $request, $id)
    {
        $log = Log::findOrFail($id);
        $log->update($request->all());
        return response()->json($log, 200);
    }

    // Eliminar un log
    public function destroy($id)
    {
        Log::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}