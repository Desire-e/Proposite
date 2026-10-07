<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicacionesController extends Controller {

    /**
     * Obtiene listado de publicaciones
     * Muestra página de inicio de opositor - foro de publicaciones
     */
    public function index(): Response {
        return Inertia::render('publicaciones/index', [
            // publicaciones...
        ]);
    }


    /**
     * Vista formulario de creación de publicación
     */
    public function create(): Response {
        return Inertia::render('publicaciones/create');
    }

    /**
     * Crear publicación
     */
    public function store(Request $request): RedirectResponse {
        // ...
    }

    
    /**
     * Mostrar una publicación y sus comentarios
     */
    public function show(Request $request): Response {
        return Inertia::render('publicaciones/show', [
            // 'publicacion' => $request,
        ]);
    }


    /**
     * Eliminar publicación
     * (modal de confirmación)
     */
    // public function destroy(Publicacion $publicacion): RedirectResponse {
    //     // ...
    // }
    public function destroy(Request $request): Response {
        //
    }

    
    /**
     * Reacción en la publicación "me sirvió"
     */
    // public function toggleReaccion(Publicacion $publicacion): RedirectResponse {
    //     // ...
    // }
    public function toggleReaccion(Request $request): Response {
        // 
    }

}
