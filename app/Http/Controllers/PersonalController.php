<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Persona;
use App\Models\TiposDePersonal;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

use App\Models\Token;
use Illuminate\Support\Facades\Http;

use Exception;

//para el correo con ruta firmada
use App\Mail\RegistroCorreo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;

//clinica veterinarias
class PersonalController extends Controller
{

public function index()
{
    try { 
        $faker= Faker::create();
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['mensaje' => 'Authorization header not found'], 401);
        }
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenRecord = Token::where('token1', $token)->first();
        if (!$tokenRecord) {
            return response()->json(['mensaje' => 'Token not found'], 404);
        }
        $token2 = $tokenRecord->token2;

        $response = Http::withToken($token2)
            ->timeout(80)
        //read a la sig appi
            ->get('http://192.168.117.230:3325/api/clinicas/index',[
        ]);

        $datas = $response->json();

        $personal = Personal::all();
        return response()->json([
            'personal' => $personal,
            'clinicas' => $datas //respuesta del sig appi
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getmensaje()], 422);
    } 
}

public function create(Request $request)
{
    try { 
        $faker= Faker::create();
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['mensaje' => 'Authorization header not found'], 401);
        }
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenRecord = Token::where('token1', $token)->first();
        if (!$tokenRecord) {
            return response()->json(['mensaje' => 'Token not found'], 404);
        }
        $token2 = $tokenRecord->token2;

        $response = Http::withToken($token2)
            ->timeout(80)
            //crear en la tabla de la sig api
            ->post('http://192.168.117.230:3325/api/clinicas',[

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
                return response()->json(['mensaje' => 'Authorization header not found'], 401);
            }
            $token = str_replace('Bearer ', '', $authHeader);
            $tokenRecord = Token::where('token1', $token)->first();
            if (!$tokenRecord) {
                return response()->json(['mensaje' => 'Token not found'], 404);
            }
            $token2 = $tokenRecord->token2;

            $response = Http::withToken($token2)
                ->timeout(80)
            //read a la sig appi
                ->get('http://192.168.117.230:3325/api/clinicas/'.$id,[]);

            $datas = $response->json();

            //this appi
            $personal = Personal::with(['persona', 'tipoDePersonal'])->find($id);
            
            if (!$personal) {
                return response()->json(['mensaje' => 'Personal no encontrado'], 404);
            }
        } else {
            $personal = Personal::all();
        }
        return response()->json([
            'personal' => $personal,
            'clinicas' => $datas //respuesta del sig appi
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getmensaje()], 422);
    }  
}

public function update(Request $request, $id)
{
    try { 
        $faker= Faker::create();
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['mensaje' => 'Authorization header not found'], 401);
        }
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenRecord = Token::where('token1', $token)->first();
        if (!$tokenRecord) {
            return response()->json(['mensaje' => 'Token not found'], 404);
        }
        $token2 = $tokenRecord->token2;

        //sig appi petición
        $response = Http::withToken($token2)
            ->timeout(80)
            ->put('http://192.168.117.230:3325/api/clinicas/'.$id,[
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
            return response()->json(['mensaje' => 'Personal no encontrado'], 404);
        }

        $personal->update($request->only([
            'tipo_id'
        ]));

        return response()->json(['mensaje' => 'Datos actualizado correctamente'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getmensaje()], 422);
    } 
}

public function delete($id, Request $request)
{
    try {
        $faker= Faker::create();
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['mensaje' => 'Authorization header not found'], 401);
        }
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenRecord = Token::where('token1', $token)->first();
        if (!$tokenRecord) {
            return response()->json(['mensaje' => 'Token not found'], 404);
        }
        $token2 = $tokenRecord->token2;

        $response = Http::withToken($token2)
            ->timeout(80)
            ->delete('http://192.168.117.230:3325/api/clinicas/'.$id,[
            ]);
        $datas = $response->json();

        $personal = Personal::find($id);
        if (!$personal) {
            return response()->json(['mensaje' => 'Personal no encontrado'], 404);
        }
        $personal->delete();
        return response()->json(['mensaje' => 'Personal eliminado'], 204);
    } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
    }
}


// ------------------------------------------------------------

