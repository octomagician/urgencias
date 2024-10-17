<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    // Muestra la lista de áreas
    public function index()
    {
        $areas = Area::all();
        return view('areas.index', compact('areas'));
    }

    // Muestra el formulario para crear una nueva área
    public function create()
    {
        return view('areas.create');
    }

    // Guarda una nueva área
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:50',
        ]);

        Area::create($validatedData);

        return redirect()->route('areas.index')
                         ->with('success', 'Área creada correctamente.');
    }

    // Muestra los detalles de una área específica
    public function show(Area $area)
    {
        return view('areas.show', compact('area'));
    }

    // Muestra el formulario para editar un área existente
    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    // Actualiza un área existente
    public function update(Request $request, Area $area)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:50',
        ]);

        $area->update($validatedData);

        return redirect()->route('areas.index')
                         ->with('success', 'Área actualizada correctamente.');
    }

    // Elimina un área
    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('areas.index')
                         ->with('success', 'Área eliminada correctamente.');
    }
}
