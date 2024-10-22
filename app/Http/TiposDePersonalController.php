<?php

namespace App\Http\Controllers;

use App\Models\TiposDePersonal;
use Illuminate\Http\Request;

//dueños
class TiposDePersonalController extends Controller
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
                ->post('http://192.168.118.187:3325/duenos/crear',[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),

                    'nombre' => $faker->name,
                    'email' => $faker->mail,
                    'telefono' => $faker->phoneNumber
                ]);
            $datas = $response->json();

        $request->validate([
            'nombre' => 'required|max:50'
        ]);

        $tipo_de_personal = TiposDePersonal::create([
            'nombre' => $request->nombre
        ]);

        return response()->json($tipo_de_personal, 201);
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
            ->get('http://192.168.118.187:3325/duenos/'.$id,[
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $datas = $response->json();

        //this appi
            $tipo_de_personal = TiposDePersonal::find($id);
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
            ->put('http://192.168.118.187:3325/duenos/'.$id.'/editar',[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'nombre' => $faker->name,
                'email' => $faker->mail,
                'telefono' => $faker->phoneNumber
            ]);
        $datas = $response->json();

        //this appi
        $tipo_de_personal = TiposDePersonal::find($id);
        $tipo_de_personal->update($request->all());
        //return response()->json($tipo_de_personal);
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
                ->delete('http://192.168.118.187:3325/duenos/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
            $datas = $response->json();

            $tipo_de_personal = TiposDePersonal::find($id);
            $tipo_de_personal->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
            }
    }
}
