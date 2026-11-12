<?php

namespace App\Services;

use App\Models\IntentoTest;
use App\Models\Test;
use App\Models\Usuario;
use Illuminate\Support\Collection;

class TestsService
{
    private const NOTA_APROBADO = 5;

    /**
     * Datos de la página de tests para el usuario actual.
     */
    public function indexData(Usuario $user): array
    {
        $tests = $this->testsDeOposicion($user);

        // Último intento de cada usuario de la oposición en cada test
        $ultimosIntentos = $this->ultimosIntentos($tests);

        // De esos intentos, los del usuario actual
        $intentosUsuario = $ultimosIntentos->where('usuario_id', $user->id);

        // [test_id => intento], para no buscar en la colección en cada vuelta
        $intentosPorTest = $intentosUsuario->keyBy('test_id');

        return [
            'usuario' => [
                'oposicion' => $user->oposicion->nombre,
                'numero_tests_realizados' => $intentosUsuario->count(),
                'porcentaje_tests_aprobados' => 
                    $this->porcentajeAprobados($intentosUsuario, $tests->count()),
                'ranking' => 
                    $this->posicionRanking($user, $ultimosIntentos),
            ],
            
            // map() recorre cada test y lo sustituye por el array 
            // formateado, dando una Collection   
            'tests' => $tests->map(fn ($test) => [
                'id' => $test->id,
                'titulo' => $test->titulo,
                'descripcion' => $test->descripcion,
                'numero_tema' => $test->temario->numero_tema,
                'preguntas_count' => $test->preguntas_count,
                'intentos_comunidad_count' => $test->intentos_test_count,

                'ultima_nota' => isset($intentosPorTest[$test->id])
                    ? round($intentosPorTest[$test->id]->nota, 2)
                    : null,
            ])->all() // all() convierte colecciones en un array normal
        ];
    }

    /**
     * Datos de la página de un tests.
     */
    public function showData(Usuario $user, Test $test): array
    {
        $mejorNota = $test->intentosTest()
            ->where('usuario_id', $user->id)
            ->max('nota');

        $ultimaNota = $test->intentosTest()
            ->where('usuario_id', $user->id)
            ->orderByDesc('created_at')
            ->value('nota');

        return [
            'test' => [
                'id' => $test->id,
                'titulo' => $test->titulo,
                'descripcion' => $test->descripcion,
                'numero_tema' => $test->temario->numero_tema,
                'preguntas_count' => $test->preguntas()->count(),
                'intentos_comunidad_count' => $test->intentosTest()
                    ->distinct()->count('usuario_id'),
            ],
            'usuario' => [
                'mejor_nota' => $mejorNota ? round($mejorNota, 2): null,
                'ultima_nota' => $ultimaNota ? round($ultimaNota, 2) : null
            ],
        ];
    }



    // ====================
    // Consultas
    // ====================

    /**
     * Tests cuyo temario pertenece a la oposición del @param Usuario,
     * 
     * @return Collection con todos los datos de cada test, 
     * id y título del tema y preguntas_count e intentosTest_count.
     */
    private function testsDeOposicion(Usuario $user): Collection
    {
        return Test::query()
            ->whereHas('temario', fn ($q) => $q->where('oposicion_id', $user->oposicion_id))
            ->with('temario:id,titulo')
            ->withCount(['preguntas', 'intentosTest'])
            ->get();
    }

    /**
     * Último intento de cada usuario en cada uno de los tests.
     *
     * @return Collection<int, IntentoTest> plana (usuario_id, test_id, nota)
     */
    private function ultimosIntentos(Collection $tests): Collection
    {
        return IntentoTest::query()
            ->whereIn('test_id', $tests->modelKeys())
            ->orderByDesc('created_at')
            ->get(['usuario_id', 'test_id', 'nota'])
            // no compara los intentos enteros, compara solo la cadena con usuario y test
            ->unique(fn ($intento) => "{$intento->usuario_id}-{$intento->test_id}");
    }


    // ====================
    // Cálculos
    // ====================

    /**
     * @return float Porcentaje de @param intentosUsuario aprobados
     * del usuario, sobre el @param totalTests disponibles.
     */
    private function porcentajeAprobados(Collection $intentosUsuario, int $totalTests): float
    {
        // Si no hay tests
        if ($totalTests === 0) return 0;

        $aprobados = $intentosUsuario->where('nota', '>=', self::NOTA_APROBADO)->count();

        return round($aprobados / $totalTests * 100, 1);
    }

   /**
     * Posición del @param Usuario en el ranking de la oposición.
     *
     * Se ordena por la suma de notas: equivale a ordenar por la media,
     * ya que todos se dividen entre el mismo total de tests
     * (los tests no realizados cuentan como 0).
     */
    private function posicionRanking(Usuario $user, Collection $ultimosIntentos): int
    {
        // [usuario_id => suma de notas]
        $sumas = $ultimosIntentos
            ->groupBy('usuario_id')
            ->map(fn ($intentos) => $intentos->sum('nota'));

        $miSuma = $sumas->get($user->id, 0);

        // Los usuarios sin intentos suman 0 y nunca quedan por delante,
        // así que no hace falta cargarlos. Los empates comparten posición.
        return $sumas->filter(fn ($suma) => $suma > $miSuma)->count() + 1;
    }

}