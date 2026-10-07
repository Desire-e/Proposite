<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class TestsController extends Controller {
    // index, show (listado/detalle, solo lectura opositor)

    /**
     * Vista de test concreto
     */
    public function show(Test $test): Response {
        return Inertia::render('tests/show', [
            'test'=> $test,
        ]);
    }

    /**
     * Listado de tests disponibles de la oposición
     * Vista de tests disponibles de la oposición
     */
    public function index(): Response {
        return Inertia::render('tests/index', [
            // tests...
        ]);
    }
}
