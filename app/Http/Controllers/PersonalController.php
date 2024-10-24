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
        try { //para cachar errores de validación
            $faker = Faker::create();
            //mandar credenciales a la sig api

                            // Obtén el valor del encabezado Authorization
                            $authHeader = $request->header('Authorization');

                            // Verifica si el encabezado está presente
                            if (!$authHeader) {
                                return response()->json(['message' => 'Authorization header not found'], 401);
                            }
                    
                            // Extrae el token Bearer del encabezado
                            $token = str_replace('Bearer ', '', $authHeader); // Esto elimina la parte 'Bearer ' y deja solo el token
                    
                            // Ahora puedes usar este token para buscar en tu base de datos
                            $tokenRecord = Token::where('token1', $token)->first();
                    
                            // Verifica si el token fue encontrado
                            if (!$tokenRecord) {
                                return response()->json(['message' => 'Token not found'], 404);
                            }
                    
                            // Accede al campo 'token2'
                            $token2 = $tokenRecord->token2;
    
            $response = Http::withToken($token2)
                ->timeout(80)
                //crear en la tabla de la sig api
                ->post('https://7fc8-2806-267-148b-15bf-125-e767-a22d-b16a.ngrok-free.app/clinicas/crear',[

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
    if ($id) {

                // Obtén el valor del encabezado Authorization
                $authHeader = $request->header('Authorization');

                // Verifica si el encabezado está presente
                if (!$authHeader) {
                    return response()->json(['message' => 'Authorization header not found'], 401);
                }
        
                // Extrae el token Bearer del encabezado
                $token = str_replace('Bearer ', '', $authHeader); // Esto elimina la parte 'Bearer ' y deja solo el token
        
                // Ahora puedes usar este token para buscar en tu base de datos
                $tokenRecord = Token::where('token1', $token)->first();
        
                // Verifica si el token fue encontrado
                if (!$tokenRecord) {
                    return response()->json(['message' => 'Token not found'], 404);
                }
        
                // Accede al campo 'token2'
                $token2 = $tokenRecord->token2;

        $response = Http::withToken($token2)
            ->timeout(80)
        //read a la sig appi
            ->get('https://7fc8-2806-267-148b-15bf-125-e767-a22d-b16a.ngrok-free.app/clinicas/'.$id,[]);

        $datas = $response->json();

        //this appi
        $personal = Personal::with(['persona', 'tipoDePersonal'])->find($id);
        
        if (!$personal) {
            return response()->json(['message' => 'Personal no encontrado'], 404);
        }

        return response()->json([
            'personal' => $personal,
            'clinicas' => $datas //respuesta del sig appi
        ], 200);
    } else {
        return response()->json(['message' => 'Servicio para consultarlos a todos no disponible'], 404);
    }
}

public function update(Request $request, $id)
{
    //sig appi acceso
    $faker= Faker::create();

                // Obtén el valor del encabezado Authorization
                $authHeader = $request->header('Authorization');

                // Verifica si el encabezado está presente
                if (!$authHeader) {
                    return response()->json(['message' => 'Authorization header not found'], 401);
                }
        
                // Extrae el token Bearer del encabezado
                $token = str_replace('Bearer ', '', $authHeader); // Esto elimina la parte 'Bearer ' y deja solo el token
        
                // Ahora puedes usar este token para buscar en tu base de datos
                $tokenRecord = Token::where('token1', $token)->first();
        
                // Verifica si el token fue encontrado
                if (!$tokenRecord) {
                    return response()->json(['message' => 'Token not found'], 404);
                }
        
                // Accede al campo 'token2'
                $token2 = $tokenRecord->token2;

    //sig appi petición
    $response = Http::withToken($token2)
        ->timeout(80)
        ->put('https://7fc8-2806-267-148b-15bf-125-e767-a22d-b16a.ngrok-free.app/clinicas/'.$id.'/editar',[
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
}

public function delete($id, Request $request)
{
    try {
        
                        // Obtén el valor del encabezado Authorization
                        $authHeader = $request->header('Authorization');

                        // Verifica si el encabezado está presente
                        if (!$authHeader) {
                            return response()->json(['message' => 'Authorization header not found'], 401);
                        }
                
                        // Extrae el token Bearer del encabezado
                        $token = str_replace('Bearer ', '', $authHeader); // Esto elimina la parte 'Bearer ' y deja solo el token
                
                        // Ahora puedes usar este token para buscar en tu base de datos
                        $tokenRecord = Token::where('token1', $token)->first();
                
                        // Verifica si el token fue encontrado
                        if (!$tokenRecord) {
                            return response()->json(['message' => 'Token not found'], 404);
                        }
                
                        // Accede al campo 'token2'
                        $token2 = $tokenRecord->token2;

        $response = Http::withToken($token2)
            ->timeout(80)
            ->delete('https://7fc8-2806-267-148b-15bf-125-e767-a22d-b16a.ngrok-free.app/clinicas/'.$id,[
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
