<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tema;
use App\Models\Oposicion;

class TemarioPoliciaNacionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oposicion = Oposicion::query()
            ->where('nombre', 'like', '%Policía Nacional%')
            ->where('nombre', 'like', '%Escala Básica%')
            ->first();

        if (!$oposicion) {
            throw new RuntimeException(
                'No se ha encontrado la oposición Policía Nacional (Escala Básica).'
            );
        }

        $temas = [
            // A) CIENCIAS JURÍDICAS

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '1',
                'titulo' => 'El Derecho: normas jurídicas, jerarquía normativa, persona, nacionalidad, domicilio y vecindad civil.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '2',
                'titulo' => 'La Constitución Española de 1978: estructura, valores, principios, derechos fundamentales, garantías y Defensor del Pueblo.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '3',
                'titulo' => 'La Constitución: Corona, Cortes Generales, Gobierno, Poder Judicial, organización territorial, Tribunal Constitucional y reforma.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '4',
                'titulo' => 'La Unión Europea: historia, Derecho derivado, instituciones y cooperación policial internacional.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '5',
                'titulo' => 'La Administración General del Estado: organización y funcionamiento. El Gobierno y sus funciones.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '6',
                'titulo' => 'Los funcionarios públicos: concepto, clases, adquisición y pérdida de la condición de funcionario.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '7',
                'titulo' => 'El Ministerio del Interior y la Secretaría de Estado de Seguridad: estructura y funciones.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '8',
                'titulo' => 'La Dirección General de la Policía: organización, funciones, escalas, categorías, acceso y régimen disciplinario.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '9',
                'titulo' => 'La Ley Orgánica 2/1986 de Fuerzas y Cuerpos de Seguridad: principios, organización, coordinación y colaboración policial.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '10',
                'titulo' => 'Extranjería: libre circulación, residencia, derechos de los extranjeros, entrada y salida de España.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '11',
                'titulo' => 'Infracciones en materia de extranjería, sanciones, prescripción, expulsión, devolución y denegación de entrada.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '12',
                'titulo' => 'Protección internacional, procedimiento de reconocimiento, personas vulnerables, refugiados, apátridas y desplazados.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '13',
                'titulo' => 'Seguridad privada: empresas, detectives, personal, servicios, medidas de seguridad y coordinación.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '14',
                'titulo' => 'La Ley Orgánica 4/2015 de protección de la seguridad ciudadana: documentación, actuaciones y régimen sancionador.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '15',
                'titulo' => 'Protección de infraestructuras críticas: catálogo, sistema de protección y ciberseguridad.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '16',
                'titulo' => 'Derecho Penal general: principios, infracción penal, delito, responsabilidad, penas, vigencia de la ley y circunstancias modificativas.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '17',
                'titulo' => 'Derecho Penal especial: homicidio, aborto, lesiones, delitos contra la libertad, integridad moral y libertad sexual.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '18',
                'titulo' => 'Delitos contra el patrimonio y el orden socioeconómico: hurto, robo, extorsión, usurpación y defraudaciones.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '19',
                'titulo' => 'Delitos contra el orden público, atentados, resistencia, desobediencia, desórdenes y armas, municiones o explosivos.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '20',
                'titulo' => 'Delitos informáticos, protección de la intimidad y prueba digital en el proceso penal.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '21',
                'titulo' => 'Derecho Procesal Penal: jurisdicción, denuncia, detención, derechos del detenido, habeas corpus, Ministerio Fiscal y Policía Judicial.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '22',
                'titulo' => 'La Ley 4/2015 del Estatuto de la víctima del delito: derechos, protección y medidas de asistencia.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '23',
                'titulo' => 'Igualdad y no discriminación, violencia de género, discapacidad, dependencia e igualdad de las personas LGTBI.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '24',
                'titulo' => 'Prevención de riesgos laborales: trabajo, salud, condiciones laborales, riesgos, prevención y protección.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '25',
                'titulo' => 'Normativa de prevención de riesgos laborales aplicable a la Policía Nacional y a la Administración General del Estado.'],

            ['bloque' => 'A. Ciencias Jurídicas', 'numero_tema' => '26',
                'titulo' => 'Protección de datos personales: Ley Orgánica 3/2018 y Ley Orgánica 7/2021.'],

            // B) CIENCIAS SOCIALES

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '27',
                'titulo' => 'Derechos Humanos: Declaración Universal, Convenio Europeo, prevención de la tortura y mecanismo nacional de prevención.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '28',
                'titulo' => 'Globalización y antiglobalización: conceptos, consecuencias, organizaciones y movimiento antiglobalización.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '29',
                'titulo' => 'Actitudes y valores sociales: estereotipos, prejuicios, discriminación, xenofobia, dogmatismo y grupos sociales.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '30',
                'titulo' => 'Principios éticos y valores sociales: socialización, libertad, igualdad, solidaridad, tolerancia y delitos de odio.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '31',
                'titulo' => 'Inmigración y movimientos migratorios: causas, tipos, efectos e integración social.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '32',
                'titulo' => 'Geografía humana, ciudad, población, demografía, sociedad de masas, contaminación y protección ambiental.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '33',
                'titulo' => 'La seguridad individual y colectiva, seguridad pública y privada, inseguridad y teorías de la delincuencia.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '34',
                'titulo' => 'Drogodependencias: conceptos, consumo, adicción, dependencia, tolerancia, clasificación y tendencias.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '35',
                'titulo' => 'Desarrollo sostenible, cooperación internacional, sociedad e instrumentos de gestión ambiental.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '36',
                'titulo' => 'Gramática española: morfología, categorías gramaticales, verbos, sintaxis y análisis de la oración.'],

            ['bloque' => 'B. Ciencias Sociales', 'numero_tema' => '37',
                'titulo' => 'Ortografía española: grafemas, acentuación, signos, mayúsculas, minúsculas y expresiones numéricas.'],

            // C) MATERIAS TÉCNICO-CIENTÍFICAS

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '38',
                'titulo' => 'Sistemas operativos: funciones, tipos, sistemas móviles, almacenamiento y sistemas de archivos.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '39',
                'titulo' => 'Redes informáticas: modelos OSI y TCP/IP, dispositivos de red, DHCP, DNS, proxy, IPv4 e IPv6.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '40',
                'titulo' => 'Inteligencia: datos, información, ciclo de inteligencia, OSINT, Surface Web, Deep Web, Dark Web y Dark Net.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '41',
                'titulo' => 'Ciberdelincuencia, amenazas, malware, ingeniería social, phishing, ransomware, ataques y cibercriminales.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '42',
                'titulo' => 'Armas de fuego: origen, clasificación, funcionamiento, cartuchos, armas prohibidas, documentación y balística forense.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '43',
                'titulo' => 'Vehículos prioritarios: definición, facultades, conducción de emergencia y señales de emergencia.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '44',
                'titulo' => 'Seguridad en la conducción de vehículos prioritarios: seguridad activa y pasiva, accidentes y traslado de detenidos.'],

            ['bloque' => 'C. Materias Técnico-Científicas', 'numero_tema' => '45',
                'titulo' => 'Prevención de riesgos laborales en seguridad vial: factores de tráfico, riesgos, protección y mantenimiento preventivo.'],
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
