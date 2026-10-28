<?php

use App\Http\Controllers\Auth\AutenticacionController;
use App\Http\Controllers\Auth\RegistroController;
use Illuminate\Support\Facades\Route;


/**************************************
 * RUTAS RELACIONADAS CON AUTENTICACIÓN 
 **************************************/

/**
 * Middleware guest:
 * Solo pueden acceder usuarios no autenticados.
 * Usuarios ya autenticados los redirige (configurable en bootstrap/app.php, redirectUsersTo)
 * 
 * Middleware auth:
 * Solo pueden acceder usuarios autenticados.
 * Usuarios no autenticados los redirige (configurable en bootstrap/app.php, redirectGuestsTo),
 */

Route::middleware('guest')->group(function () {
    Route::get('register', [RegistroController::class, 'create'])
        ->name('register');

    Route::post('register', [RegistroController::class, 'store']);

    Route::get('login', [AutenticacionController::class, 'login'])
        ->name('login');

    Route::post('login', [AutenticacionController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AutenticacionController::class, 'logout'])
        ->name('logout');
});
