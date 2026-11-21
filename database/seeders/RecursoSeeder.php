<?php

namespace Database\Seeders;

use App\Models\Oposicion;
use App\Models\Recurso;
use App\Models\Tema;
use Illuminate\Database\Seeder;
use RuntimeException;

class RecursoSeeder extends Seeder
{
    private const AUXILIAR = 'Auxiliar Administrativo del Estado';
    private const POLICIA = 'Policía Nacional (Escala Básica)';

    private const BOE = 'https://www.boe.es/buscar/';

    /**
     * Recursos de cada tema: solo normas publicadas en el BOE (o el DOUE
     * alojado en boe.es), sin recursos de terceros, por sus licencias de uso.
     *
     * Fuentes: programa de Auxiliar (BOE-A-2025-26262) y Anexo I de Policía
     * (BOE-A-2026-15055), con las normas que cubren cada tema.
     *
     * Temas sin recurso, porque ninguna norma del BOE los cubre:
     * - Auxiliar: 6 (Gobierno Abierto y Agenda 2030) y 21 a 28 (informática y ofimática).
     * - Policía: 28, 29, 32, 35, 36, 37, 38, 39 y 40.
     */
    public function run(): void
    {
        $normas = $this->normas();
        $vigentes = [];

        foreach ($this->recursos() as [$nombreOposicion, $numeroTema, $clave, $detalle]) {
            [$titulo, $url] = $normas[$clave]
                ?? throw new RuntimeException("Norma desconocida: {$clave}");

            $tema = $this->tema($nombreOposicion, $numeroTema);

            Recurso::withTrashed()->updateOrCreate(
                [
                    'temario_id' => $tema->id,
                    'recurso_url' => $url,
                ],
                [
                    'subido_por' => null,
                    'titulo' => $detalle ? "{$titulo} ({$detalle})" : $titulo,
                    'deleted_at' => null,
                ]
            );

            $vigentes[$tema->id][] = $url;
        }

        // Recursos sembrados antes en un tema que ya no les corresponde
        // (subido_por null = los crea el seeder, no un administrador)
        $obsoletos = Recurso::whereNull('subido_por')->get()
            ->reject(fn (Recurso $r) => in_array($r->recurso_url, $vigentes[$r->temario_id] ?? [], true));

        $obsoletos->each->delete();

        $this->command?->info('Seeder de recursos completado: ' . count($this->recursos()) . ' recursos'
            . ($obsoletos->isNotEmpty() ? ", {$obsoletos->count()} obsoletos eliminados." : '.'));
    }

    /** @var array<string, array<string, Tema>> */
    private array $temas = [];

    private function tema(string $nombreOposicion, string $numeroTema): Tema
    {
        if (!isset($this->temas[$nombreOposicion])) {
            $oposicion = Oposicion::where('nombre', $nombreOposicion)->first()
                ?? throw new RuntimeException("No existe la oposición: {$nombreOposicion}");

            $this->temas[$nombreOposicion] = Tema::where('oposicion_id', $oposicion->id)
                ->get()->keyBy('numero_tema')->all();
        }

        return $this->temas[$nombreOposicion][$numeroTema]
            ?? throw new RuntimeException("No existe el tema {$numeroTema} de {$nombreOposicion}. Ejecuta antes su seeder de temario.");
    }

