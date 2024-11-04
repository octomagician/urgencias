<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Persona;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;
use Exception;

//mascotas
class PacienteController extends Controller
{

	public function index()
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
			//read a la sig appi
				->get('http://192.168.117.230:3325/api/mascotas/index',[
			]);

			$datas = $response->json();

			$paciente = Paciente::all();
            return response()->json([
                'paciente' => $paciente,
                'mascotas' => $datas //respuesta del sig appi
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

            $response =  Http::withOptions(['verify' => false])
                ->withToken($token2)
                ->timeout(80)
                ->post('http://192.168.117.230:3325/api/mascotas', [
                    'emails' => $request->input('emails'),
                    'password' => $request->input('passwords'),
                    'nombre' => $faker->name,
                    'edad' => $faker->numberBetween(1, 10),
                    'dueno' => [
                        'nombre' => $faker->name,
                        'email' => $faker->email,
                        'telefono' => $faker->phoneNumber,
                    ],
                    'raza' => [
                        'nombre' => $faker->word,
                        'descripcion' => $faker->sentence,
                    ],
                    'vacuna' => [
                        'nombre' => $faker->word,
                        'descripcion' => $faker->sentence,
                    ]
                ]);
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();

            $validatedData = $request->validate([
                'nombre' => 'required|max:35',
                'apellido_paterno' => 'required|max:35',
                'apellido_materno' => 'required|max:35',
                'sexo' => 'required|in:M,F',

                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',

                'nacimiento' => 'required|date',
                'nss' => 'required|string|max:11|unique:pacientes',
                'direccion' => 'required|string|max:100',
                'tel_1' => 'required|string|max:20',
                'tel_2' => 'nullable|string|max:20',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
            ]);

            $paciente = Paciente::create([
                'persona_id' => $persona->id,
                'nacimiento' => $request->nacimiento,
                'nss' => $request->nss,
                'direccion' => $request->direccion,
                'tel_1' => $request->tel_1,
                'tel_2' => $request->tel_2,
            ]);

            return response()->json([
                'user' => $user,
                'persona' => $persona,
                'paciente' => $paciente,
                'mascotas' => $datas
            ], 200);
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

                $response =  Http::withOptions(['verify' => false])
                    ->withToken($token2)
                    ->timeout(80)
                    ->get('http://192.168.117.230:3325/api/mascotas/' . $id, [
                    ]);

                    if (!$response->successful()) {
                        $errorBody = $response->json();
                        $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                        throw new Exception("Error detectado en la sig API: " . $errorMessage);
                    }

                $datas = $response->json();

                $px = Paciente::with('persona')->find($id);

                if (!$px) {
                    return response()->json(['message' => 'Paciente no encontrado'], 404);
                }
            } else {
                $px = Paciente::all();
            }
            return response()->json([
                'paciente' => $px,
                'mascotas' => $datas
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

            $response =  Http::withOptions(['verify' => false])
                ->withToken($token2)
                ->timeout(80)
                ->put('http://192.168.117.230:3325/api/mascotas/' . $id, [
                    'nombre' => $faker->firstName,
                    'edad' => $faker->numberBetween(1, 10),
                ]);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();

            $request->validate([
                'nacimiento' => 'required|date',
                'nss' => 'required|string',
                'direccion' => 'required|string',
                'tel_1' => 'required|string',
                'tel_2' => 'nullable|string'
            ]);

            $px = Paciente::find($id);
            if (!$px) {
                return response()->json(['message' => 'Paciente no encontrado'], 404);
            }

            $px->update($request->only([
                'nacimiento',
                'nss',
                'direccion',
                'tel_1',
                'tel_2'
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

            $response =  Http::withOptions(['verify' => false])
                ->withToken($token2)
                ->timeout(80)
                ->delete('http://192.168.117.230:3325/api/mascotas/' . $id, [
                ]);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
        
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();

            $px = Paciente::find($id);
            $px->delete();
            return response()->json(204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el recurso', 'error' => $e->getMessage()], 500);
        }
    }
}
