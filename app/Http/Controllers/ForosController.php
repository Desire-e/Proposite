<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\Publicacion;
use App\Models\Comentario;
use App\Enums\Rol;


use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class ForosController extends Controller
{
    /**
     * Carga los datos del foro
     */

    public function index(): Response
    {
        $user = Auth::user();
        $oposicion = $user->oposicion;
        $publicaciones = $oposicion->publicaciones()
            ->with('usuario')
            ->withCount(['comentarios', 'meSirvio'])
            // Subconsulta por fila, evita N+1. No se lanza una consulta aparte por cada publicación
            ->withExists([ 
                // añade una condición extra a la subconsulta
                'meSirvio as me_sirvio_activo' => fn ($q) => $q->where('usuarios.id', $user->id),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('foro/feed', [
            'oposicion' => $oposicion,
            'publicaciones' => $publicaciones ?? [],
            'miembros' => $oposicion->usuarios
                ->where('rol', Rol::OPOSITOR)->count(),
        ]);
    }


    /**
     * Cambiar "me sirvió" / like
     */
    
    public function toggleMeSirvio(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $user = $request->user();

        if ($publicacion->oposicion_id !== $user->oposicion_id) {
            return back()->with('aviso', [
                'tipo' => 'warning',
                'mensaje' => 'No puedes interactuar con publicaciones de otra oposición.',
            ]);
        }

        // toggle() es un método de relaciones belongsToMany que invierte el 
        // estado de la relación entre dos modelos en la tabla pivote. 
        // Si el ID ya está relacionado, lo desrelaciona (borra fila). 
        // Si no, lo relaciona (inserta fila) 
        $publicacion->meSirvio()->toggle($user->id);

        return back();
    }


    /**
     * Publicar
     */

    public function insertPublicacion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contenido' => ['required', 'string', ],
        ],
        ['contenido.required' => 'Debe escribir antes de publicar.']
        );
        
        $user = $request->user(); 
        
        Publicacion::create([
            'usuario_id' => $user->id,
            'oposicion_id' => $user->oposicion_id,
            'contenido' => $validated['contenido'],
        ]);

        return back();
    }

    public function deletePublicacion(Request $request, Publicacion $publicacion): RedirectResponse
    {
        if ($publicacion->usuario_id !== $request->user()->id) {
            return back()->with('aviso', [
                'tipo' => 'warning',
                'mensaje' => 'Solo puedes eliminar tus propias publicaciones.',
            ]);
        }

        $publicacion->delete(); // soft delete: rellena deleted_at

        return back();
    }

    public function reportPublicacion(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $user = $request->user(); 
        
        if ($publicacion->usuario_id === $user->id) {
            return back()->with('aviso', [
                'tipo' => 'warning',
                'mensaje' => 'Solo puedes reportar publicaciones que no sean las tuyas.',
            ]);
        }

        if ($publicacion->oposicion_id !== $user->oposicion_id) {
            return back()->with('aviso', [
                'tipo' => 'warning',
                'mensaje' => 'No puedes reportar publicaciones de otra oposición.',
            ]);
        }

        $yaReportada = $publicacion->reportesRecibidos()->where('usuario_reporta_id', $user->id)->exists();

        if ($yaReportada) {
            return back()->with('aviso', [
                'tipo' => 'warning',
                'mensaje' => 'Ya habías reportado esta publicación.',
            ]);
        }
        
        $validated = $request->validate(
            ['motivo' => ['required', 'string', 'max:1000']],
            ['motivo.required' => 'Indica el motivo del reporte.']
        );

        $publicacion->reportesRecibidos()->create([
            'usuario_reporta_id' => $user->id,
            'motivo' => $validated['motivo'],
        ]);

        return back()->with('aviso', [
            'tipo' => 'success',
            'mensaje' => 'Gracias, hemos recibido tu reporte.',
        ]);
    }


    /**
     * Mostrar una publicacion con sus comentarios
     */
    public function show($publicacionId): Response
    {
        // 
    }
    

    /**
     * Comentar
     */
    public function insertComentario(Request $request): RedirectResponse
    {
        // 
    }


}
