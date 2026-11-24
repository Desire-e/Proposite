<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Reporte;
use App\Models\Usuario;
use App\Models\Publicacion;
use App\Models\Comentario;

use App\Enums\EstadoReporte;
use App\Enums\Reportable;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;


// Que puede moderar la oposición seleccionada.
// Que el elemento del reporte pertenece a esa oposición.
// Para comentarios, que se comprueba la comunidad a través de su publicación, si ese es tu esquema.

class ReportesController extends Controller
{
    /**
     * Omitir un reporte
     */
    public function omitir(Request $request, Reporte $reporte): RedirectResponse 
    {
        Gate::authorize('solve', $reporte);

        $reporte->estado = EstadoReporte::RESUELTO;
        $reporte->resuelto_por = $request->user()->id;
        $reporte->save();

        return back();
    }
    

    /**
     * Banear un usuario reportado
     */
    public function banearUsuario(Request $request, Reporte $reporte): RedirectResponse
    {
        Gate::authorize('solve', $reporte);

        $usuarioId = null;

        // Banear usuario reportado
        if ($reporte->reportable_tipo === Reportable::USUARIO->value) {
            $usuarioId = $reporte->reportable_id;
        }
        // Banear autor de contenido reportado (incluso si el contenido fue eliminado)
        else if ($reporte->reportable_tipo === Reportable::PUBLICACION->value) {
            $usuarioId = Publicacion::withTrashed()
                ->find($reporte->reportable_id)?->usuario_id;
        }
        else if ($reporte->reportable_tipo === Reportable::COMENTARIO->value) {
            $usuarioId = Comentario::withTrashed()
                ->find($reporte->reportable_id)?->usuario_id;
        }

        $usuario = $usuarioId !== null 
            ? Usuario::find($usuarioId) 
            : null;

        if ($usuario !== null) {
            $usuario->deleted_by = $request->user()->id;
            $usuario->save();
            $usuario->delete();
        } 

        
        // Resolver reporte
        $reporte->estado = EstadoReporte::RESUELTO;
        $reporte->resuelto_por = $request->user()->id;
        $reporte->save();

        
        return back();
    }


    /**
     * Eliminar contenido reportado
     */
    public function eliminarContenido(Request $request, Reporte $reporte): RedirectResponse
    {
        Gate::authorize('solve', $reporte);


        if ($reporte->reportable_tipo === Reportable::USUARIO->value) {
            // Usuarios se banean
            return back();
        }
        else if ($reporte->reportable_tipo === Reportable::PUBLICACION->value) {
            // Resolver reportable publicación
            $publicacion = Publicacion::find($reporte->reportable_id);
            if ($publicacion !== null) $publicacion->delete();
        }
        else if ($reporte->reportable_tipo === Reportable::COMENTARIO->value) {
            // Resolver reportable comentario
            $comentario = Comentario::find($reporte->reportable_id);
            if ($comentario !== null) $comentario->delete();
        }
        else { return back(); }
        

        // Resolver reporte
        $reporte->estado = EstadoReporte::RESUELTO;
        $reporte->resuelto_por = $request->user()->id;
        $reporte->save();

        
        return back();
    }
}
