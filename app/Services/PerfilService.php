<?php

namespace App\Services;

use App\Models\Tema;
use App\Models\Usuario;
use Illuminate\Support\Collection;

class PerfilService 
{     

    /**
     * Recoge los datos a insertar en el perfil de @param Usuario 
     */
    public function datosPerfil(Usuario $user): array
    {
        // último intento de cada test que ha hecho el usuario
        $ultimosIntentos = $this->ultimosIntentosTest($user);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'oposicion' => $user->oposicion->only(['nombre', 'descripcion']),
            'biografia' => $user->biografia ?? null,
            'signed_up_at' => $user->created_at,
            'anio_convocatoria' => $user->fecha_convocatoria?->format('Y') ?? null,
            'progreso' => $this->progresoTemario($user),
            'estadisticas' => $this->estadisticasTests($ultimosIntentos),
            'tema_mas_fuerte' => $this->temaMasFuerte($ultimosIntentos),
            'actividad_reciente' => $this->actividadReciente($user),
        ];
    }


    /**
     * @return Collection del último intento de cada test que ha 
     * hecho el @param Usuario 
     */
    private function ultimosIntentosTest(Usuario $user): Collection
    {
        $ultimosIntentosTest = $user->intentosTests()
            // carga el test al que pertenece cada intento, y el temario asociado a ese test.    
            ->with('test.temario')
            // ordena intento de nuevo a viejo
            ->orderByDesc('created_at')->get() 
            // quita intentos en tests eliminados
            ->filter(fn ($intento) => $intento->test !== null)
            // se queda con el primero de cada test, el más nuevo
            ->unique('test_id');

        return $ultimosIntentosTest;  
    }


    /**
     * Obtiene @param Usuario y mide progreso de temas marcados estudiados
     * @return array de Temas estudiados, porcentaje de temas estudiados,
     * total de temas de la oposición,
     */
    private function progresoTemario(Usuario $user): array
    {
        $totalTemas = Tema::where('oposicion_id', $user->oposicion_id)->count();

        // Temas de esa oposición marcados como estudiados.
        $temasEstudiados = $user->checkedTemario() // obtiene temarios estudiados
            ->where('temarios.oposicion_id', $user->oposicion_id) // solo temas de su oposición
            ->count();
            
        $porcentaje = $totalTemas > 0 ? 
            (int) round($temasEstudiados / $totalTemas * 100) : 
            0;

        return [
            'total_temas' => $totalTemas,
            'temas_estudiados' => $temasEstudiados,
            'porcentaje' => $porcentaje,
        ];
    }


    /**
     * @param Collection de ultimosIntentosTest()
     * @return array de Tests realizados, tests aprobados, y nota media 
     * de todos los tests
     */
    private function estadisticasTests(Collection $ultimosIntentos): array
    {
        // cuenta cuantos tests hizo, contando solo el último intento de cada test
        $realizados = $ultimosIntentos->count();

        // contar cuantos fueron aprobados        
        $testsAprobados = $ultimosIntentos
            ->filter(fn ($intento) => (float) $intento->nota >= 5)
            ->count();
        
        // media de notas contando solo los ultimos intentos de cada test
        $notaMedia = $realizados > 0 
            ? round($ultimosIntentos->avg('nota'), 2)
            : 0;
        
        return [
            'tests_realizados' => $realizados,
            'tests_aprobados' => $testsAprobados,
            'nota_media' => $notaMedia,
        ];
    }


    /**
     * @param Collection de ultimosIntentosTest() del usuario
     * @return array de tema con mejor nota media 
     * @return null si no hay datos
     */
    private function temaMasFuerte(Collection $ultimosIntentos): ?array
    {
        $mejorNotaTema = $ultimosIntentos
            // quita intentos de tests asociados a temas eliminados 
            ->filter(fn ($intento) => $intento->test->temario !== null)
            // agrupa los intentos por temario
            ->groupBy(fn ($intento) => $intento->test->temario_id)
            // transforma cada grupo de intentos por tema, en un 
            // resumen del tema, generando un nuevo elemento con 3 datos
            ->map(fn ($grupo) => [ // intentos de un mismo tema
                'titulo' => 
                    $grupo->first()->test->temario->titulo, 
                'nota_media' => 
                    round($grupo->avg('nota'), 2), 
            ])
            ->sortByDesc('nota_media') // ordenar de mayor a menor nota media
            ->first(); // mejor nota
    
        return $mejorNotaTema;
    }


    /**
     * @return Collection de las 2 últimas publicaciones 
     * o comentarios del @param Usuario
     */
    private function actividadReciente(Usuario $user): Collection
    {
        $publicaciones = $user->publicaciones()
            ->latest()->take(2)->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'tipo' => 'publicacion',
                'texto' => $p->contenido,
                'created_at' => $p->created_at,
            ]);

        $comentarios = $user->comentarios()
            ->latest()->take(2)->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'tipo' => 'comentario',
                'texto' => $c->contenido,
                'created_at' => $c->created_at,
            ]);

        return $publicaciones->concat($comentarios)
            ->sortByDesc('created_at')->take(2)->values();
    }

}