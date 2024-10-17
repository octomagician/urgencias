<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\Ingreso;
use App\Models\Personal;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    // Lista todos los registros de historial
    public function index()
    {
        $historiales = Historial::with(['ingreso', 'personal'])->get();
        return view('historial.index', compact('historiales'));
    }

    // Muestra el formulario para crear un nuevo registro de historial
    public function create()
    {
        $ingresos = Ingreso::all();
        $personal = Personal::all();
        
        return view('historial.create', compact('ingresos', 'personal'));
    }

    // Guarda un nuevo registro de historial
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ingreso_id' => 'required|exists:ingresos,id',
            'personal_id' => 'required|exists:personal,id',
            'presion' => 'required|string|max:10',
            'temperatura' => 'required|numeric|between:30,45',
            'glucosa' => 'required|numeric|between:30,999.99',
            'sintomatologia' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        Historial::create($validatedData);

        return redirect()->route('historial.index')
                         ->with('success', 'Historial creado correctamente.');
    }

    // Muestra los detalles de un registro de historial
    public function show(Historial $historial)
    {
        return view('historial.show', compact('historial'));
    }

    // Muestra el formulario para editar un registro de historial
    public function edit(Historial $historial)
    {
        $ingresos = Ingreso::all();
        $personal = Personal::all();
        
        return view('historial.edit', compact('historial', 'ingresos', 'personal'));
    }

    // Actualiza un registro de historial
    public function update(Request $request, Historial $historial)
    {
        $validatedData = $request->validate([
            'ingreso_id' => 'required|exists:ingresos,id',
            'personal_id' => 'required|exists:personal,id',
            'presion' => 'required|string|max:10',
            'temperatura' => 'required|numeric|between:30,45',
            'glucosa' => 'required|numeric|between:30,999.99',
            'sintomatologia' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        $historial->update($validatedData);

        return redirect()->route('historial.index')
                         ->with('success', 'Historial actualizado correctamente.');
    }

    // Elimina un registro de historial
    public function destroy(Historial $historial)
    {
        $historial->delete();

        return redirect()->route('historial.index')
                         ->with('success', 'Historial eliminado correctamente.');
    }
}