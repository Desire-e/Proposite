<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\Tema;
use App\Models\Test;
use App\Models\Usuario;
use Database\Seeders\Concerns\FechasDemo;
use Illuminate\Database\Seeder;

class TestsSeeder extends Seeder
{
    use FechasDemo;

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
        // Los tests los crea un administrador (el que moderaba esa comunidad)
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

            // firstOrCreate por título y tema: relanzar el seeder no duplica tests,
            // preguntas ni opciones, y dos oposiciones pueden tener un test con el mismo título.
            // Fecha en los primeros días de la demo, para que los intentos sean posteriores.
            $test = Test::unguarded(fn () => Test::firstOrCreate(
                [
                    'titulo' => $datos['titulo'],
                    'temario_id' => $tema->id,
                ],
                [
                    'creado_por' => $creador?->id,
                    'descripcion' => $datos['descripcion'],
                    'created_at' => $this->fechaEntre($this->inicioDemo(), $this->inicioDemo()->addDays(7)),
                ]
            ));

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
                'titulo' => 'La Constitución Española de 1978',
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

            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '3',
                'titulo' => 'Las Cortes Generales y el Defensor del Pueblo',
                'descripcion' => 'Composición y funcionamiento del Congreso y del Senado, iniciativa legislativa popular y Defensor del Pueblo.',
                'preguntas' => [
                    $this->pregunta('Según la Constitución, las Cortes Generales están formadas por:',
                        'El Congreso de los Diputados y el Senado', ['El Congreso de los Diputados, el Senado y el Gobierno', 'El Congreso de los Diputados y el Consejo de Estado']),
                    $this->pregunta('Según el artículo 68.1 de la Constitución, el Congreso se compone de:',
                        'Un mínimo de 300 y un máximo de 400 Diputados', ['350 Diputados fijos', 'Entre 250 y 350 Diputados', 'Entre 300 y 450 Diputados']),
                    $this->pregunta('¿Cuántos Senadores se eligen en cada provincia?',
                        'Cuatro', ['Tres', 'Cinco', 'Dos']),
                    $this->pregunta('El Congreso de los Diputados es elegido por:',
                        'Cuatro años', ['Cinco años', 'Tres años']),
                    $this->pregunta('La iniciativa legislativa popular exige no menos de:',
                        '500.000 firmas acreditadas', ['100.000 firmas acreditadas', '750.000 firmas acreditadas', '1.000.000 de firmas acreditadas']),
                    $this->pregunta('¿Quién puede pedir que las Cámaras se reúnan en sesiones extraordinarias?',
                        'El Gobierno, la Diputación Permanente o la mayoría absoluta de los miembros de cualquiera de las Cámaras',
                        ['Solo el Rey', 'Solo el Presidente del Congreso de los Diputados', 'El Gobierno y el Consejo General del Poder Judicial']),
                    $this->pregunta('El Defensor del Pueblo es elegido por las Cortes Generales para un periodo de:',
                        'Cinco años', ['Cuatro años', 'Seis años', 'Tres años']),
                ],
            ],
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '5',
                'titulo' => 'El Gobierno',
                'descripcion' => 'Composición del Gobierno, investidura del Presidente, moción de censura, cuestión de confianza y Gobierno en funciones.',
                'preguntas' => [
                    $this->pregunta('Según el artículo 98.1 de la Constitución, el Gobierno se compone de:',
                        'El Presidente, los Vicepresidentes en su caso, los Ministros y los demás miembros que establezca la ley',
                        ['El Presidente y los Ministros', 'El Presidente, los Ministros y los Secretarios de Estado', 'El Rey, el Presidente y los Ministros']),
                    $this->pregunta('En la primera votación de investidura, el candidato a Presidente del Gobierno necesita:',
                        'La mayoría absoluta del Congreso de los Diputados', ['La mayoría simple', 'Tres quintos de la Cámara', 'Dos tercios de la Cámara']),
                    $this->pregunta('Si el candidato no obtiene la mayoría necesaria, la nueva votación se celebra:',
                        '48 horas después de la anterior', ['24 horas después', '72 horas después', 'Una semana después']),
                    $this->pregunta('Si ningún candidato obtiene la confianza del Congreso, el Rey disuelve ambas Cámaras cuando han pasado, desde la primera votación:',
                        'Dos meses', ['Un mes', 'Tres meses']),
                    $this->pregunta('La moción de censura debe ser propuesta al menos por:',
                        'La décima parte de los Diputados', ['La quinta parte de los Diputados', 'La mayoría absoluta de los Diputados', 'Cincuenta Diputados']),
                    $this->pregunta('El Presidente del Gobierno plantea la cuestión de confianza ante:',
                        'El Congreso de los Diputados', ['El Senado', 'Las Cortes Generales en sesión conjunta']),
                    $this->pregunta('Según la Ley 50/1997, del Gobierno, el Gobierno en funciones NO puede:',
                        'Aprobar el Proyecto de Ley de Presupuestos Generales del Estado',
                        ['Celebrar reuniones del Consejo de Ministros', 'Despachar los asuntos públicos ordinarios', 'Adoptar medidas en casos de urgencia debidamente acreditados']),
                ],
            ],
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '7',
                'titulo' => 'Transparencia y acceso a la información pública',
                'descripcion' => 'Repaso de la Ley 19/2013: derecho de acceso, plazos, silencio y Consejo de Transparencia y Buen Gobierno.',
                'preguntas' => [
                    $this->pregunta('¿Cuál es el plazo máximo para resolver una solicitud de acceso a la información pública?',
                        'Un mes desde su recepción por el órgano competente', ['Quince días', 'Dos meses', 'Tres meses']),
                    $this->pregunta('Si no se resuelve en plazo una solicitud de acceso a la información, se entiende que:',
                        'La solicitud ha sido desestimada', ['La solicitud ha sido estimada', 'El procedimiento ha caducado']),
                    $this->pregunta('¿Está obligado el solicitante a motivar su solicitud de acceso?',
                        'No, aunque puede exponer los motivos por los que la pide', ['Sí, siempre', 'Solo si pide datos personales']),
                    $this->pregunta('Contra la resolución de una solicitud de acceso cabe una reclamación potestativa ante:',
                        'El Consejo de Transparencia y Buen Gobierno', ['El Defensor del Pueblo', 'La Agencia Española de Protección de Datos', 'El Tribunal de Cuentas']),
                    $this->pregunta('¿En qué plazo se interpone esa reclamación?',
                        'Un mes desde el día siguiente a la notificación', ['Diez días', 'Dos meses', 'Tres meses']),
                    $this->pregunta('El Presidente del Consejo de Transparencia y Buen Gobierno es nombrado por:',
                        'Un período no renovable de cinco años', ['Cuatro años renovables', 'Seis años', 'Cinco años, renovable una vez']),
                ],
            ],
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '12',
                'titulo' => 'Protección de datos personales',
                'descripcion' => 'Preguntas sobre la LOPDGDD y el Reglamento General de Protección de Datos: consentimiento, derechos y plazos.',
                'preguntas' => [
                    $this->pregunta('Según la LOPDGDD, el tratamiento de los datos de un menor puede fundarse en su consentimiento cuando sea:',
                        'Mayor de catorce años', ['Mayor de dieciséis años', 'Mayor de doce años', 'Mayor de dieciocho años']),
                    $this->pregunta('Según el RGPD, una violación de la seguridad de los datos debe notificarse a la autoridad de control, de ser posible, en un máximo de:',
                        '72 horas', ['24 horas', '48 horas', 'Una semana']),
                    $this->pregunta('Según el RGPD, el responsable debe atender una solicitud de ejercicio de derechos en el plazo de:',
                        'Un mes, prorrogable otros dos meses', ['Quince días', 'Dos meses improrrogables', 'Tres meses']),
                    $this->pregunta('El llamado «derecho al olvido» es otro nombre del derecho de:',
                        'Supresión', ['Rectificación', 'Oposición', 'Portabilidad']),
                    $this->pregunta('¿Qué título de la LOPDGDD regula la garantía de los derechos digitales?',
                        'El Título X', ['El Título I', 'El Título V', 'El Título VIII']),
                    $this->pregunta('¿Cuál es la autoridad administrativa independiente de protección de datos de ámbito estatal?',
                        'La Agencia Española de Protección de Datos', ['El Consejo de Transparencia y Buen Gobierno', 'El Defensor del Pueblo']),
                ],
            ],
            [
                'oposicion' => self::AUXILIAR,
                'numero_tema' => '18',
                'titulo' => 'Información administrativa y derecho de petición',
                'descripcion' => 'Información general y particular al ciudadano (RD 208/1996) y derecho de petición (LO 4/2001).',
                'preguntas' => [
                    $this->pregunta('Según el Real Decreto 208/1996, la información administrativa general se facilita:',
                        'Obligatoriamente, sin exigir que se acredite legitimación alguna', ['Solo a quienes sean interesados en un procedimiento', 'Previo pago de una tasa']),
                    $this->pregunta('La información particular sobre el estado de un procedimiento solo puede facilitarse a:',
                        'Los interesados en el procedimiento o sus representantes legales', ['Cualquier ciudadano que la pida', 'Otros funcionarios de la Administración']),
                    $this->pregunta('Según la Ley Orgánica 4/2001, ¿quién puede ejercer el derecho de petición?',
                        'Toda persona natural o jurídica, prescindiendo de su nacionalidad',
                        ['Solo los españoles mayores de edad', 'Solo las personas físicas de nacionalidad española', 'Solo los ciudadanos de la Unión Europea']),
                    $this->pregunta('¿En qué plazo debe declararse y notificarse la inadmisión de una petición?',
                        'En los 45 días hábiles siguientes a su presentación', ['En diez días', 'En un mes', 'En tres meses']),
                    $this->pregunta('Una vez admitida una petición, el plazo máximo para contestarla es de:',
                        'Tres meses desde su presentación', ['Un mes', '45 días', 'Seis meses']),
                    $this->pregunta('El derecho de petición se ejerce:',
                        'Por escrito, por cualquier medio que permita acreditar la voluntad del peticionario', ['De forma verbal ante el Registro', 'Solo en papel']),
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
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '14',
                'titulo' => 'Ley Orgánica 4/2015, de protección de la seguridad ciudadana',
                'descripcion' => 'Documentación e identificación, actuaciones policiales y régimen sancionador de la LO 4/2015.',
                'preguntas' => [
                    $this->pregunta('El Documento Nacional de Identidad es obligatorio a partir de:',
                        'Los catorce años', ['Los doce años', 'Los dieciséis años', 'Los dieciocho años']),
                    $this->pregunta('La permanencia en dependencias policiales para identificar a una persona no puede superar:',
                        'Seis horas', ['Doce horas', '24 horas', '72 horas']),
                    $this->pregunta('¿Cuándo prescriben las infracciones de la Ley Orgánica 4/2015?',
                        'A los seis meses las leves, al año las graves y a los dos años las muy graves',
                        ['Al año las leves, a los dos años las graves y a los tres años las muy graves', 'A los tres meses las leves, a los seis meses las graves y al año las muy graves', 'A los seis meses las leves, a los dos años las graves y a los cuatro años las muy graves']),
                    $this->pregunta('Las infracciones muy graves se sancionan con multa de:',
                        '30.001 a 600.000 euros', ['601 a 30.000 euros', '100 a 600 euros', '60.001 a 1.000.000 de euros']),
                    $this->pregunta('Las infracciones leves se sancionan con multa de:',
                        '100 a 600 euros', ['30 a 300 euros', '601 a 3.000 euros']),
                    $this->pregunta('El registro corporal externo y superficial debe efectuarlo:',
                        'Un agente del mismo sexo que la persona sobre la que se practique',
                        ['Un agente en presencia de un abogado', 'Dos agentes, de cualquier sexo', 'Un agente con autorización judicial previa']),
                ],
            ],
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '16',
                'titulo' => 'Derecho Penal. Parte general',
                'descripcion' => 'Concepto de delito, imprudencia, tentativa, edad penal, clases de penas y circunstancias modificativas.',
                'preguntas' => [
                    $this->pregunta('Según el artículo 10 del Código Penal, son delitos:',
                        'Las acciones y omisiones dolosas o imprudentes penadas por la ley', ['Solo las acciones dolosas penadas por la ley', 'Las acciones contrarias a la moral pública']),
                    $this->pregunta('Las acciones u omisiones imprudentes solo se castigan:',
                        'Cuando expresamente lo disponga la ley', ['Siempre', 'Cuando el resultado sea grave', 'Cuando haya reincidencia']),
                    $this->pregunta('Según el artículo 16.1 del Código Penal, hay tentativa cuando:',
                        'El sujeto da principio a la ejecución del delito por hechos exteriores y el resultado no se produce por causas independientes de su voluntad',
                        ['El sujeto solo planea el delito', 'El resultado se produce, pero sin dolo', 'El autor desiste voluntariamente de la ejecución']),
                    $this->pregunta('No son responsables criminalmente con arreglo al Código Penal los menores de:',
                        'Dieciocho años', ['Catorce años', 'Dieciséis años', 'Veintiún años']),
                    $this->pregunta('En función de su naturaleza y duración, las penas se clasifican en:',
                        'Graves, menos graves y leves', ['Mayores y menores', 'Principales y accesorias']),
                    $this->pregunta('¿Cuál de las siguientes es una circunstancia agravante?',
                        'Ejecutar el hecho mediante disfraz',
                        ['Confesar la infracción a las autoridades antes de conocer el procedimiento', 'Reparar el daño ocasionado a la víctima', 'Obrar por arrebato u obcecación']),
                    $this->pregunta('Las penas que pueden imponerse con arreglo al Código Penal son:',
                        'Privativas de libertad, privativas de otros derechos y multa', ['Prisión y multa', 'Prisión, multa y trabajos forzados']),
                ],
            ],
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '21',
                'titulo' => 'La detención y el procedimiento de Habeas Corpus',
                'descripcion' => 'Duración de la detención, derechos del detenido, obligación de denunciar y Ley Orgánica 6/1984 de Habeas Corpus.',
                'preguntas' => [
                    $this->pregunta('Según la Ley de Enjuiciamiento Criminal, el detenido debe ser puesto en libertad o a disposición judicial, en todo caso, en un plazo máximo de:',
                        '72 horas', ['24 horas', '48 horas', '96 horas']),
                    $this->pregunta('¿Qué juez es competente para conocer la solicitud de Habeas Corpus?',
                        'El Juez de Instrucción del lugar en que se encuentre la persona privada de libertad',
                        ['El Juez de lo Penal', 'El Tribunal Supremo', 'El Juez de Paz del domicilio del detenido']),
                    $this->pregunta('Desde que se dicta el auto de incoación, el juez debe practicar las actuaciones y resolver en un plazo de:',
                        '24 horas', ['48 horas', '72 horas', 'Tres días']),
                    $this->pregunta('¿Quién NO puede instar el procedimiento de Habeas Corpus?',
                        'El agente que practicó la detención', ['El Ministerio Fiscal', 'El Defensor del Pueblo', 'El abogado defensor del privado de libertad']),
                    $this->pregunta('¿Puede el juez competente iniciar de oficio el procedimiento de Habeas Corpus?',
                        'Sí', ['No, solo a instancia de parte', 'Solo si lo pide el Ministerio Fiscal']),
                    $this->pregunta('¿Quién tiene derecho a ser asistido gratuitamente por un intérprete durante la detención?',
                        'El extranjero que no comprenda el castellano, las personas sordas o con discapacidad auditiva y otras personas con dificultades del lenguaje',
                        ['Solo los extranjeros de fuera de la Unión Europea', 'Solo quien lo autorice el juez']),
                    $this->pregunta('Según el artículo 259 de la Ley de Enjuiciamiento Criminal, quien presencie la perpetración de un delito público está obligado a:',
                        'Ponerlo inmediatamente en conocimiento del Juez o funcionario fiscal más próximo', ['Denunciarlo en un plazo de 30 días', 'Denunciarlo solo si es funcionario']),
                ],
            ],
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '22',
                'titulo' => 'Estatuto de la víctima del delito',
                'descripcion' => 'Repaso de la Ley 4/2015: concepto de víctima, ámbito y derechos básicos.',
                'preguntas' => [
                    $this->pregunta('Según la Ley 4/2015, es víctima directa:',
                        'Toda persona física que haya sufrido un daño o perjuicio directamente causado por un delito',
                        ['Toda persona física o jurídica perjudicada por un delito', 'Solo quien presente denuncia', 'Solo quien tenga nacionalidad española']),
                    $this->pregunta('La Ley 4/2015 se aplica a las víctimas de delitos cometidos en España:',
                        'Con independencia de su nacionalidad y de si disfrutan o no de residencia legal', ['Solo si son de nacionalidad española', 'Solo si residen legalmente en España']),
                    $this->pregunta('Desde el primer contacto con las autoridades, la víctima puede estar acompañada de:',
                        'Una persona de su elección', ['Solo su abogado', 'Solo un familiar directo']),
                    $this->pregunta('El artículo 4 de la Ley 4/2015 reconoce el derecho de la víctima a:',
                        'Entender y ser entendida', ['La asistencia jurídica gratuita en todo caso', 'Una indemnización inmediata']),
                    $this->pregunta('¿Tiene la víctima derecho a traducción e interpretación?',
                        'Sí, también en las entrevistas policiales, con traducción gratuita de la información esencial', ['Solo si paga su coste', 'Solo durante el juicio']),
                    $this->pregunta('¿Qué oficinas regula la Ley 4/2015 para la asistencia a las víctimas?',
                        'Las Oficinas de Asistencia a las Víctimas', ['Las Oficinas de Atención al Ciudadano', 'Los Juzgados de Paz', 'Las Delegaciones del Gobierno']),
                ],
            ],
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '27',
                'titulo' => 'Derechos humanos y prevención de la tortura',
                'descripcion' => 'Convenio Europeo de Derechos Humanos, Convención contra la tortura y Mecanismo Nacional de Prevención de la Tortura.',
                'preguntas' => [
                    $this->pregunta('¿Qué artículo del Convenio Europeo de Derechos Humanos prohíbe la tortura?',
                        'El artículo 3', ['El artículo 2', 'El artículo 5', 'El artículo 8']),
                    $this->pregunta('El Convenio Europeo para la Protección de los Derechos Humanos se firmó en:',
                        'Roma, el 4 de noviembre de 1950', ['París, en 1948', 'Estrasburgo, en 1959', 'Niza, en 2000']),
                    $this->pregunta('La Convención contra la tortura y otros tratos o penas crueles, inhumanos o degradantes se hizo en:',
                        'Nueva York, el 10 de diciembre de 1984', ['Ginebra, en 1949', 'Roma, en 1950']),
                    $this->pregunta('Según la Convención contra la tortura, ¿puede invocarse un estado de guerra o una emergencia pública para justificar la tortura?',
                        'No, en ningún caso', ['Sí, en estado de guerra', 'Sí, con autorización judicial']),
                    $this->pregunta('El Comité contra la Tortura está compuesto de:',
                        'Diez expertos', ['Siete expertos', 'Quince expertos', 'Veinte expertos']),
                    $this->pregunta('En España, ¿quién ejerce las funciones del Mecanismo Nacional de Prevención de la Tortura?',
                        'El Defensor del Pueblo', ['El Ministerio del Interior', 'La Fiscalía General del Estado', 'El Consejo General del Poder Judicial']),
                ],
            ],
            [
                'oposicion' => self::POLICIA,
                'numero_tema' => '43',
                'titulo' => 'El vehículo prioritario',
                'descripcion' => 'Vehículos prioritarios en el Reglamento General de Circulación: prioridad, facultades de sus conductores y comportamiento de los demás.',
                'preguntas' => [
                    $this->pregunta('Según el Reglamento General de Circulación, tienen prioridad de paso:',
                        'Los vehículos de servicios de urgencia, públicos o privados, cuando se hallen en servicio de tal carácter',
                        ['Todos los vehículos oficiales', 'Los vehículos policiales siempre, estén o no de servicio', 'Los autobuses de transporte público']),
                    $this->pregunta('Los conductores de vehículos prioritarios hacen uso de su régimen especial:',
                        'Únicamente cuando circulan en prestación de un servicio urgente', ['Siempre que lo decida el conductor', 'Solo en vías urbanas']),
                    $this->pregunta('¿Qué deben cumplir siempre los conductores de vehículos prioritarios?',
                        'Las órdenes y señales de los agentes', ['Los límites de velocidad', 'Las normas de adelantamiento', 'Las normas de estacionamiento']),
                    $this->pregunta('La instalación de luces y señales acústicas especiales en vehículos prioritarios requiere autorización de:',
                        'La Jefatura Provincial de Tráfico', ['El Ayuntamiento', 'El Ministerio de Industria', 'La Delegación del Gobierno']),
                    $this->pregunta('Ante las señales de un vehículo prioritario, los demás conductores deben:',
                        'Facilitarle el paso, apartándose normalmente a su derecha o deteniéndose si fuera preciso', ['Acelerar para despejar la vía', 'Detenerse en el carril izquierdo']),
                    $this->pregunta('En autopista o autovía, en servicio urgente y sin comprometer la seguridad, un vehículo prioritario puede excepcionalmente:',
                        'Circular en sentido contrario, siempre que lo haga por el arcén', ['Circular en sentido contrario por cualquier carril', 'Superar los 200 km/h']),
                    $this->pregunta('¿Cuándo deben utilizar los vehículos prioritarios la señal luminosa aisladamente, sin la acústica?',
                        'Cuando la omisión de las señales acústicas especiales no entrañe peligro para los demás usuarios', ['Nunca: las dos señales se usan siempre juntas', 'Solo de noche']),
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
