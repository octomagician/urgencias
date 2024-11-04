<?php

namespace App\Http\Controllers;

use App\Models\TiposDeEstudio;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

use App\Models\Token;
use Illuminate\Support\Facades\Http;

use Exception;

//vacunas
class TiposDeEstudioController extends Controller
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
				->get('http://192.168.117.230:3325/api/vacunas/index',[
			]);
            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                throw new Exception("Error detectado en la sig API: " . $errorMessage);
            }
			$datas = $response->json();

			$tipo_de_estudio = TiposDeEstudio::all();
            return response()->json([
                'tipo_de_estudio' => $tipo_de_estudio,
                'vacunas' => $datas //respuesta del sig appi
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
                ->post('http://192.168.117.230:3325/api/vacunas',[
                    'nombre' => $faker->word,
                    'descripcion' => $faker->sentence(10),
                    'dosis' => $faker->randomNumber(2) . ' mg'
                ]);
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }
            $datas = $response->json();

            $request->validate([
                'nombre' => 'required|max:50'
            ]);

            $tipo_de_estudio = TiposDeEstudio::create([
                'nombre' => $request->nombre
            ]);

            return response()->json([
                'tipo_de_estudio' => $tipo_de_estudio,
                'vacunas' => $datas
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
                    ->get('http://192.168.117.230:3325/api/vacunas/'.$id,[
                ]);
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }
                $datas = $response->json();

                //this appi
                $tipo_de_estudio = TiposDeEstudio::find($id);
                if (!$tipo_de_estudio) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }
            } else {
                $tipo_de_estudio = TiposDeEstudio::all();
            }
        
            return response()->json([
                'tipo_de_estudio' => $tipo_de_estudio,
                'vacunas' => $datas //respuesta del sig appi
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
                ->put('http://192.168.117.230:3325/api/vacunas/'.$id,[
            
                    'nombre' => $faker->word,
                    'descripcion' => $faker->sentence(10),
                    'dosis' => $faker->randomNumber(2) . ' mg'
                ]);
            $datas = $response->json();

            //this appi
            $tipo_de_estudio = TiposDeEstudio::find($id);
            if (!$tipo_de_estudio) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $request->validate([
                'nombre' => 'required|max:50'
            ]);

            $tipo_de_estudio->update($request->only(['nombre']));
            return response()->json(['message' => 'Datos actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }  
    }

    public function delete(Request $request, $id)
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
                ->delete('http://192.168.117.230:3325/api/vacunas/'.$id,[
                ]);
                
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['message'] ?? 'Error desconocido';
                    throw new Exception("Error detectado en la sig API: " . $errorMessage);
                }

            $datas = $response->json();

            $tipo_de_estudio = TiposDeEstudio::find($id);
            if (!$tipo_de_estudio) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $tipo_de_estudio->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error con el recurso', 'error' => $e->getMessage()], 500);
        }
    }
}
