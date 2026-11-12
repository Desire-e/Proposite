<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\Tema;
use App\Models\Test;
use App\Models\Usuario;
use App\Models\IntentoTest;
use Illuminate\Database\Seeder;

class TestsSeeder extends Seeder
{
    public function run(): void
    {
        // Oposición con temas y al menos 3 usuarios
        $oposicionId = Usuario::whereNotNull('oposicion_id')
            ->whereIn('oposicion_id', Tema::select('oposicion_id'))
            ->select('oposicion_id')
            ->groupBy('oposicion_id')
            ->havingRaw('COUNT(*) >= 1')
            ->inRandomOrder()
            ->value('oposicion_id');

        if (! $oposicionId) {
            $this->command->warn('Necesitas una oposición con temas y al menos 3 usuarios.');
            return;
        }

        $temas = Tema::where('oposicion_id', $oposicionId)->get();
        $creador = Usuario::where('rol', Rol::ADMIN)->inRandomOrder()->first();

        $datosTests = [
            [
                'titulo' => 'Organización del Estado',
                'descripcion' => 'Preguntas sobre la organización territorial y las instituciones del Estado español.',
                'preguntas' => [
                    [
                        'enunciado' => '¿Qué artículo de la Constitución Española recoge el principio de legalidad?',
                        'opciones' => [
                            ['contenido' => 'Artículo 9.3', 'es_correcta' => true],
                            ['contenido' => 'Artículo 14', 'es_correcta' => false],
                            ['contenido' => 'Artículo 103', 'es_correcta' => false],
                            ['contenido' => 'Artículo 149', 'es_correcta' => false],
                        ],
                    ],
                    [
                        'enunciado' => '¿Cuál es la forma política del Estado español?',
                        'opciones' => [
                            ['contenido' => 'Monarquía parlamentaria', 'es_correcta' => true],
                            ['contenido' => 'República federal', 'es_correcta' => false],
                            ['contenido' => 'Monarquía absoluta', 'es_correcta' => false],
                            ['contenido' => 'República parlamentaria', 'es_correcta' => false],
                        ],
                    ],
                ],
            ],
            [
                'titulo' => 'Derecho Administrativo',
                'descripcion' => 'Repaso de procedimiento administrativo, recursos y silencio administrativo.',
                'preguntas' => [
                    [
                        'enunciado' => '¿Qué órgano resuelve los recursos de alzada?',
                        'opciones' => [
                            ['contenido' => 'El superior jerárquico del que dictó el acto', 'es_correcta' => true],
                            ['contenido' => 'El mismo órgano que dictó el acto', 'es_correcta' => false],
                            ['contenido' => 'Los tribunales de justicia', 'es_correcta' => false],
                            ['contenido' => 'El Defensor del Pueblo', 'es_correcta' => false],
                        ],
                    ],
                    [
                        'enunciado' => 'El silencio administrativo en procedimientos iniciados a solicitud del interesado es, como regla general:',
                        'opciones' => [
                            ['contenido' => 'Estimatorio', 'es_correcta' => true],
                            ['contenido' => 'Desestimatorio', 'es_correcta' => false],
                            ['contenido' => 'Indiferente', 'es_correcta' => false],
                            ['contenido' => 'Nulo de pleno derecho', 'es_correcta' => false],
                        ],
                    ],
                ],
            ],
            [
                'titulo' => 'Función Pública',
                'descripcion' => 'Preguntas sobre el EBEP, grupos de clasificación y situaciones administrativas.',
                'preguntas' => [
                    [
                        'enunciado' => '¿Qué artículo del EBEP regula los grupos de clasificación?',
                        'opciones' => [
                            ['contenido' => 'Artículo 76', 'es_correcta' => true],
                            ['contenido' => 'Artículo 24', 'es_correcta' => false],
                            ['contenido' => 'Artículo 60', 'es_correcta' => false],
                            ['contenido' => 'Artículo 90', 'es_correcta' => false],
                        ],
                    ],
                    [
                        'enunciado' => 'La situación de servicios especiales se regula en:',
                        'opciones' => [
                            ['contenido' => 'Artículo 87 del EBEP', 'es_correcta' => true],
                            ['contenido' => 'Artículo 45 del EBEP', 'es_correcta' => false],
                            ['contenido' => 'Artículo 100 del EBEP', 'es_correcta' => false],
                            ['contenido' => 'Artículo 30 del EBEP', 'es_correcta' => false],
                        ],
                    ],
                ],
            ],
        ];

        // firstOrCreate: relanzar el seeder no duplica tests, preguntas ni opciones
        foreach ($datosTests as $datos) {
            $test = Test::firstOrCreate(
                ['titulo' => $datos['titulo']],
                [
                    'creado_por' => $creador?->id,
                    'temario_id' => $temas->random()->id,
                    'descripcion' => $datos['descripcion'],
                ]
            );

            foreach ($datos['preguntas'] as $datosPregunta) {
                $pregunta = Pregunta::firstOrCreate([
                    'test_id' => $test->id,
                    'enunciado' => $datosPregunta['enunciado'],
                ]);

                foreach ($datosPregunta['opciones'] as $opcion) {
                    OpcionRespuesta::firstOrCreate(
                        [
                            'pregunta_id' => $pregunta->id,
                            'contenido' => $opcion['contenido'],
                        ],
                        ['es_correcta' => $opcion['es_correcta']]
                    );
                }
            }
        }

        $this->command->info('Seeder de tests completado: 3 tests, 6 preguntas y 24 opciones.');
    }
}