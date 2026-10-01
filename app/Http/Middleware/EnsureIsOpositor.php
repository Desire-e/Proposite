<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Rol;

class EnsureIsAdmin {
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    public function handle(Request $request, Closure $next): Response {

        if ($request->user()?->rol !== Rol::OPOSITOR) {
            return redirect()->route('home');
        }
 
        return $next($request);
    }
}

