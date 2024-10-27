<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        /* $expCorreo = '/^([a-zA-Z0-9]+([._-]?[a-zA-Z0-9]+)*)@([a-zA-Z0-9]+(\.[a-zA-Z]+)+)$/'; 
        lol laravel ya tenía manera de autenticar correos hahahahaha :C*/

        return [
            'name' => 'required|string|max:255',
            'email' => 'required | string | email|unique:users,email' /* 'regex:' . $expCorreo */,
            'password' => 'required|string|min:8'
        ];
    }
}
