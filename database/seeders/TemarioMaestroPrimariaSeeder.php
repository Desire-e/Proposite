<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tema;
use App\Models\Oposicion;

class TemarioMaestroPrimariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oposicion = Oposicion::query()
            ->where('nombre', 'like', '%Maestro%')
            ->where('nombre', 'like', '%Educación Primaria%')
            ->first();

        if (!$oposicion) {
            throw new RuntimeException(
                'No se ha encontrado la oposición de Maestro/a de Educación Primaria.'
            );
        }

        $temas = [
            // GENERALIDADES EDUCATIVAS

            [
                'bloque' => 'Generalidades educativas',
                'numero_tema' => '1',
                'titulo' => 'Desarrollo psicoevolutivo de los niños y niñas de seis a doce años: aspectos cognitivos, motrices, afectivos y sociales e implicaciones educativas.',
            ],
            [
                'bloque' => 'Generalidades educativas',
                'numero_tema' => '2',
                'titulo' => 'Concreción curricular en el proyecto educativo del centro, programación docente, atención a las necesidades del alumnado y coordinación docente.',
            ],
            [
                'bloque' => 'Generalidades educativas',
                'numero_tema' => '3',
                'titulo' => 'La tutoría en Educación Primaria: orientación, colaboración con las familias, coordinación profesional y plan de acción tutorial.',
            ],
            [
                'bloque' => 'Generalidades educativas',
                'numero_tema' => '4',
                'titulo' => 'Atención a la diversidad: principios, estrategias, medidas organizativas y curriculares y alumnado con necesidad específica de apoyo educativo.',
            ],
            [
                'bloque' => 'Generalidades educativas',
                'numero_tema' => '5',
                'titulo' => 'Evaluación del aprendizaje en Educación Primaria: características, estrategias, técnicas, instrumentos, promoción y refuerzo.',
            ],
            [
                'bloque' => 'Generalidades educativas',
                'numero_tema' => '6',
                'titulo' => 'Tecnologías de la información y la comunicación: intervención educativa y aplicación en las distintas áreas de conocimiento.',
            ],

            // CONOCIMIENTO DEL MEDIO

            [
                'bloque' => 'Conocimiento del medio',
                'numero_tema' => '7',
                'titulo' => 'El área de Conocimiento del medio natural, social y cultural: enfoque, intervención educativa, competencias, objetivos, contenidos y evaluación.',
            ],
            [
                'bloque' => 'Conocimiento del medio',
                'numero_tema' => '8',
                'titulo' => 'Construcción del tiempo histórico, etapas de la humanidad, fuentes históricas e intervención educativa.',
            ],
            [
                'bloque' => 'Conocimiento del medio',
                'numero_tema' => '9',
                'titulo' => 'El entorno y su conservación: ecosistemas, deterioro, regeneración e intervención humana sobre la naturaleza.',
            ],
            [
                'bloque' => 'Conocimiento del medio',
                'numero_tema' => '10',
                'titulo' => 'Aprendizaje de los fenómenos físicos y cambios químicos: experiencias sobre propiedades y comportamiento de la materia y la energía.',
            ],

            // CIUDADANÍA Y EDUCACIÓN ARTÍSTICA

            [
                'bloque' => 'Ciudadanía y derechos humanos',
                'numero_tema' => '11',
                'titulo' => 'Educación para la ciudadanía y los derechos humanos: enfoque, intervención educativa, competencias, objetivos, contenidos y evaluación.',
            ],
            [
                'bloque' => 'Educación artística',
                'numero_tema' => '12',
                'titulo' => 'Educación artística como área integrada: enfoque, intervención educativa, competencias, objetivos, contenidos y evaluación.',
            ],
            [
                'bloque' => 'Educación artística',
                'numero_tema' => '13',
                'titulo' => 'Lenguajes y técnicas artísticas: elaboración de composiciones plásticas e imágenes, planificación, diseño, materiales y técnicas.',
            ],

            // LENGUA CASTELLANA Y LITERATURA

            [
                'bloque' => 'Lengua castellana y literatura',
                'numero_tema' => '14',
                'titulo' => 'El área de Lengua castellana y literatura: enfoque, intervención educativa, competencias, objetivos, contenidos, evaluación y comunicación.',
            ],
            [
                'bloque' => 'Lengua castellana y literatura',
                'numero_tema' => '15',
                'titulo' => 'Reflexión sobre el lenguaje y sus usos comunicativos. Adquisición de la lectura y la escritura.',
            ],
            [
                'bloque' => 'Lengua castellana y literatura',
                'numero_tema' => '16',
                'titulo' => 'Educación literaria: literatura infantil y juvenil, estrategias didácticas y bibliotecas escolar y de aula.',
            ],
            [
                'bloque' => 'Lengua castellana y literatura',
                'numero_tema' => '17',
                'titulo' => 'Adquisición y desarrollo del lenguaje: comprensión y expresión oral, comunicación no verbal e intervención educativa.',
            ],
            [
                'bloque' => 'Lengua castellana y literatura',
                'numero_tema' => '18',
                'titulo' => 'Proceso lector, comprensión lectora, tipos de textos, planes de fomento de la lectura e intervención educativa.',
            ],
            [
                'bloque' => 'Lengua castellana y literatura',
                'numero_tema' => '19',
                'titulo' => 'Expresión escrita: aprendizaje, composición de textos, tecnologías de la información y estrategias de intervención educativa.',
            ],

            // MATEMÁTICAS

            [
                'bloque' => 'Matemáticas',
                'numero_tema' => '20',
                'titulo' => 'El área de Matemáticas: enfoque, intervención educativa, competencias, objetivos, contenidos, evaluación y relación con otras áreas.',
            ],
            [
                'bloque' => 'Matemáticas',
                'numero_tema' => '21',
                'titulo' => 'Resolución de problemas: métodos, planificación, recursos, representación, interpretación y valoración de resultados.',
            ],
            [
                'bloque' => 'Matemáticas',
                'numero_tema' => '22',
                'titulo' => 'Números y cálculo: sistemas de numeración, números naturales, enteros, fraccionarios y decimales, operaciones y cálculo mental y escrito.',
            ],
            [
                'bloque' => 'Matemáticas',
                'numero_tema' => '23',
                'titulo' => 'Magnitudes y medida: unidades, instrumentos, estimación, aproximación y recursos didácticos.',
            ],
            [
                'bloque' => 'Matemáticas',
                'numero_tema' => '24',
                'titulo' => 'Percepción espacial, elementos, formas y relaciones geométricas, clasificación, representación e intervención educativa.',
            ],
            [
                'bloque' => 'Matemáticas',
                'numero_tema' => '25',
                'titulo' => 'Recogida, organización y representación de información: tablas, gráficos, interpretación de datos y tecnologías de la información.',
            ],
        ];

        foreach ($temas as $datosTema) {
            Tema::updateOrCreate(
                [
                    'oposicion_id' => $oposicion->id,
                    'numero_tema' => $datosTema['numero_tema'],
                ],
                [
                    'bloque' => $datosTema['bloque'],
                    'titulo' => $datosTema['titulo'],
                ]
            );
        }
    }
}
