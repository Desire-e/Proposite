<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tema;
use App\Models\Oposicion;

class TemarioAuxiliarAdministrativoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar la oposición por su nombre.
        // No se inventa el UUID: se obtiene de la base de datos.
        $oposicion = Oposicion::query()
            ->where('nombre', 'like', '%Auxiliar%')
            ->where('nombre', 'like', '%Administrativo del Estado%')
            ->first();

        if (!$oposicion) {
            throw new RuntimeException(
                'No se ha encontrado la oposición de Auxiliar Administrativo del Estado. '
                . 'Ejecuta primero el seeder de oposiciones y comprueba el nombre de la columna.'
            );
        }

        $temas = [
            // BLOQUE I. ORGANIZACIÓN PÚBLICA

            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '1',
                'titulo' => 'La Constitución Española de 1978. Características. Los principios constitucionales y los valores superiores. Derechos y deberes fundamentales. Su garantía y suspensión.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '2',
                'titulo' => 'El Tribunal Constitucional. La reforma de la Constitución. La Corona: funciones constitucionales del Rey. Sucesión y regencia.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '3',
                'titulo' => 'Las Cortes Generales: composición, atribuciones y funcionamiento del Congreso de los Diputados y Senado. El Defensor del Pueblo.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '4',
                'titulo' => 'El Poder Judicial. El Consejo General del Poder Judicial. El Tribunal Supremo. La organización judicial española.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '5',
                'titulo' => 'El Gobierno y la Administración. El Presidente del Gobierno. El Consejo de Ministros. Designación, causas de cese y responsabilidad del Gobierno.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '6',
                'titulo' => 'El Gobierno Abierto: concepto y principios informadores. La Agenda 2030 y los Objetivos de Desarrollo Sostenible.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '7',
                'titulo' => 'La Ley 19/2013, de 9 de diciembre, de transparencia, acceso a la información pública y buen gobierno.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '8',
                'titulo' => 'La Administración General del Estado. Órganos centrales. Órganos superiores y órganos directivos. Órganos territoriales. Otros órganos administrativos. La Administración del Estado en el exterior.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '9',
                'titulo' => 'La Organización territorial del Estado. Las Comunidades Autónomas: Constitución y distribución de competencias entre el Estado y las Comunidades Autónomas. La Administración local: entidades que la integran. La provincia, el municipio y la isla.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '10',
                'titulo' => 'La organización de la Unión Europea. El Consejo Europeo, el Consejo, el Parlamento Europeo, la Comisión Europea y el Tribunal de Justicia de la Unión Europea.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '11',
                'titulo' => 'Las Leyes del Procedimiento Administrativo Común de las Administraciones Públicas y del Régimen Jurídico del Sector Público. El procedimiento administrativo común y sus fases. La revisión de los actos en vía administrativa: revisión de oficio y recursos administrativos. El recurso contencioso-administrativo. Actividad administrativa impugnable. Las partes: capacidad, legitimación, representación y defensa.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '12',
                'titulo' => 'La protección de datos personales y su régimen jurídico: principios, derechos y obligaciones.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '13',
                'titulo' => 'El personal funcionario al servicio de las Administraciones públicas: concepto y clases. Régimen jurídico. El Registro Central de Personal. Programación de efectivos y Oferta de Empleo Público. Selección. Provisión de puestos de trabajo. Situaciones administrativas de los funcionarios.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '14',
                'titulo' => 'Derechos y deberes de los funcionarios. La carrera administrativa. Promoción interna. El sistema de retribuciones e indemnizaciones. Régimen disciplinario. El régimen de la Seguridad Social de los funcionarios.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '15',
                'titulo' => 'El presupuesto del Estado en España. Contenido, elaboración y estructura. Fases del ciclo presupuestario.',
            ],
            [
                'bloque' => 'I. Organización pública',
                'numero_tema' => '16',
                'titulo' => 'Políticas de igualdad y contra la violencia de género. Políticas de igualdad de trato y no discriminación de las personas LGTBI. Discapacidad y dependencia: régimen jurídico.',
            ],

            // BLOQUE II. ACTIVIDAD ADMINISTRATIVA Y OFIMÁTICA

            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '17',
                'titulo' => 'Atención al público: acogida e información al ciudadano. Atención de personas con discapacidad.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '18',
                'titulo' => 'Los servicios de información administrativa. Información general y particular al ciudadano. Iniciativas. Reclamaciones. Quejas. Peticiones.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '19',
                'titulo' => 'Concepto de documento, registro y archivo. Funciones del registro y del archivo. Clases de archivo y criterios de ordenación.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '20',
                'titulo' => 'Administración electrónica y servicios al ciudadano. La información administrativa. Análisis de principales páginas web de carácter público. Servicios telemáticos. Oficinas integradas de atención al ciudadano. Ventanilla única empresarial. El Punto de Acceso General de la Administración General del Estado.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '21',
                'titulo' => 'Informática básica: conceptos fundamentales sobre el hardware y el software. Sistemas de almacenamiento de datos. Sistemas operativos. Nociones básicas de seguridad informática.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '22',
                'titulo' => 'Introducción al sistema operativo: el entorno Windows. Fundamentos. Trabajo en el entorno gráfico de Windows: ventanas, iconos, menús contextuales, cuadros de diálogo. El escritorio y sus elementos. El menú inicio. Copilot.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '23',
                'titulo' => 'El explorador de Windows. Gestión de carpetas y archivos. Operaciones de búsqueda. Herramientas «Este equipo» y «Acceso rápido». Accesorios. Herramientas del sistema.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '24',
                'titulo' => 'Procesadores de texto: Word 365. Principales funciones y utilidades. Creación y estructuración del documento. Gestión, grabación, recuperación e impresión de ficheros. Personalización del entorno de trabajo.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '25',
                'titulo' => 'Hojas de cálculo: Excel 365. Principales funciones y utilidades. Libros, hojas y celdas. Configuración. Introducción y edición de datos. Fórmulas y funciones. Gráficos. Gestión de datos. Personalización del entorno de trabajo.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '26',
                'titulo' => 'Bases de datos: Access 365. Principales funciones y utilidades. Tablas. Consultas. Formularios. Informes. Relaciones. Importación, vinculación y exportación de datos.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '27',
                'titulo' => 'Correo electrónico: Outlook 365. Conceptos elementales y funcionamiento. El entorno de trabajo. Enviar, recibir, responder y reenviar mensajes. Creación de mensajes. Reglas de mensaje. Libreta de direcciones.',
            ],
            [
                'bloque' => 'II. Actividad administrativa y ofimática',
                'numero_tema' => '28',
                'titulo' => 'La Red Internet: origen, evolución y estado actual. Conceptos elementales sobre protocolos y servicios en Internet. Funcionalidades básicas de los navegadores web.',
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
