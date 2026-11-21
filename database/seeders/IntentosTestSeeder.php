<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\IntentoTest;
use App\Models\Test;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\FechasDemo;
use Illuminate\Database\Seeder;

class IntentosTestSeeder extends Seeder
{
    use FechasDemo;

    /**
     * Intentos aleatorios. No depende de ningún usuario concreto.
     *
     * - Solo opositores (los intentos de los administradores no se guardan)
     *   y solo tests de su propia oposición.
     * - 3 de cada 4 opositores han hecho algún test; cada uno, de 1 a 3 veces.
     * - Notas coherentes con el test (aciertos / preguntas * 10), la mayoría
     *   entre 4 y 8, y algo mejores en cada nuevo intento.
     * - Cada intento, después del alta del usuario, de la creación del test
     *   y del intento anterior.
     */
    public function run(): void
    {
        // Al relanzar el seeder no se añaden más: se conservan los que ya hay
        if (IntentoTest::withTrashed()->exists()) {
            $this->command?->info('Ya existen intentos: no se crean más.');
            return;
        }

        $creados = 0;

        foreach (Usuario::where('rol', Rol::OPOSITOR)->whereNotNull('oposicion_id')->get() as $usuario) {
            // 1 de cada 4 opositores no ha hecho ningún test todavía
            if (random_int(1, 4) === 1) {
                continue;
            }

            $creados += $this->intentosDe($usuario);
        }

        $this->command?->info("Seeder de intentos completado: {$creados} intentos creados.");
    }

    /**
     * Crea intentos de un opositor en un subconjunto aleatorio de los tests
     * de su oposición. También lo usa CuentasDemoSeeder.
     *
     * @return int intentos creados
     */
    public function intentosDe(Usuario $usuario, ?int $numTests = null): int
    {
        $tests = Test::whereHas('temario', fn ($q) => $q->where('oposicion_id', $usuario->oposicion_id))
            ->has('preguntas')
            ->withCount('preguntas')
            ->get()
            // Evita duplicados al relanzar el seeder: solo los tests que aún no ha hecho
            ->reject(fn (Test $test) => IntentoTest::withTrashed()
                ->where('usuario_id', $usuario->id)->where('test_id', $test->id)->exists());

        if ($tests->isEmpty()) {
            return 0;
        }

        $numTests = min($numTests ?? random_int(1, $tests->count()), $tests->count());
        $creados = 0;

        foreach ($tests->random($numTests) as $test) {
            $fecha = CarbonImmutable::instance($usuario->created_at)->max($test->created_at);
            // Nivel del opositor en este test: proporción de aciertos esperada
            $nivel = $this->nivel();

            for ($i = 0, $intentos = random_int(1, 3); $i < $intentos; $i++) {
                $fecha = $this->fechaEntre($fecha->addHour(), now()->subHours(($intentos - $i) * 6));

                $aciertos = (int) round($test->preguntas_count * min(1, $nivel + $i * 0.1));

                // created_at no es asignable en masa: forceFill para fechar el intento en el pasado
                (new IntentoTest)->forceFill([
                    'usuario_id' => $usuario->id,
                    'test_id' => $test->id,
                    'nota' => round($aciertos / $test->preguntas_count * 10, 3),
                    'created_at' => $fecha,
                ])->save();

                $creados++;
            }
        }

        return $creados;
    }

    /**
     * Proporción de aciertos entre 0,2 y 1. La media de tres valores
     * aleatorios concentra las notas en torno a 6.
     */
    private function nivel(): float
    {
        $media = (random_int(0, 100) + random_int(0, 100) + random_int(0, 100)) / 300;

        return 0.2 + $media * 0.8;
    }
}
