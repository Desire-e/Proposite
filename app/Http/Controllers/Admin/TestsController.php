<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestsController extends Controller {
    // resource completo (create/store/edit/update/destroy) — Pregunta y OpcionRespuesta 
    // se guardan dentro del mismo store/update vía Form Request, no llevan controller propio


    /**
     * Vista de creación test
     */
    public function create(): Response {
        return Inertia::render('admin/tests/create');
    }

    /**
     * Crear nuevo test
     */
    public function store(Request $request): Response {

    }

    /**
     * Vista de edición test
     * (relleno inicial con datos antiguos)
     */
    public function edit(Request $request): Response {
        return Inertia::render('admin/tests/edit');
    }

    /**
     * Editar nuevo recurso test
     */
    public function update(Request $request): Response {

    }

    /**
     * Eliminar recurso test
     * (modal de confirmación)
     */
    public function destroy(Request $request): Response {

    }

}
