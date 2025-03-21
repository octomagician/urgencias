<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;
//use Exception; //para el trycatch

//para el correo con ruta firmada
use App\Mail\RegistroCorreo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;

//correo con clave de verificación
use Illuminate\Support\Str;
use App\Mail\RegistroCodigoCorreo;

//para los roles de usuario
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\Storage;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

class ##########Controller extends Controller
{






    Route::post('v2/paciente', [PacienteController::class, 'nuevoIngreso']);
    Route::put('v2/paciente/{nss}', [PacienteController::class, 'updatePaciente'])
    ->where('nss', '[0-9]{11}');
            Route::get('v2/paciente/{nss}', [PacienteController::class, 'getPacienteByNss'])
   ->where('nss', '[0-9]{11}');

    Route::post('resetPassword', [AuthController::class, 'resetPassword']);
    Route::get('user/p', [UserController::class, 'downloadPP']);
    Route::post('user/p', [UserController::class, 'uploadPP']);
    Route::delete('user/p', [UserController::class, 'deletePP']);

public function uploadPP(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

   $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    /* $user = Auth::user(); */

    if ($user->profile_photo_path) {
        Storage::disk('spaces')->delete($user->profile_photo_path);
    }

    $path = $request->file('photo')->store('profile_pictures', 'spaces');
    $user->update(['profile_photo_path' => $path]);

    $photoUrl = Storage::disk('spaces')->url($path);

    return response()->json(['message' => 'Foto de perfil actualizada', 'photo_url' => $photoUrl]);
}

public function deletePP()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    if ($user->profile_photo_path) {
        Storage::disk('spaces')->delete($user->profile_photo_path);
        $user->update(['profile_photo_path' => null]);
        return response()->json(['message' => 'Foto de perfil eliminada']);
    }
    return response()->json(['error' => 'No hay foto de perfil para eliminar'], 404);
}

public function downloadPP()
{
    $user = Auth::user();

    if (!$user->profile_photo_path || !Storage::disk('spaces')->exists($user->profile_photo_path)) {
        return response()->json(['error' => 'Foto de perfil no encontrada'], 404);
    }

    $fileContent = Storage::disk('spaces')->get($user->profile_photo_path);

    return response($fileContent)->header('Content-Type', 'image/png');
}

public function nuevoIngreso(Request $request)
    {
        try {

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

            $user->removeRole('guest'); 
    
            // Asignar el rol 'user'
            $user->assignRole('user');

            return response()->json([
                'user' => $user,
                'persona' => $persona,
                'paciente' => $paciente
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error con el recurso', 'error' => $e->getMessage()], 500);
        }
    }

    public function getPacienteByNss($nss)
    {
        try {
            // Buscar el paciente por NSS
            $paciente = Paciente::where('nss', $nss)->firstOrFail();
            //dd($paciente);

            // Obtener la persona asociada al paciente
            $persona = Persona::findOrFail($paciente->persona_id);
            //dd($persona);

            // Obtener el usuario asociado a la persona
            $user = User::findOrFail($persona->users_id);
            //dd($user);

            // Retornar los datos del paciente, persona y usuario
            return response()->json([
                'paciente' => $paciente,
                'persona' => $persona,
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            // Registrar el error en los logs
            Log::error("Error al obtener el paciente por NSS: " . $e->getMessage());

            // Retornar un mensaje de error
            return response()->json([
                'message' => 'No se encontró el paciente con el NSS proporcionado.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Método para actualizar un paciente
    public function updatePaciente(Request $request, $nss)
    {
        try {
                        // Buscar el paciente por NSS
                        $paciente = Paciente::where('nss', $nss)->firstOrFail();
                        
            // Validar los datos recibidos
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|max:35',
                'apellido_paterno' => 'sometimes|required|max:35',
                'apellido_materno' => 'sometimes|required|max:35',
                'sexo' => 'sometimes|required|in:M,F',
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $paciente->persona->users_id,
                'password' => 'sometimes|required|string|min:8|confirmed',
                'nacimiento' => 'sometimes|required|date',
                'direccion' => 'sometimes|required|string|max:100',
                'tel_1' => 'sometimes|required|string|max:20',
                'tel_2' => 'nullable|string|max:20',
            ]);

            // Actualizar la persona asociada al paciente
            $persona = Persona::findOrFail($paciente->persona_id);
            $persona->update([
                'nombre' => $request->nombre ?? $persona->nombre,
                'apellido_paterno' => $request->apellido_paterno ?? $persona->apellido_paterno,
                'apellido_materno' => $request->apellido_materno ?? $persona->apellido_materno,
                'sexo' => $request->sexo ?? $persona->sexo,
            ]);

            // Actualizar el usuario asociado al paciente
            $user = User::findOrFail($persona->users_id);
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            // Actualizar el paciente
            $paciente->update([
                'nacimiento' => $request->nacimiento ?? $paciente->nacimiento,
                'direccion' => $request->direccion ?? $paciente->direccion,
                'tel_1' => $request->tel_1 ?? $paciente->tel_1,
                'tel_2' => $request->tel_2 ?? $paciente->tel_2,
            ]);

            return response()->json([
                'user' => $user,
                'persona' => $persona,
                'paciente' => $paciente
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el paciente', 'error' => $e->getMessage()], 500);
        }
    }


}