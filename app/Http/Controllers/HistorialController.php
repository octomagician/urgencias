<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Http\Requests\HistorialRequest;
use Illuminate\Http\Request;
use App\Events\HistorialRefresh;
use Illuminate\Support\Facades\Log;

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
        event(new HistorialRefresh('created', $historial)); // Emitir evento de creación
        Log::info("Evento de historial disparado: created", ['id' => $historial->id]);
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
        event(new HistorialRefresh('updated', $historial)); // Emitir evento de actualización
        Log::info("Evento de historial disparado: updated", ['id' => $historial->id]);
        return response()->json(['mensaje' => 'Datos actualizados correctamente'], 200);
    }

    public function delete($id)
    {
        $historial = Historial::find($id);
        
        if (!$historial) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }
    
        // Crear copia antes de eliminar
        $deletedHistorial = $historial->toArray();
        
        // Disparar evento ANTES de eliminar
        event(new HistorialRefresh('deleted', $historial)); // Envía el modelo completo
        Log::info("Evento de historial disparado: deleted", ['id' => $historial->id]);
    
        // Eliminar el registro
        $historial->delete();
    
        return response()->json(['mensaje' => 'Eliminado'], 204);
    }


}