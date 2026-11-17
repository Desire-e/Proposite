<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\Tema;
use App\Models\Test;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class TestsSeeder extends Seeder
{
    private const AUXILIAR = 'Auxiliar Administrativo del Estado';
    private const POLICIA = 'Policía Nacional (Escala Básica)';

    // Cada test debe tener entre 5 y 15 preguntas
    private const MIN_PREGUNTAS = 5;
    private const MAX_PREGUNTAS = 15;

    /**
     * Crea los tests de cada oposición, asociados al tema del temario
     * del que tratan (no a un tema aleatorio).
     */
    public function run(): void
    {
        $creador = Usuario::where('rol', Rol::ADMIN)->inRandomOrder()->first();

        $tests = 0;
        $preguntas = 0;

        foreach ($this->datosTests() as $datos) {
            $total = count($datos['preguntas']);

            if ($total < self::MIN_PREGUNTAS || $total > self::MAX_PREGUNTAS) {
                $this->command?->warn("Test «{$datos['titulo']}» omitido: tiene {$total} preguntas (deben ser entre 5 y 15).");
                continue;
            }

            $tema = Tema::where('numero_tema', $datos['numero_tema'])
                ->whereHas('oposicion', fn ($q) => $q->where('nombre', $datos['oposicion']))
                ->first();

            if (! $tema) {
                $this->command?->warn("Test «{$datos['titulo']}» omitido: no existe el tema {$datos['numero_tema']} de {$datos['oposicion']}.");
                continue;
            }

            // firstOrCreate: relanzar el seeder no duplica tests, preguntas ni opciones
            $test = Test::firstOrCreate(
                ['titulo' => $datos['titulo']],
                [
                    'creado_por' => $creador?->id,
                    'temario_id' => $tema->id,
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

            $tests++;
            $preguntas += $total;
        }

        $this->command?->info("Seeder de tests completado: {$tests} tests y {$preguntas} preguntas.");
    }

    /**
     * Tests por oposición. numero_tema indica el tema del que trata cada test.
     * En cada pregunta, la primera opción escrita es la correcta; pregunta() las baraja.
     */
    private function datosTests(): array
    {
        return [
            // ── Auxiliar Administrativo del Estado ──
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '1',
                'titulo' => 'Organización del Estado',
                'descripcion' => 'Preguntas sobre la Constitución Española de 1978: principios, valores superiores y derechos.',
                'preguntas' => [
                    $this->pregunta('¿Qué artículo de la Constitución Española recoge el principio de legalidad?',
                        'Artículo 9.3', ['Artículo 14', 'Artículo 103', 'Artículo 149']),
                    $this->pregunta('¿Cuál es la forma política del Estado español?',
                        'Monarquía parlamentaria', ['República federal', 'Monarquía absoluta', 'República parlamentaria']),
                    $this->pregunta('Según el artículo 1.1 de la Constitución, los valores superiores del ordenamiento jurídico son:',
                        'La libertad, la justicia, la igualdad y el pluralismo político',
                        ['La libertad, la seguridad, la igualdad y la solidaridad', 'La justicia, la paz, la igualdad y la legalidad', 'La libertad, la democracia, la igualdad y la unidad']),
                    $this->pregunta('¿Qué artículo de la Constitución establece que los españoles son iguales ante la ley?',
                        'Artículo 14', ['Artículo 9.3', 'Artículo 16', 'Artículo 24']),
                    $this->pregunta('¿Cuántos artículos tiene la Constitución Española?',
                        '169', ['155', '178', '200']),
                    $this->pregunta('¿En qué fecha se ratificó la Constitución Española en referéndum?',
                        '6 de diciembre de 1978', ['31 de octubre de 1978', '27 de diciembre de 1978', '29 de diciembre de 1978']),
                    $this->pregunta('Según el artículo 12 de la Constitución, los españoles son mayores de edad a los:',
                        '18 años', ['16 años', '21 años', '17 años']),
                ],
            ],
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '11',
                'titulo' => 'Derecho Administrativo',
                'descripcion' => 'Repaso de la Ley 39/2015: plazos, recursos administrativos y silencio administrativo.',
                'preguntas' => [
                    $this->pregunta('¿Qué órgano resuelve los recursos de alzada?',
                        'El superior jerárquico del que dictó el acto', ['El mismo órgano que dictó el acto', 'Los tribunales de justicia', 'El Defensor del Pueblo']),
                    $this->pregunta('El silencio administrativo en procedimientos iniciados a solicitud del interesado es, como regla general:',
                        'Estimatorio', ['Desestimatorio', 'Indiferente', 'Nulo de pleno derecho']),
                    $this->pregunta('Si el acto es expreso, ¿cuál es el plazo para interponer un recurso de alzada?',
                        'Un mes', ['Dos meses', 'Quince días', 'Tres meses']),
                    $this->pregunta('¿Cuál es el plazo máximo para dictar y notificar la resolución del recurso de alzada?',
                        'Tres meses', ['Un mes', 'Seis meses', 'Dos meses']),
                    $this->pregunta('Si el acto es expreso, ¿cuál es el plazo para interponer el recurso potestativo de reposición?',
                        'Un mes', ['Dos meses', 'Diez días', 'Tres meses']),
                    $this->pregunta('Cuando las normas reguladoras del procedimiento no fijen un plazo máximo para resolver, este será de:',
                        'Tres meses', ['Seis meses', 'Un mes', 'Dos meses']),
                    $this->pregunta('Salvo que una ley disponga otro cómputo, los plazos señalados por días se entienden:',
                        'Hábiles, excluyendo sábados, domingos y festivos',
                        ['Naturales', 'Hábiles, excluyendo solo domingos y festivos', 'Naturales, excluyendo los festivos']),
                ],
            ],
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '13',
                'titulo' => 'Función Pública',
                'descripcion' => 'Preguntas sobre el EBEP, grupos de clasificación y situaciones administrativas.',
                'preguntas' => [
                    $this->pregunta('¿Qué artículo del EBEP regula los grupos de clasificación?',
                        'Artículo 76', ['Artículo 24', 'Artículo 60', 'Artículo 90']),
                    $this->pregunta('La situación de servicios especiales se regula en:',
                        'Artículo 87 del EBEP', ['Artículo 45 del EBEP', 'Artículo 100 del EBEP', 'Artículo 30 del EBEP']),
                    $this->pregunta('¿Qué norma aprueba el texto refundido de la Ley del Estatuto Básico del Empleado Público?',
                        'El Real Decreto Legislativo 5/2015, de 30 de octubre',
                        ['La Ley 7/2007, de 12 de abril', 'La Ley 39/2015, de 1 de octubre', 'La Ley 30/1984, de 2 de agosto']),
                    $this->pregunta('¿Qué titulación se exige para acceder al subgrupo C2?',
                        'Graduado en Educación Secundaria Obligatoria',
                        ['Bachiller o Técnico', 'Título universitario de Grado', 'Técnico Superior']),
                    $this->pregunta('¿Cuál de las siguientes NO es una situación administrativa de los funcionarios de carrera según el artículo 85 del EBEP?',
                        'Comisión de servicios', ['Servicio activo', 'Servicios especiales', 'Suspensión de funciones']),
                ],
            ],

            // ── Policía Nacional (Escala Básica) ──
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '2',
                'titulo' => 'Constitución Española',
                'descripcion' => 'Estructura de la Constitución, derechos fundamentales y garantías.',
                'preguntas' => [
                    $this->pregunta('Además del Título Preliminar, ¿cuántos títulos tiene la Constitución Española?',
                        'Diez', ['Nueve', 'Once', 'Ocho']),
                    $this->pregunta('¿Qué artículo de la Constitución reconoce el derecho a la vida y a la integridad física y moral?',
                        'Artículo 15', ['Artículo 14', 'Artículo 17', 'Artículo 18']),
                    $this->pregunta('Según el artículo 17.2 de la Constitución, el plazo máximo de la detención preventiva es de:',
                        '72 horas', ['24 horas', '48 horas', '96 horas']),
                    $this->pregunta('¿Qué artículo de la Constitución recoge el derecho a la tutela judicial efectiva?',
                        'Artículo 24', ['Artículo 20', 'Artículo 25', 'Artículo 117']),
                    $this->pregunta('El Defensor del Pueblo es un alto comisionado designado por:',
                        'Las Cortes Generales', ['El Gobierno', 'El Rey, a propuesta del Consejo General del Poder Judicial', 'El Tribunal Constitucional']),
                ],
            ],
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '9',
                'titulo' => 'Fuerzas y Cuerpos de Seguridad',
                'descripcion' => 'Repaso de la Ley Orgánica 2/1986, de Fuerzas y Cuerpos de Seguridad.',
                'preguntas' => [
                    $this->pregunta('¿Qué ley orgánica regula las Fuerzas y Cuerpos de Seguridad?',
                        'La Ley Orgánica 2/1986, de 13 de marzo',
                        ['La Ley Orgánica 4/2015, de 30 de marzo', 'La Ley Orgánica 6/1985, de 1 de julio', 'La Ley Orgánica 4/2000, de 11 de enero']),
                    $this->pregunta('Las Fuerzas y Cuerpos de Seguridad del Estado están integradas por:',
                        'La Policía Nacional y la Guardia Civil',
                        ['La Policía Nacional, la Guardia Civil y las Policías Locales', 'La Policía Nacional y las Policías autonómicas', 'La Policía Nacional y las Policías Locales']),
                    $this->pregunta('La Policía Nacional es un Instituto Armado de naturaleza civil que depende del:',
                        'Ministro del Interior', ['Ministro de Defensa', 'Presidente del Gobierno', 'Ministro de Justicia']),
                    $this->pregunta('La Guardia Civil es un Instituto Armado de naturaleza:',
                        'Militar', ['Civil', 'Mixta', 'Judicial']),
                    $this->pregunta('Según la Ley Orgánica 2/1986, al utilizar las armas los agentes deben regirse por los principios de:',
                        'Congruencia, oportunidad y proporcionalidad',
                        ['Legalidad, jerarquía y eficacia', 'Necesidad, urgencia y legalidad', 'Eficacia, coordinación y jerarquía']),
                ],
            ],
        ];
    }

    /**
     * Una pregunta con su opción correcta y sus tres incorrectas.
     * Se barajan: la app muestra las opciones en el orden en que se insertan,
     * y la correcta no debe quedar siempre la primera.
     */
    private function pregunta(string $enunciado, string $correcta, array $incorrectas): array
    {
        $opciones = [
            ['contenido' => $correcta, 'es_correcta' => true],
            ...array_map(fn ($contenido) => ['contenido' => $contenido, 'es_correcta' => false], $incorrectas),
        ];

        shuffle($opciones);

        return [
            'enunciado' => $enunciado,
            'opciones' => $opciones,
        ];
    }
}
