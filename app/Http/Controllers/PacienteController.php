<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Persona;
use App\Http\Requests\PacienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with('persona')->get();
        
        return response()->json([
            'pacientes' => $pacientes->map(function ($paciente) {
                return [
                    'paciente' => $paciente,
                ];
            })
        ], 200);
    }

    public function create(PacienteRequest $request)
    {
        // Crear la persona
        $persona = Persona::create($request->only([
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'sexo'
        ]));

        // Crear el paciente
        $paciente = Paciente::create([
            'persona_id' => $persona->id,
            'nacimiento' => $request->nacimiento,
            'nss' => $request->nss,
            'direccion' => $request->direccion,
            'tel_1' => $request->tel_1,
            'tel_2' => $request->tel_2,
        ]);

        return response()->json([
            'persona' => $persona,
            'paciente' => $paciente,
        ], 201);
    }

    public function read($id = null)
    {
        if ($id) {
            $paciente = Paciente::with('persona')->find($id);
            if (!$paciente) {
                return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
            }
            return response()->json([
                'paciente' => $paciente
            ], 200);
        } else {
            $pacientes = Paciente::all();
            return response()->json([
                'pacientes' => $pacientes
            ], 200);
        }
    }

    public function update(Request $request, $id)
{
    // Buscar el paciente con su persona asociada
    $paciente = Paciente::with('persona')->find($id);
    
    if (!$paciente) {
        return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
    }

    // Reglas de validación
    $rules = [
        'nombre' => 'required|max:35',
        'apellido_paterno' => 'required|max:35',
        'apellido_materno' => 'required|max:35',
        'sexo' => 'required|in:M,F',
        'nacimiento' => 'required|date',
        'nss' => [
            'required',
            'string',
            'max:11',
            Rule::unique('pacientes', 'nss')->ignore($paciente->id)
        ],
        'direccion' => 'required|string|max:100',
        'tel_1' => 'required|string|max:20',
        'tel_2' => 'nullable|string|max:20',
    ];

    $messages = [
        // Mensajes generales
        'required' => 'El campo :attribute es obligatorio',
        'string' => 'El campo :attribute debe ser texto',
        'max' => 'El campo :attribute no debe exceder :max caracteres',
        'date' => 'El campo :attribute debe ser una fecha válida',
        'in' => 'El campo :attribute contiene un valor no válido',
        
        // Mensajes específicos por campo
        'nombre.required' => 'El nombre del paciente es obligatorio',
        'nombre.max' => 'El nombre no debe exceder los 35 caracteres',
        
        'apellido_paterno.required' => 'El apellido paterno es obligatorio',
        'apellido_paterno.max' => 'El apellido paterno no debe exceder los 35 caracteres',
        
        'apellido_materno.required' => 'El apellido materno es obligatorio',
        'apellido_materno.max' => 'El apellido materno no debe exceder los 35 caracteres',
        
        'sexo.required' => 'Debe especificar el sexo del paciente',
        'sexo.in' => 'El sexo debe ser "M" (masculino) o "F" (femenino)',
        
        'nacimiento.required' => 'La fecha de nacimiento es obligatoria',
        'nacimiento.date' => 'La fecha de nacimiento debe ser válida (formato: AAAA-MM-DD)',
        
        'nss.required' => 'El número de seguro social (NSS) es obligatorio',
        'nss.string' => 'El NSS debe ser una cadena de texto',
        'nss.max' => 'El NSS no debe exceder los 11 caracteres',
        'nss.unique' => 'Este número de seguro social ya está registrado para otro paciente',
        
        'direccion.required' => 'La dirección es obligatoria',
        'direccion.max' => 'La dirección no debe exceder los 100 caracteres',
        
        'tel_1.required' => 'El teléfono principal es obligatorio',
        'tel_1.max' => 'El teléfono principal no debe exceder los 20 caracteres',
        
        'tel_2.max' => 'El teléfono secundario no debe exceder los 20 caracteres',
        
        // Mensajes para campos que podrían ser agregados después
        'email.email' => 'Debe proporcionar un correo electrónico válido',
        'email.max' => 'El correo electrónico no debe exceder los 255 caracteres',
    ];

    // Validar los datos
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'mensaje' => 'Error de validación',
            'errores' => $validator->errors()
        ], 422);
    }

    // Actualizar los datos de la persona
    $paciente->persona->update($request->only([
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'sexo'
    ]));

    // Actualizar los datos del paciente
    $paciente->update($request->only([
        'nacimiento',
        'nss',
        'direccion',
        'tel_1',
        'tel_2'
    ]));

    // Recargar los modelos para obtener los datos actualizados
    $paciente->refresh();
    $paciente->persona->refresh();

    return response()->json([
        'mensaje' => 'Datos actualizados correctamente',
        'paciente' => $paciente,
        'persona' => $paciente->persona
    ], 200);
}

    public function delete($id)
    {
        $paciente = Paciente::find($id);
        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        $paciente->delete();
        return response()->json(null, 204);
    }
}