<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\Ingreso;
use App\Models\Personal;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;


//historial medicos
class HistorialController extends Controller
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
				->get('http://192.168.117.230:3325/api/historiales/index',[
			]);

			$datas = $response->json();

			$historial = Historial::all();
            return response()->json([
                'historial' => $historial,
                'historiales' => $datas //respuesta del sig appi
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
                ->post('http://192.168.117.230:3325/api/historiales',[
                        'email' => $request->input('email'),
                        'password' => $request->input('password'),

                        'fecha_cita' => $faker->dateTimeBetween('now', '+1 year'),
                        'diagnostico' => $faker->sentence(6),
                        'tratamiento' => $faker->sentence(8),
                ]);
            $datas = $response->json();

            $request->validate([
                'ingreso_id' => 'required|exists:ingresos,id',
                'personal_id' => 'required|exists:personal,id',
                'presion' => 'required|string|max:10',
                'temperatura' => 'required|numeric|between:0,100.00',
                'glucosa' => 'required|numeric|between:0,999.99',
                'sintomatologia' => 'required|string',
                'observaciones' => 'nullable|string'
            ]);

            $historial = Historial::create([
                'ingreso_id' => $request->ingreso_id,
                'personal_id' => $request->personal_id,
                'presion' => $request->presion,
                'temperatura' => $request->temperatura,
                'glucosa' => $request->glucosa,
                'sintomatologia' => $request->sintomatologia,
                'observaciones' => $request->observaciones
            ]);

            return response()->json($historial, 201);
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
                    ->get('http://192.168.117.230:3325/api/historiales/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
        
                $datas = $response->json();
        
                //this appi 
                $historial = Historial::find($id);
                if (!$historial) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }
            } else {
                $historial = Historial::all();
            }

            return response()->json([
                'historial' => $historial,
                'historiales' => $datas //respuesta del sig appi
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
                ->put('http://192.168.117.230:3325/api/historiales/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'fecha_cita' => $faker->dateTimeBetween('now', '+1 year'),
                    'diagnostico' => $faker->sentence(6),
                    'tratamiento' => $faker->sentence(8),
                ]);
            $datas = $response->json();

            //this appi
            $historial = Historial::find($id);
            if (!$historial) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $request->validate([
                'ingreso_id' => 'required|exists:ingresos,id',
                'personal_id' => 'required|exists:personal,id',
                'presion' => 'required|string|max:10',
                'temperatura' => 'required|numeric|between:0,100.00',
                'glucosa' => 'required|numeric|between:0,999.99',
                'sintomatologia' => 'required|string',
                'observaciones' => 'nullable|string'
            ]);

            $historial->update($request->only([
                'ingreso_id',
                'personal_id',
                'presion',
                'temperatura',
                'glucosa',
                'sintomatologia',
                'observaciones'
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
                ->delete('http://192.168.117.230:3325/api/historiales/'.$id,[
                ]);
            $datas = $response->json();

            $historial = Historial::find($id);
            if (!$historial) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $historial->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }
}