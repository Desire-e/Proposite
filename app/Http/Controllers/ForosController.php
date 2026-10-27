<?php

namespace App\Http\Controllers;

use App\Enums\Rol;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ForosController extends Controller
{
    /**
     * Carga los datos del foro
     */

    public function index(Request $request): Response
    {
        $user = $request->user();
        
        $oposicion = $user->oposicion;

        // TODO.  Usa paginate()
        
        $publicaciones = $oposicion->publicaciones()
            ->with('usuario:id,name')
            ->withCount(['comentarios', 'meSirvio'])
            // Subconsulta por fila, evita N+1. No se lanza una consulta aparte por cada publicación
            ->withExists([ 
                // añade una condición extra a la subconsulta
                'meSirvio as me_sirvio_activo' => fn ($q) => $q->where('usuarios.id', $user->id),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('foro/index', [
            'oposicion' => $oposicion,
            'publicaciones' => $publicaciones,
            'miembros' => $oposicion->usuarios()
                ->where('rol', Rol::OPOSITOR)->count(),
        ]);
    }
    
}
