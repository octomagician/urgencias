<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HistorialRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia a false si necesitas lógica de autorización
    }

    public function rules()
    {
        return [
            'ingreso_id' => 'required|exists:ingresos,id',
            'user_id' => 'required|exists:users,id',
            'presion' => 'required|string|max:10',
            'temperatura' => 'required|numeric|between:0,100.00',
            'glucosa' => 'required|numeric|between:0,999.99',
            'sintomatologia' => 'required|string',
            'observaciones' => 'nullable|string'
        ];
    }
}