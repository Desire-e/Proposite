<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IntentosTestController extends Controller {
    // store (enviar respuestas → corrige y crea nota), show (ver resultado)

    /**
     * Crear nuevo intento - corrige y crea nota
     */
    public function store(Request $request): Response {
        
    }

    /**
     * Ver resultados - tras corregir
     */
    public function show(): Response {
        return Inertia::render('tests/intentos/show');
    }


}
