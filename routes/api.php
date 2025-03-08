<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TiposDePersonalController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\CamaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\TiposDeEstudioController;
use App\Http\Controllers\EstudiosController;
use App\Http\Controllers\practicaunoController;

use App\Http\Controllers\Auth\SanctumController;
use Spatie\Permission\Middlewares\RoleMiddleware;

Route::get('', function () {
    return response()->json([
        'mensaje' => 'Sistema de urgenicas de Grey Sloan Memorial',
    ]);
});

Route::post('user', [UserController::class, 'create']);

Route::get('/activate/{user}', [AuthController::class, 'activateAccount'])
    ->name('activate.account')
    ->middleware('signed'); //para verificar si el enlace es válido

Route::post('/resend-activation', [AuthController::class, 'resendActivation']);

Route::get('/authorize-user-role/{user}', [AuthController::class, 'authorizeUserRole'])
    ->name('authorize.user.role')
    /* ->middleware(['signed', 'auth:api']) */;

Route::post('login', [AuthController::class, 'login']); 
Route::post('token-command', [TokenController::class, 'store']);

// --------------------------------------------------------------------------------------------------------

Route::middleware('auth:sanctum')->group(function () {
    //------------------------ USER
    Route::get('user/{id?}', [UserController::class, 'read'])
    -> where('id', '[0-9]+');
    Route::get('user/', [UserController::class, 'index']);
         // Foto de perfil
    Route::get('user/p', [UserController::class, 'downloadPP']);
    Route::post('user/p', [UserController::class, 'uploadPP']);
    Route::delete('user/p', [UserController::class, 'deletePP']);

    Route::get('personas', [PersonaController::class, 'index']);
    Route::get('personas/{id}', [PersonaController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('pacientes/', [PacienteController::class, 'index']);
    Route::get('pacientes/{id?}', [PacienteController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('personal/', [PersonalController::class, 'index']);
    Route::get('personal/{id?}', [PersonalController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('tipos-de-personal/', [TiposDePersonalController::class, 'index']);
    Route::get('tipos-de-personal/{id?}', [TiposDePersonalController::class, 'read'])
    -> where('id', '[0-9]+'); 

    Route::get('area/{id?}', [AreaController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('cama', [CamaController::class, 'index']);
    Route::get('cama/{id?}', [CamaController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('tipos-de-estudio/{id?}', [TiposDeEstudioController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('estudios/{id?}', [EstudiosController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('diagnostico/{id?}', [DiagnosticoController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('ingresos/{id?}', [IngresoController::class, 'read'])
    -> where('id', '[0-9]+');

    Route::get('historial/{id?}', [HistorialController::class, 'read'])
    -> where('id', '[0-9]+');

    //------------------------ ADMINISTRADOR
    Route::group(['middleware' => ['roleCustom:Administrador']], function () {
            // users
            Route::put('user/{id}', [UserController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('user/{id}', [UserController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador');

            //personas
            Route::post('personas', [PersonaController::class, 'create']);
            Route::put('personas/{id}', [PersonaController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('personas/{id}', [PersonaController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 

            //pacientes
            Route::post('pacientes', [PacienteController::class, 'create']);
            Route::put('pacientes/{id}', [PacienteController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('pacientes/{id}', [PacienteController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 

            //personal
            Route::post('personal', [PersonalController::class, 'create']);
            Route::put('personal/{id}', [PersonalController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('personal/{id}', [PersonalController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 

            //tipos de personal
            Route::post('tipos-de-personal', [TiposDePersonalController::class, 'create']); 
            Route::put('tipos-de-personal/{id}', [TiposDePersonalController::class, 'update'])
            -> where('id', '[0-9]+'); 
            Route::delete('tipos-de-personal/{id}', [TiposDePersonalController::class, 'delete'])
            -> where('id', '[0-9]+'); 

            //area
            Route::post('area', [AreaController::class, 'create'])
            ->middleware('role:Administrador'); 
            Route::put('area/{id}', [AreaController::class, 'update'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 
            Route::delete('area/{id}', [AreaController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 

            //cama
            Route::post('cama', [CamaController::class, 'create'])
            ->middleware('role:Administrador'); 
            Route::put('cama/{id}', [CamaController::class, 'update'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 
            Route::delete('cama/{id}', [CamaController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 

            //tipos de estudio
            Route::post('tipos-de-estudio', [TiposDeEstudioController::class, 'create'])
            ->middleware('role:Administrador'); 

            Route::put('tipos-de-estudio/{id}', [TiposDeEstudioController::class, 'update'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 
            Route::delete('tipos-de-estudio/{id}', [TiposDeEstudioController::class, 'delete'])
            -> where('id', '[0-9]+')
            ->middleware('role:Administrador'); 

            //estudios
            Route::post('estudios', [EstudiosController::class, 'create']);
            Route::put('estudios/{id}', [EstudiosController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('estudios/{id}', [EstudiosController::class, 'delete'])
            -> where('id', '[0-9]+');

            //diagnósticos
            Route::post('diagnostico', [DiagnosticoController::class, 'create']);
            Route::put('diagnostico/{id}', [DiagnosticoController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('diagnostico/{id}', [DiagnosticoController::class, 'delete'])
            -> where('id', '[0-9]+');

            //ingresos
            Route::post('ingresos', [IngresoController::class, 'create']);
            Route::put('ingresos/{id}', [IngresoController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('ingresos/{id}', [IngresoController::class, 'delete'])
            -> where('id', '[0-9]+');

            //historial
            Route::post('historial', [HistorialController::class, 'create']);
            Route::put('historial/{id}', [HistorialController::class, 'update'])
            -> where('id', '[0-9]+');
            Route::delete('historial/{id}', [HistorialController::class, 'delete'])
            -> where('id', '[0-9]+');
        });
    });

    // VERSION 2 DE LAS RUTAS

    Route::post('v2/registrar', [PersonalController::class, 'registrar']);
    Route::get('v2/puesto/', [TiposDePersonalController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('v2/perfil', [PersonalController::class, 'perfil']);
        Route::put('v2/perfil', [PersonalController::class, 'actualizarPerfil']);
        Route::post('v2/resetPassword', [AuthController::class, 'resetPassword']);
        
        Route::delete('v2/logout', [AuthController::class, 'logout']);
    });