    /**
     * Normas citadas: clave => [título, URL]. Todas las URL se han comprobado
     * contra el título publicado en el BOE.
     */
    private function normas(): array
    {
        $act = fn (string $id) => self::BOE . "act.php?id={$id}"; // texto consolidado
        $doc = fn (string $id) => self::BOE . "doc.php?id={$id}"; // disposición sin consolidar

        return [
            // Constitución y órganos constitucionales
            'CE'       => ['Constitución Española de 1978', $act('BOE-A-1978-31229')],
            'LOTC'     => ['Ley Orgánica 2/1979, del Tribunal Constitucional', $act('BOE-A-1979-23709')],
            'RCD'      => ['Reglamento del Congreso de los Diputados', $act('BOE-A-1982-5196')],
            'RS'       => ['Reglamento del Senado (texto refundido)', $act('BOE-A-1994-10830')],
            'LODP'     => ['Ley Orgánica 3/1981, del Defensor del Pueblo', $act('BOE-A-1981-10325')],
            'LOPJ'     => ['Ley Orgánica 6/1985, del Poder Judicial', $act('BOE-A-1985-12666')],
            'LG'       => ['Ley 50/1997, del Gobierno', $act('BOE-A-1997-25336')],
            'CC'       => ['Código Civil', $act('BOE-A-1889-4763')],

            // Administración y procedimiento
            'LTBG'     => ['Ley 19/2013, de transparencia, acceso a la información pública y buen gobierno', $act('BOE-A-2013-12887')],
            'LRJSP'    => ['Ley 40/2015, de Régimen Jurídico del Sector Público', $act('BOE-A-2015-10566')],
            'LPAC'     => ['Ley 39/2015, del Procedimiento Administrativo Común de las Administraciones Públicas', $act('BOE-A-2015-10565')],
            'LJCA'     => ['Ley 29/1998, reguladora de la Jurisdicción Contencioso-administrativa', $act('BOE-A-1998-16718')],
            'LBRL'     => ['Ley 7/1985, reguladora de las Bases del Régimen Local', $act('BOE-A-1985-5392')],
            'LGP'      => ['Ley 47/2003, General Presupuestaria', $act('BOE-A-2003-21614')],
            'RD208'    => ['Real Decreto 208/1996, servicios de información administrativa y atención al ciudadano', $act('BOE-A-1996-4997')],
            'RD951'    => ['Real Decreto 951/2005, marco general para la mejora de la calidad en la Administración General del Estado', $act('BOE-A-2005-14836')],
            'LODPET'   => ['Ley Orgánica 4/2001, reguladora del Derecho de Petición', $act('BOE-A-2001-21090')],
            'RD1708'   => ['Real Decreto 1708/2011, Sistema Español de Archivos', $act('BOE-A-2011-18541')],
            'LPHE'     => ['Ley 16/1985, del Patrimonio Histórico Español', $act('BOE-A-1985-12534')],
            'RD203'    => ['Real Decreto 203/2021, Reglamento de actuación y funcionamiento del sector público por medios electrónicos', $act('BOE-A-2021-5032')],
            'RD1112'   => ['Real Decreto 1112/2018, accesibilidad de los sitios web y aplicaciones móviles del sector público', $act('BOE-A-2018-12699')],

            // Unión Europea y derechos humanos
            'TUE'      => ['Tratado de la Unión Europea y Tratado de Funcionamiento de la Unión Europea (versiones consolidadas)', $doc('DOUE-Z-2016-70015')],
            'LISBOA'   => ['Tratado de Lisboa (instrumento de ratificación)', $doc('BOE-A-2009-18898')],
            'CEDH'     => ['Convenio Europeo para la Protección de los Derechos Humanos y de las Libertades Fundamentales', $doc('BOE-A-1979-24010')],
            'CAT'      => ['Convención contra la tortura y otros tratos o penas crueles, inhumanos o degradantes', $doc('BOE-A-1987-25053')],
            'OPCAT'    => ['Protocolo facultativo a la Convención contra la tortura', $doc('BOE-A-2006-11128')],

            // Protección de datos
            'LOPDGDD'  => ['Ley Orgánica 3/2018, de Protección de Datos Personales y garantía de los derechos digitales', $act('BOE-A-2018-16673')],
            'RGPD'     => ['Reglamento (UE) 2016/679, General de Protección de Datos', $doc('DOUE-L-2016-80807')],
            'LO7_2021' => ['Ley Orgánica 7/2021, protección de datos personales tratados con fines penales', $act('BOE-A-2021-8806')],

            // Función pública
            'TREBEP'   => ['Real Decreto Legislativo 5/2015, texto refundido del Estatuto Básico del Empleado Público', $act('BOE-A-2015-11719')],
            'RD364'    => ['Real Decreto 364/1995, Reglamento General de Ingreso y Provisión de Puestos de Trabajo', $act('BOE-A-1995-8729')],
            'RD365'    => ['Real Decreto 365/1995, Reglamento de Situaciones Administrativas de los Funcionarios Civiles', $act('BOE-A-1995-8730')],
            'RD33'     => ['Real Decreto 33/1986, Reglamento de Régimen Disciplinario de los Funcionarios', $act('BOE-A-1986-1216')],
            'RD462'    => ['Real Decreto 462/2002, sobre indemnizaciones por razón del servicio', $act('BOE-A-2002-10337')],
            'RDL4_2000'=> ['Real Decreto Legislativo 4/2000, Seguridad Social de los Funcionarios Civiles del Estado', $act('BOE-A-2000-12140')],

            // Igualdad, discapacidad y dependencia
            'LOIG'     => ['Ley Orgánica 3/2007, para la igualdad efectiva de mujeres y hombres', $act('BOE-A-2007-6115')],
            'LOVG'     => ['Ley Orgánica 1/2004, de Medidas de Protección Integral contra la Violencia de Género', $act('BOE-A-2004-21760')],
            'LTRANS'   => ['Ley 4/2023, para la igualdad real y efectiva de las personas trans y los derechos de las personas LGTBI', $act('BOE-A-2023-5366')],
            'L15_2022' => ['Ley 15/2022, integral para la igualdad de trato y la no discriminación', $act('BOE-A-2022-11589')],
            'RDL1_2013'=> ['Real Decreto Legislativo 1/2013, Ley General de derechos de las personas con discapacidad', $act('BOE-A-2013-12632')],
            'LDEP'     => ['Ley 39/2006, de Promoción de la Autonomía Personal y Atención a las personas en situación de dependencia', $act('BOE-A-2006-21990')],

            // Interior y Policía
            'RD207'    => ['Real Decreto 207/2024, estructura orgánica básica del Ministerio del Interior', $act('BOE-A-2024-3793')],
            'RD328'    => ['Real Decreto 328/2026, por el que se modifica el Real Decreto 1009/2023', $doc('BOE-A-2026-9079')],
            'RD1009'   => ['Real Decreto 1009/2023, estructura orgánica básica de los departamentos ministeriales', $act('BOE-A-2023-24842')],
            'OINT859'  => ['Orden INT/859/2023, estructura y funciones de los servicios centrales y territoriales de la Dirección General de la Policía', $act('BOE-A-2023-17072')],
            'LO9_2015' => ['Ley Orgánica 9/2015, de Régimen de Personal de la Policía Nacional', $act('BOE-A-2015-8468')],
            'LO4_2010' => ['Ley Orgánica 4/2010, del Régimen disciplinario del Cuerpo Nacional de Policía', $act('BOE-A-2010-8115')],
            'LOFCS'    => ['Ley Orgánica 2/1986, de Fuerzas y Cuerpos de Seguridad', $act('BOE-A-1986-6859')],
            'LOPSC'    => ['Ley Orgánica 4/2015, de protección de la seguridad ciudadana', $act('BOE-A-2015-3442')],
            'LSP'      => ['Ley 5/2014, de Seguridad Privada', $act('BOE-A-2014-3649')],
            'LPIC'     => ['Ley 8/2011, medidas para la protección de las infraestructuras críticas', $act('BOE-A-2011-7630')],
            'RD704'    => ['Real Decreto 704/2011, Reglamento de protección de las infraestructuras críticas', $act('BOE-A-2011-8849')],

            // Extranjería y protección internacional
            'LOEX'     => ['Ley Orgánica 4/2000, sobre derechos y libertades de los extranjeros en España y su integración social', $act('BOE-A-2000-544')],
            'RD557'    => ['Real Decreto 557/2011, Reglamento de la Ley Orgánica 4/2000', $act('BOE-A-2011-7703')],
            'RD240'    => ['Real Decreto 240/2007, entrada y residencia de ciudadanos de la UE y del EEE', $act('BOE-A-2007-4184')],
            'LASILO'   => ['Ley 12/2009, reguladora del derecho de asilo y de la protección subsidiaria', $act('BOE-A-2009-17242')],

            // Penal y procesal
            'CP'       => ['Código Penal (Ley Orgánica 10/1995)', $act('BOE-A-1995-25444')],
            'LORPM'    => ['Ley Orgánica 5/2000, reguladora de la responsabilidad penal de los menores', $act('BOE-A-2000-641')],
            'LECRIM'   => ['Ley de Enjuiciamiento Criminal', $act('BOE-A-1882-6036')],
            'LOHC'     => ['Ley Orgánica 6/1984, reguladora del procedimiento de Habeas Corpus', $act('BOE-A-1984-11620')],
            'EVD'      => ['Ley 4/2015, del Estatuto de la víctima del delito', $act('BOE-A-2015-4606')],

            // Prevención de riesgos laborales
            'LPRL'     => ['Ley 31/1995, de Prevención de Riesgos Laborales', $act('BOE-A-1995-24292')],
            'RD39'     => ['Real Decreto 39/1997, Reglamento de los Servicios de Prevención', $act('BOE-A-1997-1853')],
            'RD2_2006' => ['Real Decreto 2/2006, prevención de riesgos laborales en la Policía Nacional', $act('BOE-A-2006-624')],
            'RD67'     => ['Real Decreto 67/2010, prevención de riesgos laborales en la Administración General del Estado', $act('BOE-A-2010-2161')],

            // Armas y tráfico
            'RARM'     => ['Real Decreto 137/1993, Reglamento de Armas', $act('BOE-A-1993-6202')],
            'LTSV'     => ['Real Decreto Legislativo 6/2015, Ley sobre Tráfico, Circulación de Vehículos a Motor y Seguridad Vial', $act('BOE-A-2015-11722')],
            'RGC'      => ['Real Decreto 1428/2003, Reglamento General de Circulación', $act('BOE-A-2003-23514')],
        ];
    }

