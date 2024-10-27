<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Persona;
use App\Models\TipoDePersonal;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

use App\Models\Token;
use Illuminate\Support\Facades\Http;

//clinica veterinarias
class PersonalController extends Controller
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
    
            $response = Http::withToken($token2)
                ->timeout(80)
                //crear en la tabla de la sig api
                ->post('http://192.168.118.187:3325/clinicas/crear',[

                    'nombre' => $faker->name,
                    'direccion' => $faker->numberBetween,
                    'telefono' => $faker->phoneNumber
                ]);
            $datas = $response->json();
        
            //acciones en this appi --------------------------------------
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',

                'nombre' => 'required|max:35',
                'apellido_paterno' => 'required|max:35',
                'apellido_materno' => 'required|max:35',
                'sexo' => 'required|in:M,F',

                'tipo_id' => 'required|exists:tipos_de_personal,id',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) 
            ]);

            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
                'users_id' => $user->id,
            ]);

            $personal = Personal::create([
                'persona_id' => $persona->id,
                'tipo_id' => $request->tipo_id,
            ]);

            return response()->json([
                'user' => $user,
                'persona' => $persona,
                'personal' => $personal,
                'clinicas' => $datas
            ],
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

            $response = Http::withToken($token2)
                ->timeout(80)
            //read a la sig appi
                ->get('http://192.168.118.187:3325/clinicas/'.$id,[]);

            $datas = $response->json();

            //this appi
            $personal = Personal::with(['persona', 'tipoDePersonal'])->find($id);
            
            if (!$personal) {
                return response()->json(['message' => 'Personal no encontrado'], 404);
            }
        } else {
            $personal = Personal::all();
        }
        return response()->json([
            'personal' => $personal,
            'clinicas' => $datas //respuesta del sig appi
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
            ->put('http://192.168.118.187:3325/clinicas/'.$id.'/editar',[
                'nombre' => $faker->firstName,
                'edad' => $faker->numberBetween(1, 10),
            ]);
        $datas = $response->json();

        //this appi
        $request->validate([
            'tipo_id' => 'required|exists:tipos_de_personal,id',
        ]);

        $personal = Personal::find($id);
        if (!$personal) {
            return response()->json(['message' => 'Personal no encontrado'], 404);
        }

        $personal->update($request->only([
            'tipo_id'
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
            ->delete('http://192.168.118.187:3325/clinicas/'.$id,[
            ]);
        $datas = $response->json();

        $personal = Personal::find($id);
        if (!$personal) {
            return response()->json(['message' => 'Personal no encontrado'], 404);
        }
        $personal->delete();
        return response()->json(['message' => 'Personal eliminado'], 204);
    } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
    }
}
}
