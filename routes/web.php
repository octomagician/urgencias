<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificacionController;

Route::get('/', function () {
    return view('welcome');
});
