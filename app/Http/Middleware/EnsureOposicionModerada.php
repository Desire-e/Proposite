<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asegura que un administrador esté en la comunidad 
 * que eligió en admin/welcome.
 * 
 * Si la oposición se elimina cuando está dentro, se 
 * le redirige a admin/welcome
 */

class EnsureOposicionModerada {

    public function handle(Request $request, Closure $next): Response {

        // null si el admin no eligió ninguna oposición, o si la que eligió
        // fue eliminada (el borrado lógico la oculta de la relación)
        if ($request->user()->esAdmin() && $request->user()->oposicionModerada === null) {
            return to_route('admin.welcome');
        }

        return $next($request);
    }
}
