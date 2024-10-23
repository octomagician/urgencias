<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Persona;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;

//mascotas
class PacienteController extends Controller
{
    public function index(Request $request)
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

        // Realiza la solicitud a la API usando el token obtenido
        $response = Http::withOptions(['verify' => false])
            ->withToken($token2)
            ->timeout(80)
            ->get('https://9315-104-28-199-132.ngrok-free.app/mascotas', [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
        // Asegúrate de manejar posibles errores en la respuesta
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch mascotas'], 500);
        }

        $datas = $response->json();

        // Obtén los pacientes de la base de datos
        $pacientes = Paciente::all();

        // Devuelve la respuesta
        return response()->json([
            'pacientes' => $pacientes,
            'mascotas' => $datas,
        ], 200);
    }


    public function create(Request $request)
    {
        try {

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


            $response =  Http::withOptions(['verify' => false])
                ->withToken($token2)
                ->timeout(80)
                ->post('https://9315-104-28-199-132.ngrok-free.app/mascotas/crear', [
                    'emails' => $request->input('emails'),
                    'password' => $request->input('passwords'),
                    'nombre' => $faker->name,
                    'edad' => $faker->numberBetween(1, 10),
                    'dueno' => [
                        'nombre' => $faker->name,
                        'email' => $faker->email,
                        'telefono' => $faker->phoneNumber,
                    ],
                    'raza' => [
                        'nombre' => $faker->word,
                        'descripcion' => $faker->sentence,
                    ],
                    'vacuna' => [
                        'nombre' => $faker->word,
                        'descripcion' => $faker->sentence,
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


            $response =  Http::withOptions(['verify' => false])
                ->withToken($token2)
                ->timeout(80)
                ->get('https://9315-104-28-199-132.ngrok-free.app/mascotas/' . $id, [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);

            $datas = $response->json();

            $px = Paciente::with('persona')->find($id);

            if (!$px) {
                return response()->json(['message' => 'Paciente no encontrado'], 404);
            }


            return response()->json([
                'paciente' => $px,
                'mascotas' => $datas
            ], 200);
        } else {
            return response()->json(['message' => 'Servicio para consultarlos a todos no disponible'], 404);
        }
    }

    public function update(Request $request, $id)
    {
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


        $response =  Http::withOptions(['verify' => false])
            ->withToken($token2)
            ->timeout(80)
            ->put('https://9315-104-28-199-132.ngrok-free.app/mascotas/' . $id . '/editar', [
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


            $response =  Http::withOptions(['verify' => false])
                ->withToken($token2)
                ->timeout(80)
                ->delete('https://9315-104-28-199-132.ngrok-free.app/mascotas/' . $id, [
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
