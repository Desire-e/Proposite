<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Publicacion;


class PublicacionSeeder extends Seeder
{
    /**
     * Inserta tres publicaciones de Alba Heredia.
     */
    public function run(): void
    {
        // Buscar al usuario que creará las publicaciones.
        $usuario = Usuario::where('email', 'alba@gmail.com')->firstOrFail();

        // Datos de las tres publicaciones.
        $publicaciones = [
            [
                'contenido' => 'Estoy empezando a preparar la oposición de Auxiliar Administrativo del Estado y me gustaría conocer vuestros métodos de organización. ¿Estudiáis un tema al día o preferís alternar varios temas durante la semana?',
            ],
            [
                'contenido' => 'Me está costando memorizar algunos artículos de la Constitución Española. ¿Qué técnicas utilizáis para recordar los artículos más importantes? Me vendrían bien recomendaciones de esquemas, repasos o tarjetas de memoria.',
            ],
            [
                'contenido' => 'Quiero incorporar más preguntas tipo test a mi rutina de estudio. ¿Cuántos test hacéis aproximadamente a la semana y cómo revisáis las respuestas incorrectas para no volver a cometer los mismos errores?',
            ],
        ];

        // Crear las publicaciones asociadas al usuario y a su oposición.
        foreach ($publicaciones as $publicacion) {
            Publicacion::firstOrCreate(
                [
                    'usuario_id' => $usuario->id,
                    'oposicion_id' => $usuario->oposicion->id,
                    'titulo' => $publicacion['titulo'],
                ],
                [
                    'contenido' => $publicacion['contenido'],
                ]
            );
        }
    }
}
