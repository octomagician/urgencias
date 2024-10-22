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

//clinica veterinarias
class PersonalController extends Controller
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
                ->post('http://192.168.118.187:3325/clinicas/crear',[
                        'email' => $request->input('email'),
                        'password' => $request->input('password'),

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
            'personal' => $personal,],
     201);
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
            ->get('http://192.168.118.187:3325/clinicas/'.$id,[
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

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
    $login = Http::post('http://192.168.118.187:3325/login', [                         
        'email' => $request->input('email'),
        'password' => $request->input('password'),
    ]);
    $token = $login->json()['token_2'];
    //sig appi petición
    $response = Http::withToken($token)
        ->timeout(80)
        ->put('http://192.168.118.187:3325/clinicas/'.$id.'/editar',[
            'email' => $request->input('email'),
            'password' => $request->input('password'),
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
            ->delete('http://192.168.118.187:3325/clinicas/'.$id,[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
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
