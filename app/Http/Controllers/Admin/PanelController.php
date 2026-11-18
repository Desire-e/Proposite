<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;


use App\Models\Oposicion;
use App\Models\Recurso;
use App\Models\Test;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;


class PanelController extends Controller
{

    /**
     * Panel de selección de oposición a moderar
     */
    public function welcome(Request $request): Response 
    {
        return Inertia::render('admin/welcome', [
            'comunidades'=> Oposicion::query()->get()->map(fn ($op) => [
                'id'=> $op->id,
                'nombre'=> $op->nombre,
                'usuarios_count'=> $op->usuarios()->count(),
                'reportes_count'=> $this->contarReportes($op),
            ]),
            'admin'=> [
                'id'=> $request->user()->id,
                'name'=> $request->user()->name
            ]
        ]);
    }
    
    // SERVICE
    /**
     * Número total de reportes pendientes de una comunidad
     */
    private function contarReportes(Oposicion $oposicion): int
    {
        $reportes = $oposicion->reportesPendientes();

        return $reportes['usuarios']->count()
            + $reportes['publicaciones']->count()
            + $reportes['comentarios']->count();
    }


    /**
     * Panel de administración
     */
    public function index(Request $request): Response 
    {  
        $oposicionModerada = $request->user()->oposicionModerada;

        $reportesPendientes = $oposicionModerada->reportesPendientes();

        // unir en una colección ordenada por creación
        $reportes = collect()
            ->merge($reportesPendientes['usuarios'])
            ->merge($reportesPendientes['publicaciones'])
            ->merge($reportesPendientes['comentarios'])
            ->sortByDesc('created_at')
            ->values();

        return Inertia::render('admin/panel', [
            // --- ESTA COMUNIDAD

            'oposicion_moderada'=> [
                'id' => $oposicionModerada->id,
                'nombre'=> $oposicionModerada->nombre,
            ],

            'reportes_pendientes' => $reportes,

            'reportes_count' => $this->contarReportes($oposicionModerada),

            // Recursos cuyo tema (relación temario) pertenece a la oposición moderada.
            // whereHas filtra por una columna de la tabla relacionada (temarios)
            'recursos_count' => Recurso::whereHas('temario', 
                function ($query) use ($oposicionModerada) {
                    $query->where('oposicion_id', $oposicionModerada->id);
            })->count(),
            
            'tests_count' => Test::whereHas('temario', 
                function ($query) use ($oposicionModerada) {
                    $query->where('oposicion_id', $oposicionModerada->id);
            })->count(),

            'usuarios_count' => $oposicionModerada->usuarios->count(),

            // --- OTRAS COMUNIDADES

            'comunidades'=> Oposicion::query()->get()->map(fn ($op) => [
                    'id'=> $op->id,
                    'nombre'=> $op->nombre,
            ]),
        ]);
    }

    

    
    // TODO. Esto debe ocurrir en el mismo panel admin, donde puede 
    // seleccionar cambiar a moderar otra oposición
    // Para que el administrador pueda cambiar de comunidad desde el panel: 
    // basta con un enlace a admin.welcome.

    // if($request->user()->rol === Rol::ADMIN 
    // && $request->user()->oposicionModerada !== null) {
    //     $request->user()->oposicion_moderada_id = null;
    //     $request->user()->save();
    // }



    /**
     * Guarda la oposición elegida y entra al panel 
     * administrativo de su comunidad
     */
    public function selectOposicion(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'oposicion_id' => [
                    'required',
                    'uuid',
                    // Rule::exists consulta la tabla directamente, sin pasar por el 
                    // modelo Eloquent, así que no detecta el borrado lógico. 
                    // Para él, una oposición con deleted_at relleno sigue existiendo, 
                    // porque la fila está en la tabla.
                    Rule::exists('oposiciones', 'id')->withoutTrashed(),
                ],
            ],
            [],
            // nombre del campo en los mensajes de error
            ['oposicion_id' => 'oposición'] 
        );

        // añadir oposicion_moderada_id a $fillable de Usuario abre la puerta 
        // a que create() y update() también lo acepten. 
        // Es un campo de administrador, no debería llegar desde el registro ni 
        // desde la edición de perfil
        $admin = $request->user();
        $admin->oposicion_moderada_id = $validated['oposicion_id'];
        $admin->save();

        return to_route('admin.panel');
    }

}
