<?php

namespace App\Http\Controllers;

use App\Models\TipoDePersonal;
use Illuminate\Http\Request;

class TipoDePersonalController extends Controller
{
    // Muestra la lista de tipos de personal
    public function index()
    {
        $tipos = TipoDePersonal::all();
        return view('tipos_de_personal.index', compact('tipos'));
    }

    // Muestra el formulario para crear un nuevo tipo de personal
    public function create()
    {
        return view('tipos_de_personal.create');
    }

    // Guarda un nuevo tipo de personal
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:50',
        ]);

        TipoDePersonal::create($validatedData);

        return redirect()->route('tipos-de-personal.index')
                         ->with('success', 'Tipo de personal creado correctamente.');
    }

    // Muestra los detalles de un tipo de personal
    public function show(TipoDePersonal $tipoDePersonal)
    {
        return view('tipos_de_personal.show', compact('tipoDePersonal'));
    }

    // Muestra el formulario para editar un tipo de personal existente
    public function edit(TipoDePersonal $tipoDePersonal)
    {
        return view('tipos_de_personal.edit', compact('tipoDePersonal'));
    }

    // Actualiza un tipo de personal existente
    public function update(Request $request, TipoDePersonal $tipoDePersonal)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:50',
        ]);

        $tipoDePersonal->update($validatedData);

        return redirect()->route('tipos-de-personal.index')
                         ->with('success', 'Tipo de personal actualizado correctamente.');
    }

    // Elimina un tipo de personal
    public function destroy(TipoDePersonal $tipoDePersonal)
    {
        $tipoDePersonal->delete();

        return redirect()->route('tipos-de-personal.index')
                         ->with('success', 'Tipo de personal eliminado correctamente.');
    }
}
