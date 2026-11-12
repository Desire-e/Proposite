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
     * Cada uno de 3 opositores aleatorios hace 3 tests aleatorios
     * de su oposición: 9 intentos en total.
     */
    public function run(): void
    {
        // Tests con preguntas, agrupados por la oposición de su tema
        $testsPorOposicion = Test::with('temario:id,oposicion_id')
            ->has('preguntas')
            ->withCount('preguntas')
            ->get()
            ->groupBy(fn (Test $test) => $test->temario?->oposicion_id)
            ->filter(fn ($tests, $oposicionId) => $oposicionId && $tests->count() >= 3);

        // Primera oposición (en orden aleatorio) con al menos 3 tests y 3 opositores
        $oposicionId = $testsPorOposicion->keys()->shuffle()->first(
            fn ($id) => Usuario::where('oposicion_id', $id)
                ->where('rol', Rol::OPOSITOR)
                ->count() >= 3
        );

        if (! $oposicionId) {
            $this->command?->warn('Necesitas una oposición con al menos 3 tests y 3 opositores. Ejecuta antes TestsSeeder y crea los usuarios.');
            return;
        }

        $tests = $testsPorOposicion[$oposicionId]->random(3);

        $usuarios = Usuario::where('oposicion_id', $oposicionId)
            ->where('rol', Rol::OPOSITOR)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $creados = 0;

        foreach ($usuarios as $usuario) {
            foreach ($tests as $test) {
                // Evita duplicados al relanzar el seeder: un intento por usuario y test
                $existe = IntentoTest::where('usuario_id', $usuario->id)
                    ->where('test_id', $test->id)
                    ->exists();

                if ($existe) {
                    continue;
                }

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

        $this->command?->info("Seeder de intentos completado: {$creados} intentos creados.");
    }
}
