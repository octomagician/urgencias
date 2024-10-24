<?php

namespace App\Http\Controllers;

use App\Models\TiposDeEstudio;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

use App\Models\Token;
use Illuminate\Support\Facades\Http;

//vacunas
class TiposDeEstudioController extends Controller
{
    public function create(Request $request)
    {
    try { //para cachar errores de validación
        $faker = Faker::create();

        //mandar credenciales a la sig api
        //$login = Http::post('http://192.168.118.187:3325/login', [                         
        //    'email' => $request->input('emails'),
        //    'password' => $request->input('passwords'),
        //]);      
        //$token = $login->json()['token_2'];
        
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
    


        $response = Http::withToken($token)
            ->timeout(80)
            //crear en la tabla de la sig api
            ->post('http://192.168.118.187:3325/vacunas/crear',[
                'email' => $request->input('email'),
                'password' => $request->input('password'),

                'nombre' => $faker->word,
                'descripcion' => $faker->sentence(10),
                'dosis' => $faker->randomNumber(2) . ' mg'
            ]);
        $datas = $response->json();

        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $tipo_de_estudio = TiposDeEstudio::create([
            'nombre' => $request->nombre
        ]);

        return response()->json($tipo_de_estudio, 201);
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

            $response = Http::withToken($token)
                ->timeout(80)
                //read a la sig appi
                ->get('http://192.168.118.187:3325/vacunas/'.$id,[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

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
    }
    
    public function update(Request $request, $id)
    {

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
        $response = Http::withToken($token)
            ->timeout(80)
            ->put('http://192.168.118.187:3325/vacunas/'.$id.'/editar',[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
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
    }

    public function delete($id)
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
    
            $response = Http::withToken($token)
                ->timeout(80)
                ->delete('http://192.168.118.187:3325/vacunas/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
            $datas = $response->json();

            $tipo_de_estudio = TiposDeEstudio::find($id);
            if (!$tipo_de_estudio) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $tipo_de_estudio->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }
}
