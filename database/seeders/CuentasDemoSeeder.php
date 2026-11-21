<?php

namespace Database\Seeders;

use App\Models\Comentario;
use App\Models\IntentoTest;
use App\Models\Publicacion;
use App\Models\Reporte;
use App\Models\Tema;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\FechasDemo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuentasDemoSeeder extends Seeder
{
    use FechasDemo;

    /**
     * Cuentas de demo: una por oposición. Las crea UsuarioSeeder.
     *
     * Es el único seeder que depende de usuarios concretos (excepción aprobada
     * en PEND-27): garantiza que estas cuentas tengan progreso que enseñar
     * en la demostración, porque con datos aleatorios podría no haberlo.
     */
    public const CUENTAS = [
        'Auxiliar Administrativo del Estado' => [
            'name' => 'Lucía Romero',
            'email' => 'lucia.romero@example.com',
            'biografia' => 'Preparo la oposición de Auxiliar Administrativo por las tardes, después del trabajo. Me organizo por bloques y hago test cada semana.',
        ],
        'Policía Nacional (Escala Básica)' => [
            'name' => 'Daniel Ortega',
            'email' => 'daniel.ortega@example.com',
            'biografia' => 'Entreno por las mañanas y estudio por las tardes. Primera convocatoria a la Escala Básica: voy tema a tema y con muchos test.',
        ],
    ];

    // Mínimos que se garantizan a cada cuenta de demo
    private const MIN_TESTS = 4;
    private const PORCENTAJE_TEMARIO = 40;
    private const MIN_COMENTARIOS = 2;

    public function run(): void
    {
        foreach (self::CUENTAS as $cuenta) {
            $usuario = Usuario::where('email', $cuenta['email'])->first();

            if (!$usuario) {
                $this->command?->warn("No existe la cuenta de demo {$cuenta['email']}. Ejecuta antes UsuarioSeeder.");
                continue;
            }

            $this->garantizarIntentos($usuario);
            $this->garantizarTemario($usuario);
            $this->garantizarPublicacion($usuario);
            $this->garantizarComentarios($usuario);
            $this->garantizarReporte($usuario);
        }

        $this->command?->info('Seeder de cuentas de demo completado.');
    }

    /** Intentos en al menos MIN_TESTS tests de su oposición. */
    private function garantizarIntentos(Usuario $usuario): void
    {
        $hechos = IntentoTest::where('usuario_id', $usuario->id)->distinct()->count('test_id');

        if ($hechos < self::MIN_TESTS) {
            (new IntentosTestSeeder)->intentosDe($usuario, self::MIN_TESTS - $hechos);
        }
    }

    /** Al menos un 40 % del temario marcado como estudiado. */
    private function garantizarTemario(Usuario $usuario): void
    {
        $temas = Tema::where('oposicion_id', $usuario->oposicion_id)->get();
        $marcados = DB::table('checks_temario')->where('usuario_id', $usuario->id)->pluck('temario_id');

        // Solo los que faltan hasta el mínimo, entre los temas aún sin marcar
        $faltan = (int) ceil($temas->count() * self::PORCENTAJE_TEMARIO / 100)
            - DB::table('checks_temario')->where('usuario_id', $usuario->id)->where('checked', true)->count();

        if ($faltan > 0) {
            $libres = $temas->whereNotIn('id', $marcados);
            (new CheckTemarioSeeder)->marcar($usuario, $libres, (int) ceil($faltan * 100 / max($libres->count(), 1)));
        }
    }

    /**
     * Al menos una publicación propia. Si no tiene, pasa a ser autora de
     * una publicación de su oposición en la que no haya comentado ni
     * reportado, y se quita su «me sirvió» de ella.
     */
    private function garantizarPublicacion(Usuario $usuario): void
    {
        if ($usuario->publicaciones()->exists()) {
            return;
        }

        $publicacion = Publicacion::where('oposicion_id', $usuario->oposicion_id)
            ->whereDoesntHave('comentarios', fn ($q) => $q->where('usuario_id', $usuario->id))
            ->whereNotIn('id', Reporte::where('usuario_reporta_id', $usuario->id)->where('reportable_tipo', 'publicacion')->pluck('reportable_id'))
            ->inRandomOrder()
            ->first();

        if (!$publicacion) {
            return;
        }

        $publicacion->forceFill(['usuario_id' => $usuario->id])->save();
        DB::table('me_sirvio')->where('publicacion_id', $publicacion->id)->where('usuario_id', $usuario->id)->delete();
    }

    /** Al menos MIN_COMENTARIOS comentarios en publicaciones ajenas. */
    private function garantizarComentarios(Usuario $usuario): void
    {
        $faltan = self::MIN_COMENTARIOS - $usuario->comentarios()->count();

        if ($faltan <= 0) {
            return;
        }

        $publicaciones = Publicacion::where('oposicion_id', $usuario->oposicion_id)
            ->where('usuario_id', '!=', $usuario->id)
            ->inRandomOrder()
            ->limit($faltan)
            ->get();

        $textos = collect([
            'Gracias por compartirlo, me lo guardo para repasar.',
            'Muy útil. Yo también tenía esa duda.',
            'Me apunto el consejo, a ver si me funciona igual.',
        ])->shuffle();

        foreach ($publicaciones as $publicacion) {
            Comentario::unguarded(fn () => Comentario::firstOrCreate(
                [
                    'publicacion_id' => $publicacion->id,
                    'usuario_id' => $usuario->id,
                    'contenido' => $textos->pop(),
                ],
                ['created_at' => $this->fechaTras(CarbonImmutable::instance($publicacion->created_at), 48)]
            ));
        }
    }

    /** Al menos un reporte enviado, pendiente, sobre un comentario ajeno. */
    private function garantizarReporte(Usuario $usuario): void
    {
        if ($usuario->reportesMandados()->exists()) {
            return;
        }

        $comentario = Comentario::where('usuario_id', '!=', $usuario->id)
            ->whereHas('publicacion', fn ($q) => $q->where('oposicion_id', $usuario->oposicion_id))
            ->inRandomOrder()
            ->first();

        if (!$comentario) {
            return;
        }

        (new ReporteSeeder)->crear(
            $usuario,
            'comentario',
            $comentario->id,
            'Comentario sin relación con la publicación.',
            $this->fechaTras(CarbonImmutable::instance($comentario->created_at)->max($usuario->created_at), 72),
        );
    }
}
