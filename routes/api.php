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

use App\Http\Controllers\Auth\SanctumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//ruta de hola
Route::get('', function () {
    return response()->json([
        'message' => 'Sistema de urgenicas de Grey Sloan Memorial',
    ]);
});


Route::post('login', [AuthController::class, 'login']);
Route::post('user', [UserController::class, 'create']);
Route::post('token-command', [TokenController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    // users
    // create está fuera del middleware
    Route::get('user/{id?}', [UserController::class, 'read']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'delete']);
   
    //personas
    Route::post('personas', [PersonaController::class, 'create']);
    Route::get('personas/{id?}', [PersonaController::class, 'read']);
    Route::put('personas/{id}', [PersonaController::class, 'update']);
    Route::delete('personas/{id}', [PersonaController::class, 'delete']);

    //pacientes
    Route::post('pacientes', [PacienteController::class, 'create']);
    Route::get('pacientes/{id?}', [PacienteController::class, 'read']);
    Route::put('pacientes/{id}', [PacienteController::class, 'update']);
    Route::delete('pacientes/{id}', [PacienteController::class, 'delete']);

    //personal
    Route::post('personal', [PersonalController::class, 'create']);
    Route::get('personal/{id?}', [PersonalController::class, 'read']);
    Route::put('personal/{id}', [PersonalController::class, 'update']);
    Route::delete('personal/{id}', [PersonalController::class, 'delete']);

    //tipos de personal
    Route::post('tipos-de-personal', [TiposDePersonalController::class, 'create']);
    Route::get('tipos-de-personal/{id?}', [TiposDePersonalController::class, 'read']);
    Route::put('tipos-de-personal/{id}', [TiposDePersonalController::class, 'update']);
    Route::delete('tipos-de-personal/{id}', [TiposDePersonalController::class, 'delete']);

    //area
    Route::post('area', [AreaController::class, 'create']);
    Route::get('area/{id?}', [AreaController::class, 'read']);
    Route::put('area/{id}', [AreaController::class, 'update']);
    Route::delete('area/{id}', [AreaController::class, 'delete']);

    //cama
    Route::post('cama', [CamaController::class, 'create']);
    Route::get('cama/{id?}', [CamaController::class, 'read']);
    Route::put('cama/{id}', [CamaController::class, 'update']);
    Route::delete('cama/{id}', [CamaController::class, 'delete']);

    //tipos de estudio
    Route::post('tipos-de-estudio', [TiposDeEstudioController::class, 'create']);
    Route::get('tipos-de-estudio/{id?}', [TiposDeEstudioController::class, 'read']);
    Route::put('tipos-de-estudio/{id}', [TiposDeEstudioController::class, 'update']);
    Route::delete('tipos-de-estudio/{id}', [TiposDeEstudioController::class, 'delete']);

    //estudios
    Route::post('estudios', [EstudiosController::class, 'create']);
    Route::get('estudios/{id?}', [EstudiosController::class, 'read']);
    Route::put('estudios/{id}', [EstudiosController::class, 'update']);
    Route::delete('estudios/{id}', [EstudiosController::class, 'delete']);

    //diagnósticos
    Route::post('diagnostico', [DiagnosticoController::class, 'create']);
    Route::get('diagnostico/{id?}', [DiagnosticoController::class, 'read']);
    Route::put('diagnostico/{id}', [DiagnosticoController::class, 'update']);
    Route::delete('diagnostico/{id}', [DiagnosticoController::class, 'delete']);

    //ingresos
    Route::post('ingresos', [IngresoController::class, 'create']);
    Route::get('ingresos/{id?}', [IngresoController::class, 'read']);
    Route::put('ingresos/{id}', [IngresoController::class, 'update']);
    Route::delete('ingresos/{id}', [IngresoController::class, 'delete']);

    //historial
    Route::post('historial', [HistorialController::class, 'create']);
    Route::get('historial/{id?}', [HistorialController::class, 'read']);
    Route::put('historial/{id}', [HistorialController::class, 'update']);
    Route::delete('historial/{id}', [HistorialController::class, 'delete']);
});