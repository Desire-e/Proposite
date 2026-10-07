<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstadisticasController extends Controller {

    /**
     * Listado estadísticas de opositor en su perfil
     * (una vista calculada, no es CRUD)
     */
    // public function index(Request $request): Response {
    // }
    /**
     * Vista de estadísticas de opositor en su perfil
     */
    public function show(Request $request): Response {
        return Inertia::render('tests/estadisticas/show');
    }

}
