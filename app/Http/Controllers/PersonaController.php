<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Models\Personal;
use App\Models\Paciente;
use App\Models\TiposDePersonal;

class PersonaController extends Controller
{

	public function index(Request $request)
    {
        try { 
			$authHeader = $request->header('Authorization');
			if (!$authHeader) {
				return response()->json(['message' => 'Authorization header not found'], 401);
			}
			$token = str_replace('Bearer ', '', $authHeader);
			$tokenRecord = Token::where('token1', $token)->first();
			if (!$tokenRecord) {
				return response()->json(['message' => 'Token not found'], 404);
			}
			$token2 = $tokenRecord->token2;

			$response = Http::withToken($token2)
				->timeout(80)
			//read a la sig appi
				->get('http://192.168.117.230:3325/api/visitas',[
			]);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                throw new Exception("Error detectado en la sig API: " . $errorMessage);
            }

			$datas = $response->json();

			$persona = Persona::all();
            return response()->json([
                'persona' => $persona,
                'visitas' => $datas //respuesta del sig appi
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } 
    }

    public function create(Request $request)
    {
        try { 
            $faker= Faker::create();
            $authHeader = $request->header('Authorization');
            if (!$authHeader) {
                return response()->json(['message' => 'Authorization header not found'], 401);
            }
            $token = str_replace('Bearer ', '', $authHeader);
            $tokenRecord = Token::where('token1', $token)->first();
            if (!$tokenRecord) {
                return response()->json(['message' => 'Token not found'], 404);
            }
            $token2 = $tokenRecord->token2;
    
            $response = Http::withToken($token2)
                ->timeout(80)
                //crear en la tabla de la sig api
                ->post('http://192.168.117.230:3325/api/visitas',[                    
                    'fecha' => $faker->date(),
                    'motivo' => $faker->sentence(6), 
                    'mascota_id' => $faker->randomNumber(),   
                ]);
                
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();

            $persona = $request->validate([
                'nombre' => 'required|max:35',
                'apellido_paterno' => 'required|max:35',
                'apellido_materno' => 'required|max:35',
                'sexo' => 'required|in:M,F',
            ]);

            Persona::create($persona);

            return response()->json([
                'persona' => $persona,
                'visitas' => $datas
            ], 201);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error con el recurso', 'error' => $e->getMessage()], 500);
            }
    }

    public function read($id = null, Request $request)
    {
        try {
            if ($id) {
                $faker= Faker::create();
                $authHeader = $request->header('Authorization');
                if (!$authHeader) {
                    return response()->json(['message' => 'Authorization header not found'], 401);
                }
                $token = str_replace('Bearer ', '', $authHeader);
                $tokenRecord = Token::where('token1', $token)->first();
                if (!$tokenRecord) {
                    return response()->json(['message' => 'Token not found'], 404);
                }
                $token2 = $tokenRecord->token2;
        
                $response = Http::withToken($token2)
                    ->timeout(80)
                //read a la sig appi
                    ->get('http://192.168.117.230:3325/api/visitas/'.$id,[
                ]);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }
        
                $datas = $response->json();
        
                //this appi


                    $persona = Persona::find($id);
                
                if (!$persona) {
                    return response()->json(['message' => 'Persona no encontrado'], 404);
                }
            } else {
                $persona = Persona::all();
            }
            return response()->json([
                'persona' => $persona,
                'visitas' => $datas //respuesta del sig appi
            ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }  
    }

    public function update(Request $request, $id)
    {
        try { 
            $faker= Faker::create();
            $authHeader = $request->header('Authorization');
            if (!$authHeader) {
                return response()->json(['message' => 'Authorization header not found'], 401);
            }
            $token = str_replace('Bearer ', '', $authHeader);
            $tokenRecord = Token::where('token1', $token)->first();
            if (!$tokenRecord) {
                return response()->json(['message' => 'Token not found'], 404);
            }
            $token2 = $tokenRecord->token2;

            //sig appi petición
            $response = Http::withToken($token2)
                ->timeout(80)
                ->put('http://192.168.117.230:3325/api/visitas/'.$id,[
                    'fecha' => $faker->date(),
                    'motivo' => $faker->sentence(6), 
                    'mascota_id' => $faker->randomNumber(), 
                ]);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();
        
            //this appi

        
            $persona = Persona::find($id);
            if (!$persona) {
                return response()->json(['message' => 'Persona no encontrado'], 404);
            }
        
            $persona->update($request->only([
                'nombre', 'apellido_paterno', 
                'apellido_materno', 'sexo'
            ]));
        
            return response()->json(['message' => 'Datos actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }  
    }

    public function delete($id, Request $request)
    {
        try {
            $faker= Faker::create();
            $authHeader = $request->header('Authorization');
            if (!$authHeader) {
                return response()->json(['message' => 'Authorization header not found'], 401);
            }
            $token = str_replace('Bearer ', '', $authHeader);
            $tokenRecord = Token::where('token1', $token)->first();
            if (!$tokenRecord) {
                return response()->json(['message' => 'Token not found'], 404);
            }
            $token2 = $tokenRecord->token2;
    
            $response = Http::withToken($token2)
                ->timeout(80)
                ->delete('http://192.168.117.230:3325/api/visitas/'.$id,[
                ]);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();
    
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrado'], 404);
        }
        $persona->delete();
        return response()->json(['message' => 'Persona eliminado'], 204);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error con el recurso', 'error' => $e->getMessage()], 500);
    }
    }

    public function perfil(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Obtener la persona asociada al usuario
        $persona = Persona::where('users_id', $user->id)->first();

        // Obtener el personal/paciente asociado a la persona
        $personal_o_paciente = Personal::where('persona_id', $persona->id)->first();
        if (!$personal_o_paciente) { //si no es personal, entonces es paciente
            $personal_o_paciente = Paciente::where('persona_id', $persona->id)->first();
            $paciente = true; //obtengo el dato y además guardo que es paciente
            $personal = false;
        }
        else{
            $personal = true;
            $paciente = false;
        }

        if ($personal) { //SI ES TRABAJADOR
            $tipo = TiposDePersonal::find($personal_o_paciente->tipo_id);

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'persona' => [
                    'nombre' => $persona->nombre,
                    'apellido_paterno' => $persona->apellido_paterno,
                    'apellido_materno' => $persona->apellido_materno,
                    'sexo' => $persona->sexo,
                ],
                'personal' => [
                    'tipo_id' => $personal_o_paciente->tipo_id,
                    'tipo' => $tipo ? $tipo->nombre : null,
                ],
            ], 200);
        }
        else { //SI ES PACIENTE

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'persona' => [
                    'nombre' => $persona->nombre,
                    'apellido_paterno' => $persona->apellido_paterno,
                    'apellido_materno' => $persona->apellido_materno,
                    'sexo' => $persona->sexo,
                ],
                'paciente' => [
                    'nacimiento' => $personal_o_paciente->nacimiento,
                    'nss' => $personal_o_paciente->nss,
                    'direccion' => $personal_o_paciente->direccion,
                    'tel_1' => $personal_o_paciente->tel_1,
                    'tel_2' => $personal_o_paciente->tel_2,
                ],
            ], 200);
        }
    }

public function actualizarPerfil(Request $request)
{
    // Obtener el usuario autenticado
    $user = $request->user();

    // Validar los datos de entrada
    $request->validate([
        'name' => 'sometimes|string|max:255', // Campo opcional
        'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id, // Campo opcional y único excepto para el usuario actual
        'password' => 'sometimes|string|min:8', // Campo opcional

        'nombre' => 'sometimes|string|max:35', // Campo opcional
        'apellido_paterno' => 'sometimes|string|max:35', // Campo opcional
        'apellido_materno' => 'sometimes|string|max:35', // Campo opcional
        'sexo' => 'sometimes|in:M,F', // Campo opcional

        'tipo_id' => 'sometimes|exists:tipos_de_personal,id', // Campo opcional
    ]);

    // Iniciar una transacción de base de datos para asegurar la consistencia
    DB::beginTransaction();

    try {
        // Actualizar los datos del usuario
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Obtener la persona asociada al usuario
        $persona = Persona::where('users_id', $user->id)->first();

        if ($persona) {
            // Actualizar los datos de la persona
            if ($request->has('nombre')) {
                $persona->nombre = $request->nombre;
            }
            if ($request->has('apellido_paterno')) {
                $persona->apellido_paterno = $request->apellido_paterno;
            }
            if ($request->has('apellido_materno')) {
                $persona->apellido_materno = $request->apellido_materno;
            }
            if ($request->has('sexo')) {
                $persona->sexo = $request->sexo;
            }
            $persona->save();

            // Obtener el personal asociado a la persona
            $personal = Personal::where('persona_id', $persona->id)->first();

            if ($personal && $request->has('tipo_id')) {
                // Actualizar el tipo de personal
                $personal->tipo_id = $request->tipo_id;
                $personal->save();
            }
        }

        // Confirmar la transacción
        DB::commit();

        // Devolver una respuesta exitosa
        return response()->json([
            'mensaje' => 'Perfil actualizado correctamente',
            'user' => $user,
            'persona' => $persona,
            'personal' => $personal ?? null,
        ], 200);

    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();

        // Devolver un mensaje de error
        return response()->json([
            'mensaje' => 'Error al actualizar el perfil',
            'error' => $e->getmensaje(),
        ], 500);
    }
}




}