    /**
     * [oposición, número de tema, clave de la norma, detalle opcional del título].
     * En Auxiliar, el bloque II (temas 1 a 12 del programa) se numera del 17 al 28.
     */
    private function recursos(): array
    {
        $a = self::AUXILIAR;
        $p = self::POLICIA;

        return [
            // ── AUXILIAR ADMINISTRATIVO DEL ESTADO ──
            // Bloque I. Organización pública
            [$a, '1', 'CE', null],
            [$a, '2', 'CE', null],
            [$a, '2', 'LOTC', null],
            [$a, '3', 'CE', null],
            [$a, '3', 'RCD', null],
            [$a, '3', 'RS', null],
            [$a, '3', 'LODP', null],
            [$a, '4', 'CE', null],
            [$a, '4', 'LOPJ', null],
            [$a, '5', 'CE', null],
            [$a, '5', 'LG', null],
            // Tema 6: sin recurso del BOE
            [$a, '7', 'LTBG', null],
            [$a, '8', 'LRJSP', null],
            [$a, '8', 'LG', null],
            [$a, '9', 'CE', 'Título VIII'],
            [$a, '9', 'LBRL', null],
            [$a, '10', 'TUE', null],
            [$a, '11', 'LPAC', null],
            [$a, '11', 'LRJSP', null],
            [$a, '11', 'LJCA', null],
            [$a, '12', 'LOPDGDD', null],
            [$a, '12', 'RGPD', null],
            [$a, '13', 'TREBEP', null],
            [$a, '13', 'RD364', null],
            [$a, '13', 'RD365', null],
            [$a, '14', 'TREBEP', null],
            [$a, '14', 'RD33', null],
            [$a, '14', 'RD462', null],
            [$a, '14', 'RDL4_2000', null],
            [$a, '15', 'CE', 'Título VII'],
            [$a, '15', 'LGP', null],
            [$a, '16', 'LOIG', null],
            [$a, '16', 'LOVG', null],
            [$a, '16', 'LTRANS', null],
            [$a, '16', 'L15_2022', null],
            [$a, '16', 'RDL1_2013', null],
            [$a, '16', 'LDEP', null],
            // Bloque II. Actividad administrativa y ofimática
            [$a, '17', 'RD208', null],
            [$a, '17', 'RDL1_2013', null],
            [$a, '18', 'RD208', null],
            [$a, '18', 'RD951', 'quejas y sugerencias'],
            [$a, '18', 'LODPET', null],
            [$a, '18', 'LPAC', null],
            [$a, '19', 'RD1708', null],
            [$a, '19', 'LPHE', null],
            [$a, '19', 'LPAC', 'art. 16, registros'],
            [$a, '20', 'RD203', null],
            [$a, '20', 'LRJSP', 'título preliminar, capítulo V'],
            [$a, '20', 'RD1112', null],
            // Temas 21 a 28: sin recurso del BOE

            // ── POLICÍA NACIONAL (ESCALA BÁSICA) ──
            // A) Ciencias Jurídicas
            [$p, '1', 'CC', null],
            [$p, '2', 'CE', null],
            [$p, '2', 'LODP', null],
            [$p, '3', 'CE', null],
            [$p, '3', 'LOPJ', null],
            [$p, '3', 'LOTC', null],
            [$p, '4', 'TUE', null],
            [$p, '4', 'LISBOA', null],
            [$p, '4', 'CEDH', null],
            [$p, '5', 'LRJSP', null],
            [$p, '5', 'LG', null],
            [$p, '6', 'TREBEP', null],
            [$p, '7', 'RD207', null],
            [$p, '7', 'RD328', null],
            [$p, '7', 'RD1009', null],
            [$p, '8', 'OINT859', null],
            [$p, '8', 'LO9_2015', null],
            [$p, '8', 'LO4_2010', null],
            [$p, '9', 'LOFCS', null],
            [$p, '10', 'LOEX', null],
            [$p, '10', 'RD557', null],
            [$p, '10', 'RD240', null],
            [$p, '11', 'LOEX', 'Título III'],
            [$p, '11', 'RD557', null],
            [$p, '12', 'LASILO', null],
            [$p, '13', 'LSP', null],
            [$p, '14', 'LOPSC', null],
            [$p, '15', 'LPIC', null],
            [$p, '15', 'RD704', null],
            [$p, '16', 'CP', null],
            [$p, '16', 'LORPM', null],
            [$p, '17', 'CP', 'Libro II'],
            [$p, '18', 'CP', 'Libro II'],
            [$p, '19', 'CP', 'Libro II'],
            [$p, '20', 'CP', null],
            [$p, '20', 'LECRIM', 'diligencias de investigación tecnológica'],
            [$p, '21', 'LECRIM', null],
            [$p, '21', 'LOHC', null],
            [$p, '21', 'LOPJ', null],
            [$p, '22', 'EVD', null],
            [$p, '23', 'LOIG', null],
            [$p, '23', 'LOVG', null],
            [$p, '23', 'LDEP', null],
            [$p, '23', 'LTRANS', null],
            [$p, '24', 'LPRL', null],
            [$p, '25', 'LPRL', null],
            [$p, '25', 'RD39', null],
            [$p, '25', 'RD2_2006', null],
            [$p, '25', 'RD67', null],
            [$p, '26', 'LOPDGDD', null],
            [$p, '26', 'LO7_2021', null],
            // B) Ciencias Sociales
            [$p, '27', 'CEDH', null],
            [$p, '27', 'CAT', null],
            [$p, '27', 'OPCAT', null],
            [$p, '27', 'LODP', 'Mecanismo Nacional de Prevención de la Tortura'],
            // Temas 28 y 29: sin recurso del BOE
            [$p, '30', 'CP', 'arts. 22.4 y 510, delitos de odio'],
            [$p, '31', 'LOEX', null],
            // Tema 32: sin recurso del BOE
            [$p, '33', 'LOFCS', null],
            [$p, '33', 'LSP', null],
            [$p, '34', 'CP', 'arts. 368 y siguientes'],
            // Temas 35 a 37: sin recurso del BOE
            // C) Materias Técnico-Científicas
            // Temas 38 a 40: sin recurso del BOE
            [$p, '41', 'CP', 'delitos informáticos'],
            [$p, '42', 'RARM', null],
            [$p, '43', 'LTSV', null],
            [$p, '43', 'RGC', null],
            [$p, '44', 'LTSV', null],
            [$p, '44', 'RGC', null],
            [$p, '45', 'LPRL', null],
            [$p, '45', 'RD2_2006', null],
            [$p, '45', 'LTSV', null],
        ];
    }
}
