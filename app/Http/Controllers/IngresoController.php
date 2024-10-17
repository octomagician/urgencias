<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Paciente;
use App\Models\Diagnostico;
use App\Models\Cama;
use App\Models\Personal;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    // Lista todos los ingresos
    public function index()
    {
        $ingresos = Ingreso::with(['paciente', 'diagnostico', 'cama', 'personal'])->get();
        return view('ingresos.index', compact('ingresos'));
    }

    // Muestra el formulario para crear un nuevo ingreso
    public function create()
    {
        $pacientes = Paciente::all();
        $diagnosticos = Diagnostico::all();
        $camas = Cama::all();
        $personal = Personal::all();
        
        return view('ingresos.create', compact('pacientes', 'diagnosticos', 'camas', 'personal'));
    }

    // Guarda un nuevo ingreso
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pacientes_id' => 'required|exists:pacientes,id',
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'camas_id' => 'required|exists:camas,id',
            'personal_id' => 'required|exists:personal,id',
            'fecha_ingreso' => 'required|date',
            'motivo_ingreso' => 'required|string',
            'fecha_alta' => 'nullable|date',
        ]);

        Ingreso::create($validatedData);

        return redirect()->route('ingresos.index')
                         ->with('success', 'Ingreso creado correctamente.');
    }

    // Muestra los detalles de un ingreso específico
    public function show(Ingreso $ingreso)
    {
        return view('ingresos.show', compact('ingreso'));
    }

    // Muestra el formulario para editar un ingreso
    public function edit(Ingreso $ingreso)
    {
        $pacientes = Paciente::all();
        $diagnosticos = Diagnostico::all();
        $camas = Cama::all();
        $personal = Personal::all();
        
        return view('ingresos.edit', compact('ingreso', 'pacientes', 'diagnosticos', 'camas', 'personal'));
    }

    // Actualiza un ingreso existente
    public function update(Request $request, Ingreso $ingreso)
    {
        $validatedData = $request->validate([
            'pacientes_id' => 'required|exists:pacientes,id',
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'camas_id' => 'required|exists:camas,id',
            'personal_id' => 'required|exists:personal,id',
            'fecha_ingreso' => 'required|date',
            'motivo_ingreso' => 'required|string',
            'fecha_alta' => 'nullable|date',
        ]);

        $ingreso->update($validatedData);

        return redirect()->route('ingresos.index')
                         ->with('success', 'Ingreso actualizado correctamente.');
    }

    // Elimina un ingreso
    public function destroy(Ingreso $ingreso)
    {
        $ingreso->delete();

        return redirect()->route('ingresos.index')
                         ->with('success', 'Ingreso eliminado correctamente.');
    }
}
