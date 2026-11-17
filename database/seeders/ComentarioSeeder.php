<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Comentario;
use App\Models\Publicacion;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class ComentarioSeeder extends Seeder
{
    /**
     * Comentarios en las publicaciones de PublicacionSeeder. Cada comentario
     * lo escribe un opositor aleatorio de la misma oposición que no sea
     * el autor de la publicación.
     */
    public function run(): void
    {
        // Inicio del contenido de cada publicación => comentarios
        $comentariosPorPublicacion = [
            'Estoy empezando a preparar' => [
                'Yo alterno dos temas por día y dejo el fin de semana para repasar. Me funciona mejor que ir tema por tema.',
                'Al principio estudiaba un tema al día, pero se me olvidaba rápido. Ahora hago repasos cortos cada tres días.',
                'Te recomiendo fijar un horario fijo y marcar los temas en el temario cuando los termines. Ayuda mucho a ver el avance.',
            ],
            'Me está costando memorizar' => [
                'Los esquemas por títulos y las tarjetas con el número de artículo por un lado y el contenido por el otro me salvaron.',
                'Prueba a explicar los artículos en voz alta como si se lo contaras a otra persona. Se queda mucho mejor.',
                'Yo repaso con tarjetas de memoria espaciadas: al día siguiente, a los tres días y a la semana.',
            ],
            'Quiero incorporar más preguntas' => [
                'Yo hago unos tres test por semana y apunto en una libreta cada fallo con el artículo correspondiente.',
                'Después de cada test reviso solo los fallos y las preguntas que acerté dudando. Es lo que más me ha subido la nota.',
            ],
            '¿Cómo combináis el estudio' => [
                'Yo entreno a primera hora y estudio por la tarde. Así llego al temario con la cabeza despejada.',
                'Tres días de físico a la semana me bastan. El resto lo dedico al temario y a hacer test.',
            ],
            'Estoy con la Ley Orgánica 2/1986' => [
                'Me hice una tabla con dos columnas, una por cuerpo, y fui rellenando sus funciones según las leía en la ley. Se ve mucho más claro.',
                'Fíjate primero en las funciones que comparten y después en las exclusivas de cada cuerpo. A mí me ayudó a no mezclarlas.',
            ],
        ];

        foreach ($comentariosPorPublicacion as $inicio => $comentarios) {
            $publicacion = Publicacion::where('contenido', 'like', $inicio . '%')->first();

            if (! $publicacion) {
                $this->command?->warn("Publicación no encontrada: {$inicio}... Ejecuta antes PublicacionSeeder.");
                continue;
            }

            // Comentaristas: opositores de la misma oposición, salvo el autor
            $comentaristas = Usuario::where('oposicion_id', $publicacion->oposicion_id)
                ->where('rol', Rol::OPOSITOR)
                ->where('id', '!=', $publicacion->usuario_id)
                ->get();

            if ($comentaristas->isEmpty()) {
                $this->command?->warn("Nadie puede comentar en «{$inicio}...»: no hay otros opositores en su oposición.");
                continue;
            }

            foreach ($comentarios as $i => $contenido) {
                // Evita duplicados al relanzar el seeder: busca por publicación y contenido, no por autor
                Comentario::unguarded(fn () => Comentario::firstOrCreate(
                    [
                        'publicacion_id' => $publicacion->id,
                        'contenido'      => $contenido,
                    ],
                    [
                        'usuario_id' => $comentaristas->random()->id,
                        // Fechas escalonadas tras la publicación
                        'created_at' => $publicacion->created_at->copy()->addHours($i + 1),
                    ]
                ));
            }
        }
    }
}
