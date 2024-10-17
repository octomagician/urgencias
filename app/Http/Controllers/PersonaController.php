<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    // Muestra la lista de personas
    public function index()
    {
        $personas = Persona::all();
        return view('personas.index', compact('personas'));
    }

    // Muestra el formulario para crear una nueva persona
    public function create()
    {
        return view('personas.create');
    }

    // Guarda una nueva persona
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',
        ]);

        Persona::create($validatedData);

        return redirect()->route('personas.index')
                         ->with('success', 'Persona creada correctamente.');
    }

    // Muestra los detalles de una persona
    public function show(Persona $persona)
    {
        return view('personas.show', compact('persona'));
    }

    // Muestra el formulario para editar una persona existente
    public function edit(Persona $persona)
    {
        return view('personas.edit', compact('persona'));
    }

    // Actualiza una persona existente
    public function update(Request $request, Persona $persona)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',
        ]);

        $persona->update($validatedData);

        return redirect()->route('personas.index')
                         ->with('success', 'Persona actualizada correctamente.');
    }

    // Elimina una persona
    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('personas.index')
                         ->with('success', 'Persona eliminada correctamente.');
    }
}
