<?php

namespace App\Services\Admin;

use App\Models\Oposicion;
use App\Models\Recurso;
use App\Models\Test;

use Illuminate\Support\Collection;

class PanelService
{
    public function panelData(Oposicion $oposicion): array 
    { 
        $reportesPendientes = $this->reportesPendientes($oposicion);

        // Recursos cuyo tema (relación temario) pertenece a la oposición moderada.
        // whereHas filtra por una columna de la tabla relacionada (temarios)
        $recursosCount = Recurso::whereHas('temario', 
            function ($query) use ($oposicion) {
                    $query->where('oposicion_id', $oposicion->id);
            })->count();

        $testsCount = Test::whereHas('temario', 
            function ($query) use ($oposicion) {
                    $query->where('oposicion_id', $oposicion->id);
            })->count();
        
        $comunidades = $this->comunidadesDisponibles();



        return [
            'oposicion_moderada'=> [
                'id' => $oposicion->id,
                'nombre'=> $oposicion->nombre,
            ],

            'reportes_pendientes' => $reportesPendientes['reportesCollect'],
            'reportes_count' => $reportesPendientes['reportesCount'],

            'recursos_count' => $recursosCount,
            'tests_count' => $testsCount,
            'usuarios_count' => $oposicion->usuarios()->count(),

            // Datos de otras comunidades (cambiar de comunidad)
            'comunidades' => $this->comunidadesDisponibles(),
        ];
    }
    

    /**
     * Reportes de la comunidad y numero de reportes
     */
    private function reportesPendientes(Oposicion $oposicion): array 
    {
        $reportes = $oposicion->reportesPendientes();

        // unir en una colección ordenada por creación
        $reportesCollect = collect()
            ->merge($reportes['usuarios'])
            ->merge($reportes['publicaciones'])
            ->merge($reportes['comentarios'])
            ->sortByDesc('created_at')
            ->values();

        return [
            'reportesCollect' => $reportesCollect,
            'reportesCount' => $reportesCollect->count(),
        ];
    }


    /**
     * Comunidades disponibles para moderar
     */
    public function comunidadesDisponibles(): Collection 
    {
        return Oposicion::query()->get()->map(fn ($op) => [
                'id'=> $op->id,
                'nombre'=> $op->nombre,
                'usuarios_count'=> $op->usuarios()->count(),
                'reportes_count'=> $this->reportesPendientes($op)['reportesCount'],
        ]);
    }
}