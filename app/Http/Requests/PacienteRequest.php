<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PacienteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia a false si necesitas lógica de autorización
    }

    public function rules()
    {
        // Obtener el ID del paciente si está presente (para actualización)
        $pacienteId = $this->route('id') ?? null;

        return [
            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',
            'nacimiento' => 'required|date',
            'nss' => 'required|string|max:11|unique:pacientes,nss,' . $pacienteId,
            'direccion' => 'required|string|max:100',
            'tel_1' => 'required|string|max:20',
            'tel_2' => 'nullable|string|max:20',
        ];
    }
}