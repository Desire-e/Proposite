<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Enums\Rol;

/**
 * Middleware. Documentación:
 * 
 * https://laravel.com/framework/docs/12.x/middleware#main-content
 */

class EnsureIsAdmin {
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    public function handle(Request $request, Closure $next): Response {

        // Obtiene usuario autenticado, compara su rol
        if ($request->user()?->rol !== Rol::ADMIN) {
            return redirect()->route('home');
        }
 
        return $next($request);
    }
}
