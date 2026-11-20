<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Oposicion;
use App\Models\Publicacion;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class PublicacionSeeder extends Seeder
{
    /**
     * Publicaciones de cada comunidad. El autor es un opositor aleatorio
     * de esa oposición, no un usuario concreto.
     */
    public function run(): void
    {
        $publicacionesPorOposicion = [
            'Auxiliar Administrativo del Estado' => [
                'Estoy empezando a preparar la oposición de Auxiliar Administrativo del Estado y me gustaría conocer vuestros métodos de organización. ¿Estudiáis un tema al día o preferís alternar varios temas durante la semana?',
                'Me está costando memorizar algunos artículos de la Constitución Española. ¿Qué técnicas utilizáis para recordar los artículos más importantes? Me vendrían bien recomendaciones de esquemas, repasos o tarjetas de memoria.',
                'Quiero incorporar más preguntas tipo test a mi rutina de estudio. ¿Cuántos test hacéis aproximadamente a la semana y cómo revisáis las respuestas incorrectas para no volver a cometer los mismos errores?',
            ],
            'Policía Nacional (Escala Básica)' => [
                '¿Cómo combináis el estudio del temario con la preparación de las pruebas físicas? Me cuesta encontrar un horario que me permita entrenar sin quitarle demasiadas horas a los temas.',
                'Estoy con la Ley Orgánica 2/1986 de Fuerzas y Cuerpos de Seguridad y se me mezclan las funciones de la Policía Nacional y de la Guardia Civil. ¿Alguien tiene un esquema que le haya servido?',
            ],
        ];

        foreach ($publicacionesPorOposicion as $nombreOposicion => $contenidos) {
            $oposicion = Oposicion::where('nombre', $nombreOposicion)->first();

            $autores = $oposicion
                ? Usuario::where('oposicion_id', $oposicion->id)->where('rol', Rol::OPOSITOR)->get()
                : collect();

            if ($autores->isEmpty()) {
                $this->command?->warn("Sin opositores en «{$nombreOposicion}»: no se crean sus publicaciones. Ejecuta antes UsuarioSeeder.");
                continue;
            }

            foreach ($contenidos as $contenido) {
                // Evita duplicados al relanzar el seeder: busca por comunidad y contenido, no por autor
                Publicacion::firstOrCreate(
                    [
                        'oposicion_id' => $oposicion->id,
                        'contenido' => $contenido,
                    ],
                    ['usuario_id' => $autores->random()->id]
                );
            }
        }
    }
}
