<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;


//visitas
class PersonaController extends Controller
{

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
    
            $response = Http::withToken($token)
                ->timeout(80)
                //crear en la tabla de la sig api
                ->post('http://192.168.118.187:3325/visitas/crear',[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    
                    'fecha' => $faker->date(),
                    'motivo' => $faker->sentence(6), 
                    'mascota_id' => $faker->randomNumber(),   
                ]);
                
            $datas = $response->json();

            $validatedData = $request->validate([
                'nombre' => 'required|max:35',
                'apellido_paterno' => 'required|max:35',
                'apellido_materno' => 'required|max:35',
                'sexo' => 'required|in:M,F',
            ]);

            Persona::create($validatedData);

            return response()->json([
                'persona' => $persona,],
                201);
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
        
                $response = Http::withToken($token)
                    ->timeout(80)
                //read a la sig appi
                    ->get('http://192.168.118.187:3325/visitas/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
        
                $datas = $response->json();
        
                //this appi
                $persona = Persona::with([
                    'nombre', 'apellido_paterno', 
                    'apellido_materno','sexo'
                    ])->find($id);
                
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
            $response = Http::withToken($token)
                ->timeout(80)
                ->put('http://192.168.118.187:3325/visitas/'.$id.'/editar',[
                    'fecha' => $faker->date(),
                    'motivo' => $faker->sentence(6), 
                    'mascota_id' => $faker->randomNumber(), 
                ]);
            $datas = $response->json();
        
            //this appi
            $request->validate([
                'tipo_id' => 'required|exists:tipos_de_personal,id',
            ]);
        
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

    public function delete(Persona $persona, Request $request)
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
    
            $response = Http::withToken($token)
                ->timeout(80)
                ->delete('http://192.168.118.187:3325/visitas/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
            $datas = $response->json();
    
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrado'], 404);
        }
        $persona->delete();
        return response()->json(['message' => 'Persona eliminado'], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }
}
