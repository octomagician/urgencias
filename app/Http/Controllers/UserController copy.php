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


}