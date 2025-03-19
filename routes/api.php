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
use Spatie\Permission\Middlewares\RoleMiddleware;

Route::middleware('log.activity')->group(function () {
    // GUEST --------------------------------------------
    Route::post('registrar', [UserController::class, 'create']);
    Route::get('puesto', [TiposDePersonalController::class, 'index']); //para el dropdown
    Route::post('/verificar-codigo', [AuthController::class, 'verificarCodigo']);
    Route::post('/reenviar-codigo', [AuthController::class, 'reenviarCodigo']);
    Route::post('entrar', [AuthController::class, 'entrar']);


    

    // USER --------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('salir', [AuthController::class, 'salir']);

        // lecturas para todos los usuarios
        Route::get('camas', [CamaController::class, 'index']);
        Route::get('camas/{id?}', [CamaController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('diagnosticos', [DiagnosticoController::class, 'index']);
        Route::get('diagnosticos/{id?}', [DiagnosticoController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('areas/', [AreaController::class, 'index']);
        Route::get('areas/{id?}', [AreaController::class, 'read'])-> where('id', '[0-9]+');




        Route::get('v2/paciente/{nss}', [PacienteController::class, 'getPacienteByNss'])
            ->where('nss', '[0-9]{11}');
        // perfil
        Route::get('v2/perfil', [PersonaController::class, 'perfil']);
        Route::put('v2/perfil', [PersonaController::class, 'actualizarPerfil']);
        Route::post('v2/resetPassword', [AuthController::class, 'resetPassword']);



    // ADMINISTRADOR --------------------------------------------
        Route::middleware(['role:Administrador'])->group(function () {
            Route::post('camas', [CamaController::class, 'create']);
            Route::put('camas/{id}', [CamaController::class, 'update']) -> where('id', '[0-9]+');
            Route::delete('camas/{id}', [CamaController::class, 'delete'])-> where('id', '[0-9]+');
            
            Route::post('diagnosticos', [DiagnosticoController::class, 'create']);
            Route::put('diagnosticos/{id}', [DiagnosticoController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('diagnosticos/{id}', [DiagnosticoController::class, 'delete'])-> where('id', '[0-9]+');

            Route::post('areas', [AreaController::class, 'create']); 
            Route::put('areas/{id}', [AreaController::class, 'update'])-> where('id', '[0-9]+'); 
            Route::delete('areas/{id}', [AreaController::class, 'delete'])-> where('id', '[0-9]+'); 




             // pacientes
             Route::post('v2/paciente', [PacienteController::class, 'nuevoIngreso']);
             Route::put('v2/paciente/{nss}', [PacienteController::class, 'updatePaciente'])
             ->where('nss', '[0-9]{11}');

        });
    });
});