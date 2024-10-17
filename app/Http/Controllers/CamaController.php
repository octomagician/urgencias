<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Area;
use Illuminate\Http\Request;

class CamaController extends Controller
{
    // Lista todas las camas
    public function index()
    {
        $camas = Cama::with('area')->get();
        return view('camas.index', compact('camas'));
    }

    // Muestra el formulario para crear una nueva cama
    public function create()
    {
        $areas = Area::all(); // Para seleccionar un área al crear la cama
        return view('camas.create', compact('areas'));
    }

    // Guarda una nueva cama
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'numero_cama' => 'required|integer',
            'area_id' => 'required|exists:areas,id',
        ]);

        Cama::create($validatedData);

        return redirect()->route('camas.index')
                         ->with('success', 'Cama creada correctamente.');
    }

    // Muestra los detalles de una cama específica
    public function show(Cama $cama)
    {
        return view('camas.show', compact('cama'));
    }

    // Muestra el formulario para editar una cama
    public function edit(Cama $cama)
    {
        $areas = Area::all();
        return view('camas.edit', compact('cama', 'areas'));
    }

    // Actualiza una cama existente
    public function update(Request $request, Cama $cama)
    {
        $validatedData = $request->validate([
            'numero_cama' => 'required|integer',
            'area_id' => 'required|exists:areas,id',
        ]);

        $cama->update($validatedData);

        return redirect()->route('camas.index')
                         ->with('success', 'Cama actualizada correctamente.');
    }

    // Elimina una cama
    public function destroy(Cama $cama)
    {
        $cama->delete();

        return redirect()->route('camas.index')
                         ->with('success', 'Cama eliminada correctamente.');
    }
}