public function registrar(Request $request){

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

    $user->assignRole('guest');

            if (!$user) {
                return response()->json([
                    'mensaje' => 'No se pudo crear el usuario'
                ], 500);
            }

            if (isset($user)) { 

                $signedUrl = URL::temporarySignedRoute(
                    'activate.account', // nombre de la ruta
                    Carbon::now()->addMinutes(5),
                    ['user' => $user->id]
                );

                Mail::to($user->email)->send(new RegistroCorreo($user, 'Registro exitoso', $signedUrl));
                
                return response()->json([
                    'mensaje' => 'Usuario creado, favor de revisar su correo para seguir con el proceso.',
                    'user' => $user,
                ], 201);
            }
}

public function perfil(Request $request)
{
    // Obtener el usuario autenticado
    $user = $request->user();

    // Obtener la persona asociada al usuario
    $persona = Persona::where('users_id', $user->id)->first();

    if ($persona) {
        // Obtener el personal asociado a la persona
        $personal = Personal::where('persona_id', $persona->id)->first();

        if ($personal) {
            // Obtener el tipo de personal (si existe la relación)
            $tipo = TiposDePersonal::find($personal->tipo_id);

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'persona' => [
                    'nombre' => $persona->nombre,
                    'apellido_paterno' => $persona->apellido_paterno,
                    'apellido_materno' => $persona->apellido_materno,
                    'sexo' => $persona->sexo,
                ],
                'personal' => [
                    'tipo_id' => $personal->tipo_id,
                    'tipo' => $tipo ? $tipo->nombre : null,
                ],
            ], 200);
        }

        // Si no hay personal asociado
        return response()->json([
            'mensaje' => 'No se encontró información de personal asociada a la persona.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'persona' => [
                'nombre' => $persona->nombre,
                'apellido_paterno' => $persona->apellido_paterno,
                'apellido_materno' => $persona->apellido_materno,
                'sexo' => $persona->sexo,
            ],
        ], 200);
    }

    // Si no hay persona asociada
    return response()->json([
        'mensaje' => 'No se encontró información adicional del usuario.',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ], 200);
}

public function actualizarPerfil(Request $request)
{
    // Obtener el usuario autenticado
    $user = $request->user();

    // Validar los datos de entrada
    $request->validate([
        'name' => 'sometimes|string|max:255', // Campo opcional
        'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id, // Campo opcional y único excepto para el usuario actual
        'password' => 'sometimes|string|min:8', // Campo opcional

        'nombre' => 'sometimes|string|max:35', // Campo opcional
        'apellido_paterno' => 'sometimes|string|max:35', // Campo opcional
        'apellido_materno' => 'sometimes|string|max:35', // Campo opcional
        'sexo' => 'sometimes|in:M,F', // Campo opcional

        'tipo_id' => 'sometimes|exists:tipos_de_personal,id', // Campo opcional
    ]);

    // Iniciar una transacción de base de datos para asegurar la consistencia
    DB::beginTransaction();

    try {
        // Actualizar los datos del usuario
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Obtener la persona asociada al usuario
        $persona = Persona::where('users_id', $user->id)->first();

        if ($persona) {
            // Actualizar los datos de la persona
            if ($request->has('nombre')) {
                $persona->nombre = $request->nombre;
            }
            if ($request->has('apellido_paterno')) {
                $persona->apellido_paterno = $request->apellido_paterno;
            }
            if ($request->has('apellido_materno')) {
                $persona->apellido_materno = $request->apellido_materno;
            }
            if ($request->has('sexo')) {
                $persona->sexo = $request->sexo;
            }
            $persona->save();

            // Obtener el personal asociado a la persona
            $personal = Personal::where('persona_id', $persona->id)->first();

            if ($personal && $request->has('tipo_id')) {
                // Actualizar el tipo de personal
                $personal->tipo_id = $request->tipo_id;
                $personal->save();
            }
        }

        // Confirmar la transacción
        DB::commit();

        // Devolver una respuesta exitosa
        return response()->json([
            'mensaje' => 'Perfil actualizado correctamente',
            'user' => $user,
            'persona' => $persona,
            'personal' => $personal ?? null,
        ], 200);

    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();

        // Devolver un mensaje de error
        return response()->json([
            'mensaje' => 'Error al actualizar el perfil',
            'error' => $e->getmensaje(),
        ], 500);
    }
}
}