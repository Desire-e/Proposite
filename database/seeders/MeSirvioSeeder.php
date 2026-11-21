<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Publicacion;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeSirvioSeeder extends Seeder
{
    /**
     * «Me sirvió» aleatorios: cada publicación recibe el de un 0-60 % de los
     * opositores de su oposición. Nunca de su autor ni de un administrador.
     */
    public function run(): void
    {
        // Al relanzar el seeder no se añaden más: se conservan los que ya hay
        if (DB::table('me_sirvio')->exists()) {
            $this->command?->info('Ya existen «me sirvió»: no se crean más.');
            return;
        }

        $opositoresPorOposicion = Usuario::where('rol', Rol::OPOSITOR)->get()->groupBy('oposicion_id');
        $filas = [];

        foreach (Publicacion::all() as $publicacion) {
            $candidatos = ($opositoresPorOposicion[$publicacion->oposicion_id] ?? collect())
                ->where('id', '!=', $publicacion->usuario_id);

            $cuantos = (int) round($candidatos->count() * random_int(0, 60) / 100);

            if ($cuantos === 0) {
                continue;
            }

            foreach ($candidatos->random($cuantos) as $usuario) {
                $filas[] = ['usuario_id' => $usuario->id, 'publicacion_id' => $publicacion->id];
            }
        }

        // La clave primaria (usuario_id, publicacion_id) evita duplicados al relanzar el seeder
        $creados = DB::table('me_sirvio')->insertOrIgnore($filas);

        $this->command?->info("Seeder de «me sirvió» completado: {$creados} creados.");
    }
}
