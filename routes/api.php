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

        // todos los gets
        Route::get('cama', [CamaController::class, 'index']);



        
        Route::get('cama/{id?}', [CamaController::class, 'read'])
            -> where('id', '[0-9]+');
        //al rato meto estos en ruta de admon
        //cama
        Route::post('cama', [CamaController::class, 'create']);
        //->middleware('role:Administrador'); 
        Route::put('cama/{id}', [CamaController::class, 'update'])
        -> where('id', '[0-9]+');
        //->middleware('role:Administrador'); 
        Route::delete('cama/{id}', [CamaController::class, 'delete'])
        -> where('id', '[0-9]+');
        //->middleware('role:Administrador'); 





        Route::get('v2/paciente/{nss}', [PacienteController::class, 'getPacienteByNss'])
            ->where('nss', '[0-9]{11}');
        




        // perfil
        Route::get('v2/perfil', [PersonaController::class, 'perfil']);
        Route::put('v2/perfil', [PersonaController::class, 'actualizarPerfil']);
        Route::post('v2/resetPassword', [AuthController::class, 'resetPassword']);



    // ADMINISTRADOR --------------------------------------------
            Route::middleware(['roleCustom:Administrador'])->group(function () {

             // pacientes
             Route::post('v2/paciente', [PacienteController::class, 'nuevoIngreso']);
             Route::put('v2/paciente/{nss}', [PacienteController::class, 'updatePaciente'])
             ->where('nss', '[0-9]{11}');

            });
        });
    });