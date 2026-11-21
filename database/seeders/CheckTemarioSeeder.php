<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Tema;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckTemarioSeeder extends Seeder
{
    /**
     * Temas marcados como estudiados: cada opositor marca entre el 0 y el
     * 70 % de los temas de su oposición. Los administradores no marcan temas.
     */
    public function run(): void
    {
        // Al relanzar el seeder no se cambia el progreso de nadie
        if (DB::table('checks_temario')->exists()) {
            $this->command?->info('Ya existen temas marcados: no se crean más.');
            return;
        }

        $temasPorOposicion = Tema::all()->groupBy('oposicion_id');
        $creados = 0;

        foreach (Usuario::where('rol', Rol::OPOSITOR)->get() as $usuario) {
            $creados += $this->marcar($usuario, $temasPorOposicion[$usuario->oposicion_id] ?? collect(), random_int(0, 70));
        }

        $this->command?->info("Seeder de temas marcados completado: {$creados} marcas creadas.");
    }

    /**
     * Marca como estudiado un porcentaje de los temas, al azar.
     * También lo usa CuentasDemoSeeder.
     *
     * @return int marcas creadas
     */
    public function marcar(Usuario $usuario, $temas, int $porcentaje): int
    {
        $cuantos = (int) round($temas->count() * $porcentaje / 100);

        if ($cuantos === 0) {
            return 0;
        }

        $filas = $temas->random($cuantos)->map(fn (Tema $tema) => [
            'usuario_id' => $usuario->id,
            'temario_id' => $tema->id,
            'checked' => true,
        ])->all();

        return DB::table('checks_temario')->insertOrIgnore($filas);
    }
}
