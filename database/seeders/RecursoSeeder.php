<?php

namespace Database\Seeders;

use App\Models\Oposicion;
use App\Models\Tema;
use App\Models\Recurso;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use RuntimeException;
use Illuminate\Database\Seeder;

class RecursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Recursos públicos relacionados con los temas.
     *
     * Formato:
     * [
     *     'oposicion' => nombre de la oposición,
     *     'tema' => número del tema,
     *     'titulo' => título del recurso,
     *     'url' => URL del recurso,
     * ]
     */
    public function run(): void
    {
        $recursos = [

            /*
            |--------------------------------------------------------------------------
            | AUXILIAR ADMINISTRATIVO DEL ESTADO
            |--------------------------------------------------------------------------
            */

            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '1',
                'titulo' => 'Constitución Española de 1978',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1978-31229',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '2',
                'titulo' => 'Ley Orgánica del Tribunal Constitucional',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1979-10706',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '3',
                'titulo' => 'Constitución Española: Corona y Cortes Generales',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1978-31229',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '4',
                'titulo' => 'Ley Orgánica del Poder Judicial',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1985-12666',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '5',
                'titulo' => 'Ley 50/1997, del Gobierno',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1997-25336',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '6',
                'titulo' => 'Ley 19/2013: transparencia y Gobierno Abierto',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2013-12887',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '7',
                'titulo' => 'Ley 19/2013, de transparencia, acceso a la información pública y buen gobierno',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2013-12887',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '8',
                'titulo' => 'Ley 40/2015, de Régimen Jurídico del Sector Público',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-10566',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '9',
                'titulo' => 'Ley 7/1985, de Bases del Régimen Local',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1985-5392',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '10',
                'titulo' => 'Tratado de la Unión Europea',
                'url' => 'https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:12016M/TXT',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '11',
                'titulo' => 'Ley 39/2015, del Procedimiento Administrativo Común',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-10565',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '11',
                'titulo' => 'Ley 40/2015, de Régimen Jurídico del Sector Público',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-10566',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '12',
                'titulo' => 'Ley Orgánica 3/2018, de Protección de Datos Personales',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2018-16673',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '13',
                'titulo' => 'Estatuto Básico del Empleado Público',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-11719',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '14',
                'titulo' => 'Estatuto Básico del Empleado Público: derechos y deberes',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-11719',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '15',
                'titulo' => 'Ley 47/2003, General Presupuestaria',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2003-21614',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '16',
                'titulo' => 'Ley Orgánica 3/2007, para la igualdad efectiva de mujeres y hombres',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2007-6115',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '16',
                'titulo' => 'Ley Orgánica 1/2004, de medidas de protección integral contra la violencia de género',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2004-21760',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '17',
                'titulo' => 'Procedimiento administrativo común: derechos de las personas',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-10565',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '18',
                'titulo' => 'Ley 19/2013: acceso a la información pública',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2013-12887',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '19',
                'titulo' => 'Ley 39/2015: registros y documentos administrativos',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-10565',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '20',
                'titulo' => 'Real Decreto 203/2021, de actuación y funcionamiento del sector público por medios electrónicos',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2021-5032',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '21',
                'titulo' => 'Ayuda oficial de Windows',
                'url' => 'https://support.microsoft.com/es-es/windows',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '22',
                'titulo' => 'Ayuda oficial de Windows 11',
                'url' => 'https://support.microsoft.com/es-es/windows',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '23',
                'titulo' => 'Documentación de Windows: archivos y carpetas',
                'url' => 'https://support.microsoft.com/es-es/windows',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '24',
                'titulo' => 'Ayuda oficial de Microsoft Word',
                'url' => 'https://support.microsoft.com/es-es/word',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '25',
                'titulo' => 'Ayuda oficial de Microsoft Excel',
                'url' => 'https://support.microsoft.com/es-es/excel',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '26',
                'titulo' => 'Ayuda oficial de Microsoft Access',
                'url' => 'https://support.microsoft.com/es-es/access',
            ],
            [
                'oposicion' => 'Auxiliar Administrativo del Estado',
                'tema' => '27',
                'titulo' => 'Ayuda oficial de Microsoft Outlook',
                'url' => 'https://support.microsoft.com/es-es/outlook',
            ],

            /*
            |--------------------------------------------------------------------------
            | POLICÍA NACIONAL — ESCALA BÁSICA
            |--------------------------------------------------------------------------
            */

            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '1',
                'titulo' => 'Constitución Española',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1978-31229',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '2',
                'titulo' => 'Constitución Española: derechos y organización del Estado',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1978-31229',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '3',
                'titulo' => 'Tratado de la Unión Europea',
                'url' => 'https://eur-lex.europa.eu/legal-content/ES/TXT/?uri=CELEX:12016M/TXT',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '4',
                'titulo' => 'Ley 40/2015, de Régimen Jurídico del Sector Público',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-10566',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '5',
                'titulo' => 'Estatuto Básico del Empleado Público',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-11719',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '6',
                'titulo' => 'Ley Orgánica 2/1986, de Fuerzas y Cuerpos de Seguridad',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1986-6859',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '7',
                'titulo' => 'Ley 5/2014, de Seguridad Privada',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2014-3649',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '8',
                'titulo' => 'Ley Orgánica 4/2000, sobre derechos y libertades de los extranjeros en España',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2000-544',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '9',
                'titulo' => 'Ley 12/2009, reguladora del derecho de asilo y de la protección subsidiaria',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2009-17242',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '10',
                'titulo' => 'Ley Orgánica 3/2018, de Protección de Datos Personales',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2018-16673',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '11',
                'titulo' => 'Declaración Universal de Derechos Humanos',
                'url' => 'https://www.un.org/es/about-us/universal-declaration-of-human-rights',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '12',
                'titulo' => 'Ley Orgánica 4/2015, de protección de la seguridad ciudadana',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-3442',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '13',
                'titulo' => 'Código Penal',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1995-25444',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '14',
                'titulo' => 'Ley de Enjuiciamiento Criminal',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1882-6036',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '15',
                'titulo' => 'Ley 4/2015, del Estatuto de la víctima del delito',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2015-4606',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '16',
                'titulo' => 'Ley Orgánica 3/2007, para la igualdad efectiva de mujeres y hombres',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2007-6115',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '17',
                'titulo' => 'Ley Orgánica 1/2004, de protección integral contra la violencia de género',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2004-21760',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '18',
                'titulo' => 'Ley 31/1995, de Prevención de Riesgos Laborales',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-1995-24292',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '19',
                'titulo' => 'Plan Nacional sobre Drogas',
                'url' => 'https://pnsd.sanidad.gob.es/',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '20',
                'titulo' => 'Instituto Nacional de Estadística',
                'url' => 'https://www.ine.es/',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '21',
                'titulo' => 'Real Academia Española: recursos lingüísticos',
                'url' => 'https://www.rae.es/recursos',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '22',
                'titulo' => 'INCIBE: recursos de ciberseguridad',
                'url' => 'https://www.incibe.es/',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '23',
                'titulo' => 'Europol: ciberdelincuencia',
                'url' => 'https://www.europol.europa.eu/',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '24',
                'titulo' => 'Dirección General de Tráfico',
                'url' => 'https://www.dgt.es/',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '25',
                'titulo' => 'Guardia Civil: información institucional',
                'url' => 'https://www.guardiacivil.es/',
            ],
            [
                'oposicion' => 'Policía Nacional (Escala Básica)',
                'tema' => '26',
                'titulo' => 'Código de normativa para ingreso en la Policía Nacional',
                'url' => 'https://www.boe.es/biblioteca_juridica/codigos/codigo.php?id=600&modo=1&tipo=L',
            ],

            /*
            |--------------------------------------------------------------------------
            | MAESTRO/A DE EDUCACIÓN PRIMARIA
            |--------------------------------------------------------------------------
            */

            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '1',
                'titulo' => 'Real Decreto 157/2022: enseñanzas mínimas de Educación Primaria',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2022-3296',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '2',
                'titulo' => 'Decreto 101/2023: currículo de Educación Primaria en Andalucía',
                'url' => 'https://www.juntadeandalucia.es/boja/2023/90/2.html',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '3',
                'titulo' => 'Decreto 101/2023: tutoría y orientación educativa',
                'url' => 'https://www.juntadeandalucia.es/boja/2023/90/2.html',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '4',
                'titulo' => 'Real Decreto 157/2022: atención a las diferencias individuales',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2022-3296',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '5',
                'titulo' => 'Real Decreto 157/2022: evaluación y promoción',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2022-3296',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '6',
                'titulo' => 'INTEF: recursos y formación educativa digital',
                'url' => 'https://intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '7',
                'titulo' => 'Currículo estatal de Educación Primaria',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2022-3296',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '8',
                'titulo' => 'Procomún: recursos educativos abiertos',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '9',
                'titulo' => 'Ministerio para la Transición Ecológica',
                'url' => 'https://www.miteco.gob.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '10',
                'titulo' => 'Procomún: materiales didácticos de ciencias',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '11',
                'titulo' => 'Real Decreto 157/2022: educación en valores cívicos y éticos',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2022-3296',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '12',
                'titulo' => 'Procomún: recursos de educación artística',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '13',
                'titulo' => 'Europeana: colecciones culturales',
                'url' => 'https://www.europeana.eu/es',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '14',
                'titulo' => 'Real Academia Española: recursos lingüísticos',
                'url' => 'https://www.rae.es/recursos',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '15',
                'titulo' => 'Procomún: recursos de lengua y lectoescritura',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '16',
                'titulo' => 'Biblioteca Virtual Miguel de Cervantes',
                'url' => 'https://www.cervantesvirtual.com/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '17',
                'titulo' => 'Procomún: recursos de comunicación oral',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '18',
                'titulo' => 'Procomún: recursos de comprensión lectora',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '19',
                'titulo' => 'Procomún: recursos de expresión escrita',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '20',
                'titulo' => 'Real Decreto 157/2022: área de Matemáticas',
                'url' => 'https://www.boe.es/buscar/act.php?id=BOE-A-2022-3296',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '21',
                'titulo' => 'Procomún: resolución de problemas matemáticos',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '22',
                'titulo' => 'Procomún: números y cálculo',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '23',
                'titulo' => 'Procomún: magnitudes y medida',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '24',
                'titulo' => 'Procomún: geometría',
                'url' => 'https://procomun.intef.es/',
            ],
            [
                'oposicion' => 'Maestro/a de Educación Primaria',
                'tema' => '25',
                'titulo' => 'Instituto Nacional de Estadística',
                'url' => 'https://www.ine.es/',
            ],
        ];

        foreach ($recursos as $datos) {
            $oposicion = Oposicion::query()
                ->where('nombre', $datos['oposicion'])
                ->first();

            if (!$oposicion) {
                throw new RuntimeException("No existe la oposición: {$datos['oposicion']}");
            }

            $tema = Tema::query()
                ->where('oposicion_id', $oposicion->id)
                ->where('numero_tema', $datos['tema'])
                ->first();

            if (!$tema) {
                throw new RuntimeException("No existe el tema {$datos['tema']} de " . 
                "{$datos['oposicion']}. Comprueba el número del tema."
                );
            }

            Recurso::withTrashed()->updateOrCreate(
                [
                    'temario_id' => $tema->id,
                    'recurso_url' => $datos['url'],
                ],
                [
                    'subido_por' => null,
                    'titulo' => $datos['titulo'],
                    'deleted_at' => null,
                ]
            );
        }
    }
}
