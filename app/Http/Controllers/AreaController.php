<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

//citas
class AreaController extends Controller
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
    
            $response = Http::withToken($token)
                ->timeout(80)
                //crear en la tabla de la sig api
                ->post('http://192.168.118.187:3325/citas/crear',[
                        'email' => $request->input('email'),
                        'password' => $request->input('password'),

                        'mascota_id' => $faker->randomNumber(),
                        'veterinario_id' => $faker->randomNumber(),
                        'clinica_id' => $faker->randomNumber(),
                        'fecha_cita' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
                ]);
            $datas = $response->json();
        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $areas = Area::create([
            'nombre' => $request->nombre
        ]);

        return response()->json($areas, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    public function read($id = null, Request $request)
    {
        if ($id) {
            //lógica para acceder al sig api
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
            ->get('http://192.168.118.187:3325/citas/'.$id,[
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $datas = $response->json();

        //this appi
            $areas = Area::find($id);
            if (!$areas) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
        } else {
            $areas = Area::all();
        }
    
        return response()->json([
            'areas' => $areas,
            'citas' => $datas //respuesta del sig appi
        ], 200);
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
    $response = Http::withToken($token)
        ->timeout(80)
        ->put('http://192.168.118.187:3325/citas/'.$id.'/editar',[
            'email' => $request->input('email'),
            'password' => $request->input('password'),

            'mascota_id' => $faker->randomNumber(),
            'veterinario_id' => $faker->randomNumber(),
            'clinica_id' => $faker->randomNumber(),
            'fecha_cita' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
        ]);
    $datas = $response->json();

    //this appi
        $areas = Area::find($id);
        $areas->update($request->all());
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
    
            $response = Http::withToken($token)
                ->timeout(80)
                ->delete('http://192.168.118.187:3325/citas/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
            $datas = $response->json();

        $areas = Area::find($id);
        $areas->delete();
        return response()->json(['message' => 'Eliminado'], 204);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->validator->errors()], 422);
}
    }
}

