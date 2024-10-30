<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Area;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

//consultas
class CamaController extends Controller
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
				->get('http://192.168.120.231:3325/api/consultas/index',[
			]);

			$datas = $response->json();

			$cama = Cama::all();
            return response()->json([
                'cama' => $cama,
                'consultas' => $datas //respuesta del sig appi
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
                ->post('http://192.168.120.231:3325/api/consultas',[
/*                         'mascota_id' => $faker->randomNumber(), 
                        'veterinario_id' => $faker->randomNumber(),  */
                        'diagnostico' => $faker->sentence(5),
                        'tratamiento' => $faker->sentence(8),
                ]);
            $datas = $response->json();

            $request->validate([
                'numero_cama' => 'required|integer|unique:camas,numero_cama',
                'area_id' => 'required|exists:areas,id'
            ]);
    
            $cama = Cama::create([
                'numero_cama' => $request->numero_cama,
                'area_id' => $request->area_id
            ]);
    
            return response()->json([
                'cama' => $cama,
                'consultas' => $datas
            ], 201);


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } 
    }

    public function read($id = null)
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
                    ->get('http://192.168.120.231:3325/api/consultas/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);

                $datas = $response->json();

                $cama = Cama::find($id);
                if (!$cama) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }
            } else {
                $cama = Cama::all();
            }
            return response()->json([
                'cama' => $cama,
                'consultas' => $datas //respuesta del sig appi
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
                ->put('http://192.168.120.231:3325/api/consultas/'.$id,[
                    'mascota_id' => $faker->randomNumber(), 
                    'veterinario_id' => $faker->randomNumber(), 
                    'diagnostico' => $faker->sentence(5),
                    'tratamiento' => $faker->sentence(8),
                ]);
            $datas = $response->json();

            $cama = Cama::find($id);
            if (!$cama) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
    
            $request->validate([
                'numero_cama' => 'required|integer|unique:camas,numero_cama,' . $cama->id,
                'area_id' => 'required|exists:areas,id'
            ]);
    
            $cama->update($request->only(['numero_cama', 'area_id']));
            return response()->json(['message' => 'Datos actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } 
    }

    public function delete(Request $request, $id)
    {
        try { 
            $faker = Faker::create();
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
                ->delete('http://192.168.120.231:3325/api/consultas/'.$id, []);
            $datas = $response->json();
    
            $cama = Cama::find($id);
            if (!$cama) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
    
            $cama->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
    
}