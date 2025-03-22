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
use App\Http\Controllers\LogController;
use App\Http\Controllers\SSEController;
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

        /*---------------------------------------*/
        Route::get('user/', [UserController::class, 'index']);
        Route::get('user/{id?}', [UserController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('pacientes/', [PacienteController::class, 'index']);
        Route::get('pacientes/{id?}', [PacienteController::class, 'read'])-> where('id', '[0-9]+');
        /* no adecuado al cambio de personal
        Route::get('ingresos/{id?}', [IngresoController::class, 'index']);
        Route::get('ingresos/{id?}', [IngresoController::class, 'read'])-> where('id', '[0-9]+');
        */
        /*---------------------------------------*/
        Route::get('camas', [CamaController::class, 'index']);
        Route::get('camas/{id?}', [CamaController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('diagnosticos', [DiagnosticoController::class, 'index']);
        Route::get('diagnosticos/{id?}', [DiagnosticoController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('areas/', [AreaController::class, 'index']);
        Route::get('areas/{id?}', [AreaController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('historial', [HistorialController::class, 'index']);
        Route::get('historial/{id?}', [HistorialController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('estudios', [EstudiosController::class, 'index']);
        Route::get('estudios/{id?}', [EstudiosController::class, 'read'])-> where('id', '[0-9]+');
        Route::get('tipos-personal/', [TiposDePersonalController::class, 'index']);
        Route::get('tipos-personal/{id?}', [TiposDePersonalController::class, 'read'])-> where('id', '[0-9]+'); 
        Route::get('tipos-de-estudio', [TiposDeEstudioController::class, 'index']);
        Route::get('tipos-de-estudio/{id?}', [TiposDeEstudioController::class, 'read'])-> where('id', '[0-9]+');

    // ADMINISTRADOR --------------------------------------------
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/sse', [SSEController::class, 'test']);

            Route::get('logs/', [LogController::class, 'index']);
            Route::get('logs/{id?}', [LogController::class, 'read'])->where('id', '^[a-fA-F0-9]{24}$');

            /*---------------------------------------*/
            Route::post('user', [UserController::class, 'create']);
            Route::put('user/{id}', [UserController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('user/{id}', [UserController::class, 'delete'])-> where('id', '[0-9]+');
        
            Route::post('pacientes', [PacienteController::class, 'create']);
            Route::put('pacientes/{id}', [PacienteController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('pacientes/{id}', [PacienteController::class, 'delete'])-> where('id', '[0-9]+'); 

            /* no adecuado al cambio de personal
            Route::post('ingresos', [IngresoController::class, 'create']);
            Route::put('ingresos/{id}', [IngresoController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('ingresos/{id}', [IngresoController::class, 'delete'])-> where('id', '[0-9]+');
            */

            /*---------------------------------------*/

            Route::post('camas', [CamaController::class, 'create']);
            Route::put('camas/{id}', [CamaController::class, 'update']) -> where('id', '[0-9]+');
            Route::delete('camas/{id}', [CamaController::class, 'delete'])-> where('id', '[0-9]+');
            
            Route::post('diagnosticos', [DiagnosticoController::class, 'create']);
            Route::put('diagnosticos/{id}', [DiagnosticoController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('diagnosticos/{id}', [DiagnosticoController::class, 'delete'])-> where('id', '[0-9]+');

            Route::post('areas', [AreaController::class, 'create']); 
            Route::put('areas/{id}', [AreaController::class, 'update'])-> where('id', '[0-9]+'); 
            Route::delete('areas/{id}', [AreaController::class, 'delete'])-> where('id', '[0-9]+'); 

            Route::post('historial', [HistorialController::class, 'create']);
            Route::put('historial/{id}', [HistorialController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('historial/{id}', [HistorialController::class, 'delete'])-> where('id', '[0-9]+');

            Route::post('estudios', [EstudiosController::class, 'create']);
            Route::put('estudios/{id}', [EstudiosController::class, 'update'])-> where('id', '[0-9]+');
            Route::delete('estudios/{id}', [EstudiosController::class, 'delete'])-> where('id', '[0-9]+');

            Route::post('tipos-personal', [TiposDePersonalController::class, 'create']); 
            Route::put('tipos-personal/{id}', [TiposDePersonalController::class, 'update'])-> where('id', '[0-9]+'); 
            Route::delete('tipos-personal/{id}', [TiposDePersonalController::class, 'delete'])-> where('id', '[0-9]+'); 

            Route::post('tipos-de-estudio', [TiposDeEstudioController::class, 'create']); 
            Route::put('tipos-de-estudio/{id}', [TiposDeEstudioController::class, 'update'])-> where('id', '[0-9]+'); 
            Route::delete('tipos-de-estudio/{id}', [TiposDeEstudioController::class, 'delete'])-> where('id', '[0-9]+'); 
        });
    });
});