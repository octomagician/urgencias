<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

//veterinarios
class DiagnosticoController extends Controller
{
    public function create(Request $request)
    {
        try { //para cachar errores de validación
            $faker = Faker::create();

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
                ->post('http://192.168.118.187:3325/veterinarios/crear',[
                        'email' => $request->input('email'),
                        'password' => $request->input('password'),

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

        return response()->json($diagnostico, 201);
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
            ->get('http://192.168.118.187:3325/veterinarios/'.$id,[
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
        ->put('http://192.168.118.187:3325/veterinarios/'.$id.'/editar',[
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
                ->delete('http://192.168.118.187:3325/veterinarios/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
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