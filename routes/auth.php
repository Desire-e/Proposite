<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;


/**************************************
 * RUTAS RELACIONADAS CON AUTENTICACIÓN 
 **************************************/

/**
 * Middleware guest:
 * 
 * Solo pueden acceder usuarios no autenticados.
 * Usuarios ya autenticados los redirige (configurable en bootstrap/app.php, redirectUsersTo)
 * 
 * Middleware auth:
 * 
 * Solo pueden acceder usuarios autenticados.
 * Usuarios no autenticados los redirige (configurable en bootstrap/app.php, redirectGuestsTo),
 */

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'login'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'logout'])
        ->name('logout');
});
