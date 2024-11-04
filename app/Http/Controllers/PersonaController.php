<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;
use Exception;


//visitas
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
}
