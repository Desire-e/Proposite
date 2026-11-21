<?php

namespace Database\Seeders;

use App\Enums\EstadoReporte;
use App\Enums\Rol;
use App\Models\Comentario;
use App\Models\Oposicion;
use App\Models\Publicacion;
use App\Models\Reporte;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\FechasDemo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ReporteSeeder extends Seeder
{
    use FechasDemo;

    // Reportes por oposición, por tipo
    private const PUBLICACIONES = 3;
    private const COMENTARIOS = 2;
    private const USUARIOS = 2;

    // Porcentaje de reportes ya resueltos por el administrador. Al banear
    // (BaneoSeeder) se resuelven además los que afectan al baneado.
    private const RESUELTOS = 20;

    /**
     * Reportes aleatorios en cada oposición, de los tres tipos.
     *
     * - Quien reporta es un opositor de la misma oposición que lo reportado.
     * - Nadie reporta su propio contenido ni a sí mismo, ni dos veces lo mismo.
     * - Los reportes a usuarios apuntan a un mismo opositor «conflictivo»
     *   (aleatorio, nunca una cuenta de demo), al que BaneoSeeder banea después.
     * - Los resueltos los resolvió el administrador.
     */
    public function run(): void
    {
        // Al relanzar el seeder no se añaden más: se conservan los que ya hay
        if (Reporte::exists()) {
            $this->command?->info('Ya existen reportes: no se crean más.');
            return;
        }

        $admin = Usuario::where('rol', Rol::ADMIN)->first();
        $creados = 0;

        foreach (Oposicion::all() as $oposicion) {
            $opositores = Usuario::where('oposicion_id', $oposicion->id)->where('rol', Rol::OPOSITOR)->get();

            if ($opositores->count() < 3) {
                continue;
            }

            $publicaciones = Publicacion::where('oposicion_id', $oposicion->id)->get();
            $comentarios = Comentario::whereIn('publicacion_id', $publicaciones->pluck('id'))->get();

            $conflictivo = $opositores
                ->whereNotIn('email', array_column(CuentasDemoSeeder::CUENTAS, 'email'))
                ->random();

            $reportables = collect()
                ->concat($this->elegir($publicaciones, self::PUBLICACIONES)->map(fn ($p) => ['publicacion', $p, $p->usuario_id]))
                ->concat($this->elegir($comentarios, self::COMENTARIOS)->map(fn ($c) => ['comentario', $c, $c->usuario_id]))
                ->concat(collect(range(1, self::USUARIOS))->map(fn () => ['usuario', $conflictivo, $conflictivo->id]));

            $motivos = collect($this->motivos())->map(fn ($lista) => collect($lista)->shuffle());

            foreach ($reportables as [$tipo, $reportable, $autorId]) {
                $reportante = $this->reportante($opositores, $tipo, $reportable, $autorId);

                if (!$reportante) {
                    continue;
                }

                $fecha = $this->fechaTras(CarbonImmutable::instance($reportable->created_at)->max($reportante->created_at), 72);
                $resuelto = random_int(1, 100) <= self::RESUELTOS;

                $this->crear($reportante, $tipo, $reportable->id, $motivos[$tipo]->pop(), $fecha, $resuelto ? $admin : null);
                $creados++;
            }
        }

        $this->command?->info("Seeder de reportes completado: {$creados} reportes creados.");
    }

    /**
     * Crea un reporte. También lo usa CuentasDemoSeeder.
     * created_at y estado se asignan con forceFill.
     */
    public function crear(Usuario $reportante, string $tipo, string $reportableId, string $motivo, CarbonImmutable $fecha, ?Usuario $resueltoPor = null): void
    {
        (new Reporte)->forceFill([
            'usuario_reporta_id' => $reportante->id,
            'reportable_tipo' => $tipo, // alias del morph map: publicacion, comentario o usuario
            'reportable_id' => $reportableId,
            'motivo' => $motivo,
            'estado' => $resueltoPor ? EstadoReporte::RESUELTO : EstadoReporte::PENDIENTE,
            'resuelto_por' => $resueltoPor?->id,
            'created_at' => $fecha,
        ])->save();
    }

    /**
     * Opositor aleatorio que puede reportar el elemento: no es su autor
     * (ni el propio usuario reportado) y no lo ha reportado antes.
     */
    private function reportante(Collection $opositores, string $tipo, Model $reportable, string $autorId): ?Usuario
    {
        $yaReportaron = Reporte::where('reportable_tipo', $tipo)
            ->where('reportable_id', $reportable->id)
            ->pluck('usuario_reporta_id');

        $candidatos = $opositores
            ->where('id', '!=', $autorId)
            ->whereNotIn('id', $yaReportaron);

        return $candidatos->isEmpty() ? null : $candidatos->random();
    }

    private function elegir(Collection $elementos, int $cuantos): Collection
    {
        return $elementos->isEmpty() ? collect() : $elementos->random(min($cuantos, $elementos->count()));
    }

    /**
     * Motivos por tipo de reporte. Hay más que reportes de cada tipo.
     */
    public function motivos(): array
    {
        return [
            'publicacion' => [
                'La información sobre la convocatoria no es correcta y puede confundir a otros opositores.',
                'Ya hay otra publicación con la misma pregunta. Creo que está repetida.',
                'Promociona una academia de pago. No parece una consulta real.',
                'El contenido no tiene que ver con la oposición.',
                'Se comparten datos de contacto personales en abierto.',
            ],
            'comentario' => [
                'El comentario da un plazo que no coincide con la ley y puede llevar a error.',
                'Respuesta despectiva hacia quien pregunta.',
                'Comentario sin relación con la publicación.',
                'Incluye un enlace a una web que no es oficial.',
            ],
            'usuario' => [
                'Responde de forma despectiva en varios hilos cuando alguien pregunta dudas básicas.',
                'Publica mensajes repetidos para anunciar una academia.',
                'Su actitud en el foro es ofensiva con otros opositores.',
            ],
        ];
    }
}
