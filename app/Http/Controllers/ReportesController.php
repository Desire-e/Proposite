<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Comentario;
use App\Models\Usuario;
use App\Models\Reporte;
use Illuminate\Support\Facades\Gate;

use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreReporteRequest;


class ReportesController extends Controller
{
    /**
     * Reportar publicacion
     */
    public function storePublicacion(StoreReporteRequest $request, Publicacion $publicacion): RedirectResponse
    {
        Gate::authorize('report', [Reporte::class, $publicacion]);

        return $this->reportar($request, $publicacion);
    }

    /**
     * Reporte usuario
     */
    public function storeUsuario(StoreReporteRequest $request, Usuario $usuario): RedirectResponse
    {        
        Gate::authorize('report', [Reporte::class, $usuario]);

        return $this->reportar($request, $usuario);
    }

    /**
     * Reporte comentario
     */
    public function storeComentario(StoreReporteRequest $request, Comentario $comentario): RedirectResponse
    {        
        Gate::authorize('report', [Reporte::class, $comentario]);

        return $this->reportar($request, $comentario);
    }



    /**
     * Función auxiliar.
     * No se crea un service porque trabaja con 
     * HTTP directamente (recibe petición y redirige)
     * Un service trabaja con el dominio - documentar
     */
    private function reportar(StoreReporteRequest $request, Publicacion|Usuario|Comentario $reportable): RedirectResponse
    {
        $usuario = $request->user();

        // Si ya lo reportó, no se crea otro; se responde como si hubiera ido bien
        $yaReportado = $reportable->reportesRecibidos()
            ->where('usuario_reporta_id', $usuario->id)
            ->exists();

        if (!$yaReportado) {
            $reportable->reportesRecibidos()->create([
                'usuario_reporta_id' => $usuario->id,
                'motivo' => $request->validated()['motivo'],
            ]);
        }

        return back()->with('aviso', [
            'tipo' => 'success',
            'mensaje' => 'Gracias, hemos recibido tu reporte.',
        ]);
    }


}
