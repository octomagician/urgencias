<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

//citas
class AreaController extends Controller
{
    public function create(Request $request)
    {
        try { //para cachar errores de validación
            $faker = Faker::create();
            //mandar credenciales a la sig api
            $login = Http::post('http://192.168.118.187:3325/login', [                         
                'email' => $request->input('emails'),
                'password' => $request->input('passwords'),
            ]);
            $token = $login->json()['token_2'];
    
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

    public function read($id = null)
    {
        if ($id) {
            //lógica para acceder al sig api
        $login = Http::post('http://192.168.118.187:3325/login', [                         
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $token = $login->json()['token_2'];

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
    $login = Http::post('http://192.168.118.187:3325/login', [                         
        'email' => $request->input('email'),
        'password' => $request->input('password'),
    ]);
    $token = $login->json()['token_2'];
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

    public function delete($id)
    {
        try {
            $login = Http::post('http://192.168.118.187:3325/login', [                         
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            $token = $login->json()['token_2'];
    
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

