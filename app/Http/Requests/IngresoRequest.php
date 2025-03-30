<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IngresoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pacientes_id' => 'required|exists:pacientes,id',
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'camas_id' => 'required|exists:camas,id',
            'user_id' => 'required|exists:users,id',
            'fecha_ingreso' => 'required|date',
            'motivo_ingreso' => 'required|string',
            'fecha_alta' => 'nullable|date'
        ];
    }

    public function messages()
    {
        return [
            'pacientes_id.required' => 'El paciente es obligatorio',
            'pacientes_id.exists' => 'El paciente seleccionado no existe',
            'diagnostico_id.required' => 'El diagnóstico es obligatorio',
            'diagnostico_id.exists' => 'El diagnóstico seleccionado no existe',
            'camas_id.required' => 'La cama es obligatoria',
            'camas_id.exists' => 'La cama seleccionada no existe',
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida',
            'motivo_ingreso.required' => 'El motivo de ingreso es obligatorio',
            'fecha_alta.date' => 'La fecha de alta debe ser una fecha válida'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'mensaje' => 'Error de validación',
            'errores' => $validator->errors()
        ], 422));
    }
}