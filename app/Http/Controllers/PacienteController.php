<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $login = Http::post('http://192.168.1.13:3325/login', [                         
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $token = $login->json()['token_2'];

        $response = Http::withToken($token)
            ->timeout(80)
            ->get('http://192.168.1.13:3325/mascotas',[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

        $datas = $response->json();

        $pacientes = Paciente::all();
        return response()->json([
            'pacientes' => $pacientes,
            'mascotas' => $datas], 200);
    }
    public function create(Request $request)
    {
       try {
           
    $faker = Faker::create();
        $login = Http::post('http://192.168.1.13:3325/login', [                         
            'email' => $request->input('emails'),
            'password' => $request->input('passwords'),
        ]);
        $token = $login->json()['token_2'];

        $response = Http::withToken($token)
            ->timeout(80)
            ->post('http://192.168.1.13:3325/mascotas/crear',[
                'emails' => $request->input('emails'),
                'password' => $request->input('passwords'),
                    'nombre' => $faker->name,  // Nombre de la mascota
                    'edad' => $faker->numberBetween(1, 10),  // Edad de la mascota
                    'dueno' => [
                        'nombre' => $faker->name,  // Nombre del dueño
                        'email' => $faker->email,  // Email del dueño
                        'telefono' => $faker->phoneNumber,  // Teléfono del dueño
                    ],
                    'raza' => [
                        'nombre' => $faker->word,  // Nombre de la raza
                        'descripcion' => $faker->sentence,  // Descripción de la raza
                    ],
                    'vacuna' => [
                        'nombre' => $faker->word,  // Nombre de la vacuna
                        'descripcion' => $faker->sentence,  // Descripción de la vacuna
                    ]
                ]);

        $datas = $response->json();

            
        $validatedData = $request->validate([
            'nombre' => 'required|max:35',
            'apellido_paterno' => 'required|max:35',
            'apellido_materno' => 'required|max:35',
            'sexo' => 'required|in:M,F',

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            'nacimiento' => 'required|date',
            'nss' => 'required|string|max:11|unique:pacientes',
            'direccion' => 'required|string|max:100',
            'tel_1' => 'required|string|max:20',
            'tel_2' => 'nullable|string|max:20',
        ]);

         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
         ]);

        $persona = Persona::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'sexo' => $request->sexo,
        ]);
    
         $paciente = Paciente::create([
            'persona_id' => $persona->id,
            'nacimiento' => $request->nacimiento,
            'nss' => $request->nss,
            'direccion' => $request->direccion,
            'tel_1' => $request->tel_1,
            'tel_2' => $request->tel_2,
        ]);

        return response()->json([
            'user' => $user,
            'persona' => $persona,
            'paciente' => $paciente,
            'mascotas' => $datas
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->validator->errors()], 422);
    } 
    }

    public function read($id = null, Request $request)
    {
     
        if ($id) {
            $login = Http::post('http://192.168.1.13:3325/login', [                         
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            $token = $login->json()['token_2'];
    
            $response = Http::withToken($token)
                ->timeout(80)
                ->get('http://192.168.1.13:3325/mascotas/'.$id,[
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
    
            $datas = $response->json();

            $px = Paciente::with('persona')->find($id);
            
            if (!$px) {
                return response()->json(['message' => 'Paciente no encontrado'], 404);
            }


            return response()->json([
                'paciente' => $px
                ,'mascotas' => $datas
            ], 200);

        } else {
            return response()->json(['message' => 'Servicio para consultarlos a todos no disponible'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $faker= Faker::create();
        $login = Http::post('http://192.168.1.13:3325/login', [                         
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $token = $login->json()['token_2'];

        $response = Http::withToken($token)
            ->timeout(80)
            ->put('http://192.168.1.13:3325/mascotas/'.$id.'/editar',[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'nombre' => $faker->firstName,
                'edad' => $faker->numberBetween(1, 10),
            ]);

        $datas = $response->json();

        $request->validate([
            'nacimiento' => 'required|date',
            'nss' => 'required|string',
            'direccion' => 'required|string',
            'tel_1' => 'required|string',
            'tel_2' => 'nullable|string'
        ]);
    
        $px = Paciente::find($id);
        if (!$px) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }
    
        $px->update($request->only([
            'nacimiento',
            'nss',
            'direccion',
            'tel_1',
            'tel_2'
        ]));
    
        return response()->json(['message' => 'Datos actualizado correctamente'], 200);
    }
    
    public function delete($id, Request $request)
    {
        try {
        $login = Http::post('http://192.168.1.13:3325/login', [                         
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        $token = $login->json()['token_2'];

        $response = Http::withToken($token)
            ->timeout(80)
            ->delete('http://192.168.1.13:3325/mascotas/'.$id,[
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

        $datas = $response->json();
        $px = Paciente::find($id);
        $px->delete();
       return response()->json(204);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->validator->errors()], 422);
    }
    }
}
