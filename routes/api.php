<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TiposDePersonalController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\CamaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\TiposDeEstudioController;
use App\Http\Controllers\EstudiosController;
use App\Http\Controllers\Auth\SanctumController;
use Spatie\Permission\Middlewares\RoleMiddleware;

Route::middleware('log.activity')->group(function () {
    // GUEST --------------------------------------------
    Route::post('registrar', [UserController::class, 'create']);
    Route::post('/verificar-codigo', [AuthController::class, 'verificarCodigo']);



















    Route::post('/reenviar-codigo', [AuthController::class, 'reenviarCodigo']);

    Route::get('v2/puesto/', [TiposDePersonalController::class, 'index']); //para el dropdown

    Route::get('/activate/{user}', [AuthController::class, 'activateAccount'])
        ->name('activate.account')
        ->middleware('signed'); //para verificar si el enlace es válido

    

    Route::post('login', [AuthController::class, 'login']); 
    Route::post('token-command', [TokenController::class, 'store']);

    // USER --------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('v2/logout', [AuthController::class, 'logout']);

        // perfil
        Route::get('v2/perfil', [PersonaController::class, 'perfil']);
        Route::put('v2/perfil', [PersonaController::class, 'actualizarPerfil']);
        Route::post('v2/resetPassword', [AuthController::class, 'resetPassword']);

        // todos los gets
        Route::get('v2/paciente/{nss}', [PacienteController::class, 'getPacienteByNss'])
            ->where('nss', '[0-9]{11}');

    // ADMINISTRADOR --------------------------------------------
            Route::middleware(['roleCustom:Administrador'])->group(function () {

             // pacientes
             Route::post('v2/paciente', [PacienteController::class, 'nuevoIngreso']);
             Route::put('v2/paciente/{nss}', [PacienteController::class, 'updatePaciente'])
             ->where('nss', '[0-9]{11}');

            });
        });
    });