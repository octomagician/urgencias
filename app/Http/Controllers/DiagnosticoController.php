<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

//veterinarios
class DiagnosticoController extends Controller
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
				->get('http://192.168.120.231:3325/api/veterinarios/index',[
			]);

			$datas = $response->json();

			$diagnostico = Diagnostico::all();
            return response()->json([
                'diagnostico' => $diagnostico,
                'veterinarios' => $datas //respuesta del sig appi
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
                //crear en la tabla de la sig api
                ->post('http://192.168.120.231:3325/api/veterinarios',[

                        'nombre' => $faker->name,
                        'especializacion' => $faker->word, 
                ]);
            $datas = $response->json();

            $request->validate([
                'dx' => 'required|string',
                'estatus' => 'required|in:sospechoso,confirmado,descartado'
            ]);

            $diagnostico = Diagnostico::create([
                'dx' => $request->dx,
                'estatus' => $request->estatus
            ]);

            return response()->json([
                'diagnostico' => $diagnostico,
                'veterinarios' => $datas
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
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
                    ->get('http://192.168.120.231:3325/api/veterinarios/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);

                $datas = $response->json();

            //this appi

                $diagnostico = Diagnostico::find($id);
                if (!$diagnostico) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }
            } else {
                $diagnostico = Diagnostico::all();
            }

            return response()->json([
                'diagnostico' => $diagnostico,
                'veterinarios' => $datas //respuesta del sig appi
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
                ->put('http://192.168.120.231:3325/api/veterinarios/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'nombre' => $faker->name,
                    'especializacion' => $faker->word, 
                ]);
            $datas = $response->json();

            //this appi
            $diagnostico = Diagnostico::find($id);
            if (!$diagnostico) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $request->validate([
                'dx' => 'required|string',
                'estatus' => 'required|in:sospechoso,confirmado,descartado'
            ]);

            $diagnostico->update($request->only(['dx', 'estatus']));
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
                ->delete('http://192.168.120.231:3325/api/veterinarios/'.$id,[
                ]);
            $datas = $response->json();

            $diagnostico = Diagnostico::find($id);
            if (!$diagnostico) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $diagnostico->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }
}