<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    // Muestra la lista de diagnósticos
    public function index()
    {
        $diagnosticos = Diagnostico::all();
        return view('diagnosticos.index', compact('diagnosticos'));
    }

    // Muestra el formulario para crear un nuevo diagnóstico
    public function create()
    {
        return view('diagnosticos.create');
    }

    // Guarda un nuevo diagnóstico
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'dx' => 'required',
            'estatus' => 'required|in:sospechoso,confirmado,descartado',
        ]);

        Diagnostico::create($validatedData);

        return redirect()->route('diagnosticos.index')
                         ->with('success', 'Diagnóstico creado correctamente.');
    }

    // Muestra los detalles de un diagnóstico específico
    public function show(Diagnostico $diagnostico)
    {
        return view('diagnosticos.show', compact('diagnostico'));
    }

    // Muestra el formulario para editar un diagnóstico existente
    public function edit(Diagnostico $diagnostico)
    {
        return view('diagnosticos.edit', compact('diagnostico'));
    }

    // Actualiza un diagnóstico existente
    public function update(Request $request, Diagnostico $diagnostico)
    {
        $validatedData = $request->validate([
            'dx' => 'required',
            'estatus' => 'required|in:sospechoso,confirmado,descartado',
        ]);

        $diagnostico->update($validatedData);

        return redirect()->route('diagnosticos.index')
                         ->with('success', 'Diagnóstico actualizado correctamente.');
    }

    // Elimina un diagnóstico
    public function destroy(Diagnostico $diagnostico)
    {
        $diagnostico->delete();

        return redirect()->route('diagnosticos.index')
                         ->with('success', 'Diagnóstico eliminado correctamente.');
    }
}