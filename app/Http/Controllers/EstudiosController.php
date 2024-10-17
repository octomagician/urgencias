<?php

namespace App\Http\Controllers;

use App\Models\Estudios;
use App\Models\TiposDeEstudios;
use App\Models\Personal;
use Illuminate\Http\Request;

class EstudiosController extends Controller
{
    // Lista todos los registros de estudios
    public function index()
    {
        $estudios = Estudios::with(['tipos_de_estudios', 'personal'])->get();
        return view('estudios.index', compact('estudios'));
    }

    // Muestra el formulario para crear un nuevo registro
    public function create()
    {
        $tiposDeEstudios = TiposDeEstudios::all();
        $personal = Personal::all();
        return view('estudios.create', compact('tiposDeEstudios', 'personal'));
    }

    // Guarda un nuevo registro de estudios
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipos_de_estudios_id' => 'required|exists:tipos_de_estudios,id',
            'personal_id' => 'required|exists:personal,id',
        ]);

        Estudios::create($validatedData);

        return redirect()->route('estudios.index')
                         ->with('success', 'Estudio creado correctamente.');
    }

    // Muestra los detalles de un registro de estudios específico
    public function show(Estudios $estudio)
    {
        return view('estudios.show', compact('estudio'));
    }

    // Muestra el formulario para editar un registro de estudios
    public function edit(Estudios $estudio)
    {
        $tiposDeEstudios = TiposDeEstudios::all();
        $personal = Personal::all();
        return view('estudios.edit', compact('estudio', 'tiposDeEstudios', 'personal'));
    }

    // Actualiza un registro de estudios existente
    public function update(Request $request, Estudios $estudio)
    {
        $validatedData = $request->validate([
            'tipos_de_estudios_id' => 'required|exists:tipos_de_estudios,id',
            'personal_id' => 'required|exists:personal,id',
        ]);

        $estudio->update($validatedData);

        return redirect()->route('estudios.index')
                         ->with('success', 'Estudio actualizado correctamente.');
    }

    // Elimina un registro de estudios
    public function destroy(Estudios $estudio)
    {
        $estudio->delete();

        return redirect()->route('estudios.index')
                         ->with('success', 'Estudio eliminado correctamente.');
    }
}
