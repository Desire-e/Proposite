<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Oposicion;

use Illuminate\Support\Str;


class OposicionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {

        $oposiciones = [
            [
                'nombre' => 'Policía Nacional (Escala Básica)', 
                'descripcion' => 'Ingreso en la Escala Básica de la Policía Nacional mediante la modalidad de oposición libre. Incluye pruebas de conocimientos jurídicos, pruebas físicas, prueba de idioma, entrevista personal y reconocimiento médico. El temario se centra en el derecho constitucional, la Ley Orgánica de Fuerzas y Cuerpos de Seguridad y la protección de la seguridad ciudadana.'
            ],
            [
                'nombre' => 'Maestro/a de Educación Primaria', 
                'descripcion' => 'Oposición docente para el cuerpo de Maestros, especialidad de Educación Primaria. Consta de una prueba de conocimientos (temario oficial y caso práctico) y una segunda prueba de aptitud pedagógica con programación didáctica y unidad didáctica. La legislación educativa vigente, la psicología del aprendizaje y las didácticas específicas son el núcleo del temario.'
            ],
            [
                'nombre' => 'Auxiliar Administrativo del Estado', 
                'descripcion' => 'Cuerpo General Auxiliar de la Administración del Estado (subgrupo C2). Es una de las oposiciones con más plazas de España y la puerta de entrada al empleo público: atención al público, registro, tratamiento de documentación y tramitación administrativa. La prueba consiste en un ejercicio tipo test sobre el temario oficial, con preguntas de derecho constitucional, organización del Estado, Unión Europea, procedimiento administrativo y función pública.'
            ],

        ];

        foreach ($oposiciones as $oposicion) {
            Oposicion::firstOrCreate (
            [
                'nombre' => $oposicion['nombre']
            ], 
            [ 
                'id' => (string) Str::uuid(), 
                'descripcion' => $oposicion['descripcion'], 
            ]);
        }

    }
}
