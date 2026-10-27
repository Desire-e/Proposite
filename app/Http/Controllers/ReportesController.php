<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Comentario;
use App\Models\Usuario;
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
        Gate::authorize('report', $publicacion);

        return $this->reportar($request, $publicacion);
    }

    /**
     * Reporte usuario
     */
    public function storeUsuario(StoreReporteRequest $request, Usuario $usuario): RedirectResponse
    {        
        Gate::authorize('report', $usuario);

        return $this->reportar($request, $usuario);
    }


    /**
     * TODO. REPORTAR COMENTARIO
     */

    /**
     * Función auxiliar.
     * No se crea un service porque trabaja con 
     * HTTP directamente (recibe petición y redirige)
     * Un service trabaja con el dominio - documentar
     */
    private function reportar(StoreReporteRequest $request, Publicacion|Usuario|Comentario $reportable): RedirectResponse
    {
        $reportable->reportesRecibidos()->create([
            'usuario_reporta_id' => $request->user()->id,
            'motivo' => $request->validated()['motivo'],
        ]);

        return back()->with('aviso', [
            'tipo' => 'success',
            'mensaje' => 'Gracias, hemos recibido tu reporte.',
        ]);
    }
}
