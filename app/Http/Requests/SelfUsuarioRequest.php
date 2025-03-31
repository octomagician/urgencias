<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SelfUsuarioRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize()
    {
        return true; // Cambia a true para permitir la validación
    }

    /**
     * Reglas de validación.
     */
    public function rules()
{
    // Obtener el ID del usuario autenticado (ya sea del request o del usuario actual)
    $userId = $this->user_id ?? auth()->id();

    return [
        'username' => [
            'required',
            'string',
            'max:255',
            Rule::unique('users', 'username')->ignore($userId)
        ],
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($userId)
        ],
        'password' => 'nullable|string|min:8', // Cambiado a nullable para actualizaciones

        'nombre' => 'required|max:35',
        'apellido_paterno' => 'required|max:35',
        'apellido_materno' => 'required|max:35',
        'sexo' => 'required|in:M,F',

        'tipo_id' => 'required|exists:tipos_de_personal,id',
    ];
}

/**
 * Mensajes personalizados para las reglas de validación.
 */
public function messages()
{
    return [
        'username.required' => 'El nombre de usuario es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.unique' => 'El correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

        'nombre.required' => 'El nombre es obligatorio.',
        'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
        'apellido_materno.required' => 'El apellido materno es obligatorio.',
        'sexo.required' => 'El sexo es obligatorio.',
        'sexo.in' => 'El sexo debe ser "M" o "F".',

        'tipo_id.required' => 'El tipo de personal es obligatorio.',
        'tipo_id.exists' => 'El tipo de personal seleccionado no es válido.',
    ];
}

/**
 * Nombres personalizados para los atributos.
 */
public function attributes()
{
    return [
        'username' => 'nombre de usuario',
        'email' => 'correo electrónico',
        'password' => 'contraseña',

        'nombre' => 'nombre',
        'apellido_paterno' => 'apellido paterno',
        'apellido_materno' => 'apellido materno',
        'sexo' => 'sexo',

        'tipo_id' => 'tipo de personal',
    ];
}

// Personalizar la respuesta de error para que me devuelva un JSON, algo parecido a if ($validator->fails() cuando uso validator
protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(response()->json([
        'mensaje' => 'Error de validación',
        'errores' => $validator->errors(),
    ], 422));
}
}