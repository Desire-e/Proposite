<?php

namespace Database\Seeders;

use App\Enums\EstadoReporte;
use App\Enums\Rol;
use App\Models\Comentario;
use App\Models\Oposicion;
use App\Models\Reporte;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class BaneoSeeder extends Seeder
{
    /**
     * Un opositor baneado por oposición, para poder enseñar cómo se revoca
     * el baneo (RF16).
     *
     * Se banea al opositor con más reportes recibidos (el «conflictivo» de
     * ReporteSeeder), nunca una cuenta de demo. Se hace como en la app:
     * deleted_by con el administrador y delete(), cuyo evento da de baja
     * sus publicaciones, comentarios e intentos. Los reportes pendientes
     * sobre él o su contenido quedan resueltos por el administrador.
     */
    public function run(): void
    {
        $admin = Usuario::where('rol', Rol::ADMIN)->first();

        if (!$admin) {
            $this->command?->warn('No hay administrador: no se banea a nadie. Ejecuta antes UsuarioSeeder.');
            return;
        }

        $cuentasDemo = array_column(CuentasDemoSeeder::CUENTAS, 'email');
        $baneados = 0;

        foreach (Oposicion::all() as $oposicion) {
            // Al relanzar el seeder, no se banea a otro si ya hay un baneado
            if (Usuario::onlyTrashed()->where('oposicion_id', $oposicion->id)->exists()) {
                continue;
            }

            $usuario = Usuario::where('oposicion_id', $oposicion->id)
                ->where('rol', Rol::OPOSITOR)
                ->whereNotIn('email', $cuentasDemo)
                ->withCount('reportesRecibidos')
                ->orderByDesc('reportes_recibidos_count')
                ->first();

            if (!$usuario) {
                continue;
            }

            // Ids de lo que se da de baja con el baneo, antes de hacerlo: el usuario,
            // sus publicaciones, sus comentarios y los comentarios de otros en sus publicaciones
            $publicaciones = $usuario->publicaciones()->pluck('id');
            $reportados = [
                'usuario' => [$usuario->id],
                'publicacion' => $publicaciones->all(),
                'comentario' => Comentario::where('usuario_id', $usuario->id)
                    ->orWhereIn('publicacion_id', $publicaciones)
                    ->pluck('id')->all(),
            ];

            // deleted_by no es asignable en masa
            $usuario->forceFill(['deleted_by' => $admin->id])->save();
            $usuario->delete();

            foreach ($reportados as $tipo => $ids) {
                Reporte::where('reportable_tipo', $tipo)
                    ->whereIn('reportable_id', $ids)
                    ->where('estado', EstadoReporte::PENDIENTE)
                    ->update(['estado' => EstadoReporte::RESUELTO, 'resuelto_por' => $admin->id]);
            }

            $baneados++;
        }

        $this->command?->info("Seeder de baneos completado: {$baneados} opositores baneados.");
    }
}
