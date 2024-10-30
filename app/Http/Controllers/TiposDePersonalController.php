<?php

namespace App\Http\Controllers;

use App\Models\TiposDePersonal;
use Illuminate\Http\Request;
use App\Models\Personal; 

use App\Models\Token;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;

//dueños
class TiposDePersonalController extends Controller
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
				->get('http://192.168.120.231:3325/api/duenos/index',[
			]);

			$datas = $response->json();

			$tipo_de_personal = TiposDePersonal::all();
            return response()->json([
                'tipo_de_personal' => $tipo_de_personal,
                'duenos' => $datas //respuesta del sig appi
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
                ->post('http://192.168.120.231:3325/api/duenos',[
                    'nombre' => $faker->name,
                    'email' => $faker->email,
                    'telefono' => $faker->phoneNumber
                ]);
            $datas = $response->json();

        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $tipo_de_personal = TiposDePersonal::create([
            'nombre' => $request->nombre
        ]);

        return response()->json([
            'tipos de personal' => $tipo_de_personal,
            'duenos' => $datas //respuesta del sig appi
        ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    public function read($id = null,Request $request)
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
                ->get('http://192.168.120.231:3325/api/duenos/'.$id,[
            ]);
    
            $datas = $response->json();
            
            //this appi
                $tipo_de_personal = TiposDePersonal::find($id);

                return  response()->json([
                    'tipos de personal' => $tipo_de_personal,
                    'duenos' => $datas //respuesta del sig appi
                ], 200);

                if (!$tipo_de_personal) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }
            } else {
                $tipo_de_personal = TiposDePersonal::all();

                return response()->json([
                    'tipos de personal' => $tipo_de_personal,
                    'duenos' => $datas //respuesta del sig appi
                ], 200);
            }
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
                ->put('http://192.168.120.231:3325/api/duenos/'.$id,[
                    'nombre' => $faker->name,
                    'email' => $faker->email,
                    'telefono' => $faker->phoneNumber
                ]);
            $datas = $response->json();

            //this appi
            $tipo_de_personal = TiposDePersonal::find($id);
            $tipo_de_personal->update($request->all());
            //return response()->json($tipo_de_personal);
            return response()->json(['message' => 'Datos actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } 
    }

    public function delete($id,Request $request)
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
                ->delete('http://192.168.120.231:3325/api/duenos/'.$id,[
                ]);
            $datas = $response->json();

            $randomTipoId = rand(1, 10);
            Personal::where('tipo_id', $id)->update(['tipo_id' => $randomTipoId]);

            $tipo_de_personal = TiposDePersonal::find($id);
            if (!$tipo_de_personal) {
                return response()->json(['message' => 'Tipo de personal no encontrado'], 404);
            }

            $tipo_de_personal->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }
}
