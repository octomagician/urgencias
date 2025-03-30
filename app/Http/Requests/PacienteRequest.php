<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia a false si necesitas lógica de autorización
    }

    public function rules()
    {
        \Log::info('Validando paciente', [
            'route_id' => $this->route('id'),
            'route_paciente' => $this->route('paciente'),
            'request_paciente' => $this->paciente ? $this->paciente->id : null,
            'all_input' => $this->all()
        ]);
        
        // Obtener el ID del paciente de la ruta
        // Usamos $this->paciente si está disponible en el request, o lo obtenemos de la ruta
        $pacienteId = $this->paciente ? $this->paciente->id : 
                     ($this->route('id') ?? $this->route('paciente') ?? null);
    
        return [
            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',
            'nacimiento' => 'required|date',
            'nss' => [
                'required',
                'string',
                'max:11',
                Rule::unique('pacientes', 'nss')->ignore($pacienteId)
            ],
            'direccion' => 'required|string|max:100',
            'tel_1' => 'required|string|max:20',
            'tel_2' => 'nullable|string|max:20',
        ];
    }
}