<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Publicacion;
use App\Models\Comentario;

class ComentarioSeeder extends Seeder
{
    /**
     * Inserta comentarios en las tres publicaciones de Alba Heredia.
     */
    public function run(): void
    {
        $user = Usuario::where('email', 'alba@gmail.com')->firstOrFail();

        // Comentaristas: otros usuarios de la misma oposición
        $comentaristas = Usuario::where('oposicion_id', $user->oposicion_id)
            ->where('id', '!=', $user->id)
            ->get();

        if ($comentaristas->isEmpty()) {
            $this->command?->warn('No hay otros usuarios en la oposición del usuario. Ejecuta antes el seeder de usuarios.');
            return;
        }

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
        ];

        foreach ($comentariosPorPublicacion as $inicio => $comentarios) {
            $publicacion = Publicacion::where('usuario_id', $alba->id)
                ->where('contenido', 'like', $inicio . '%')
                ->first();

            if (! $publicacion) {
                $this->command?->warn("Publicación no encontrada: {$inicio}...");
                continue;
            }

            foreach ($comentarios as $i => $contenido) {
                $autor = $comentaristas[$i % $comentaristas->count()];

                // Evita duplicados al relanzar el seeder
                Comentario::unguarded(fn () => Comentario::firstOrCreate(
                    [
                        'usuario_id'     => $autor->id,
                        'publicacion_id' => $publicacion->id,
                        'contenido'      => $contenido,
                    ],
                    [
                        // Fechas escalonadas tras la publicación
                        'created_at' => $publicacion->created_at->copy()->addHours($i + 1),
                    ]
                ));
            }
        }
    }
}