<?php

namespace App\Http\Controllers;

use App\Models\TiposDeEstudios;
use Illuminate\Http\Request;

class TiposDeEstudiosController extends Controller
{
    // Lista todos los registros de tipos de estudios
    public function index()
    {
        $tiposDeEstudios = TiposDeEstudios::all();
        return view('tipos_de_estudios.index', compact('tiposDeEstudios'));
    }

    // Muestra el formulario para crear un nuevo registro
    public function create()
    {
        return view('tipos_de_estudios.create');
    }

    // Guarda un nuevo registro de tipos de estudios
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        TiposDeEstudios::create($validatedData);

        return redirect()->route('tipos_de_estudios.index')
                         ->with('success', 'Tipo de estudio creado correctamente.');
    }

    // Muestra los detalles de un registro de tipos de estudios específico
    public function show(TiposDeEstudios $tipos_de_estudio)
    {
        return view('tipos_de_estudios.show', compact('tipos_de_estudio'));
    }

    // Muestra el formulario para editar un registro de tipos de estudios
    public function edit(TiposDeEstudios $tipos_de_estudio)
    {
        return view('tipos_de_estudios.edit', compact('tipos_de_estudio'));
    }

    // Actualiza un registro de tipos de estudios existente
    public function update(Request $request, TiposDeEstudios $tipos_de_estudio)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $tipos_de_estudio->update($validatedData);

        return redirect()->route('tipos_de_estudios.index')
                         ->with('success', 'Tipo de estudio actualizado correctamente.');
    }

    // Elimina un registro de tipos de estudios
    public function destroy(TiposDeEstudios $tipos_de_estudio)
    {
        $tipos_de_estudio->delete();

        return redirect()->route('tipos_de_estudios.index')
                         ->with('success', 'Tipo de estudio eliminado correctamente.');
    }
}
