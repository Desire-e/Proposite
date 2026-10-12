<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureIsOpositor;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Roles
        $middleware->alias([ 
            'admin' => EnsureIsAdmin::class, 
            'opositor' => EnsureIsOpositor::class 
        ]);

        // Usuarios no autenticados (guests) que intentan acceder a rutas protegidas 
        $middleware->redirectGuestsTo('/login');

        // Usuarios autenticados que intentan acceder a rutas para invitados (guests) 
        $middleware->redirectUsersTo('/dashboard');    

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
