<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemariosController extends Controller {

    /**
     * Obtiene listado del temario por oposición
     * Vista del temario (solo lectura para opositor)
     */
    public function index(): Response {
        return Inertia::render('temarios/index', [
            // temario...
        ]);
    }

    /**
     * Checklist personal en el temario
     * (es lo único que opositor podrá modificar en él, y solo él mismo lo verá)
     */
    public function toggleCheck(Request $request): Response {

    }


}
