<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\IntentoTest;
use App\Models\Test;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class IntentosTestSeeder extends Seeder
{
    /**
     * Intentos aleatorios: cada opositor hace (o no) algunos tests de su
     * oposición, una o dos veces, con notas aleatorias. No depende de
     * ningún usuario concreto.
     */
    public function run(): void
    {
        // Tests con preguntas, agrupados por la oposición de su tema
        $testsPorOposicion = Test::with('temario:id,oposicion_id')
            ->has('preguntas')
            ->withCount('preguntas')
            ->get()
            ->groupBy(fn (Test $test) => $test->temario?->oposicion_id);

        if ($testsPorOposicion->isEmpty()) {
            $this->command?->warn('No hay tests con preguntas. Ejecuta antes TestsSeeder.');
            return;
        }

        $opositores = Usuario::where('rol', Rol::OPOSITOR)
            ->whereNotNull('oposicion_id')
            ->get();

        $creados = 0;

        foreach ($opositores as $usuario) {
            $tests = $testsPorOposicion->get($usuario->oposicion_id);

            // Algunos opositores no han hecho ningún test todavía (1 de cada 4, aprox.)
            if (! $tests || random_int(1, 4) === 1) {
                continue;
            }

            // Subconjunto aleatorio de los tests de su oposición
            foreach ($tests->random(random_int(1, $tests->count())) as $test) {
                // Evita duplicados al relanzar el seeder
                if (IntentoTest::where('usuario_id', $usuario->id)->where('test_id', $test->id)->exists()) {
                    continue;
                }

                // Uno o dos intentos del mismo test
                $intentos = random_int(1, 2);

                for ($i = 0; $i < $intentos; $i++) {
                    // Nota coherente con el test: aciertos / preguntas * 10
                    $aciertos = random_int(0, $test->preguntas_count);
                    $nota = round($aciertos / $test->preguntas_count * 10, 3);

                    // created_at no es fillable: forceFill para fechar el intento en el pasado
                    (new IntentoTest)->forceFill([
                        'usuario_id' => $usuario->id,
                        'test_id' => $test->id,
                        'nota' => $nota,
                        'created_at' => now()->subDays(random_int(1, 30))->subMinutes(random_int(0, 1439)),
                    ])->save();

                    $creados++;
                }
            }
        }

        $this->command?->info("Seeder de intentos completado: {$creados} intentos creados.");
    }
}
