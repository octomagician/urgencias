<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TipoDePersonalController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\CamaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\TiposDeEstudiosController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('user/create', [UserController::class, 'create']);

Route::middleware('auth:sanctum')->group(function () {
    // users
    Route::get('user/{id?}', [UserController::class, 'read']); //individual con ID o general sin ID
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'delete']);
   
    //pacientes
    Route::post('pacientes/create', [PacienteController::class, 'create']);
    Route::get('pacientes/{id?}', [PacienteController::class, 'read']);
    Route::put('pacientes/{id}', [PacienteController::class, 'update']);
    Route::delete('pacientes/{id}', [PacienteController::class, 'delete']);

    //personal
    Route::post('personal/create', [PersonalController::class, 'create']);
    Route::get('personal/{id?}', [PersonalController::class, 'read']);
    Route::put('personal/{id}', [PersonalController::class, 'update']);
    Route::delete('personal/{id}', [PersonalController::class, 'delete']);

    //tipos de personal
    Route::post('tipos-de-personal/create', [TipoDePersonalController::class, 'create']);
    Route::get('tipos-de-personal/{id?}', [TipoDePersonalController::class, 'read']);
    Route::put('tipos-de-personal/{id}', [TipoDePersonalController::class, 'update']);
    Route::delete('tipos-de-personal/{id}', [TipoDePersonalController::class, 'delete']);
});