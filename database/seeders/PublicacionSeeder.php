<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Comentario;
use App\Models\Oposicion;
use App\Models\Publicacion;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\FechasDemo;
use Illuminate\Database\Seeder;

class PublicacionSeeder extends Seeder
{
    use FechasDemo;

    /**
     * Publicaciones del foro de cada oposición, cada una con sus comentarios.
     *
     * - El autor es un opositor aleatorio de esa oposición.
     * - Cada comentario lo escribe otro opositor de la misma oposición
     *   (nunca el autor) y se crea sobre la publicación recién creada,
     *   sin buscarla por su contenido.
     * - Fechas a partir del 20/09/2026: la publicación, después del alta
     *   del autor; el comentario, después de la publicación y del alta de quien comenta.
     */
    public function run(): void
    {
        $publicaciones = 0;
        $comentarios = 0;

        foreach ($this->datos() as $nombreOposicion => $hilos) {
            $oposicion = Oposicion::where('nombre', $nombreOposicion)->first();

            $opositores = $oposicion
                ? Usuario::where('oposicion_id', $oposicion->id)->where('rol', Rol::OPOSITOR)->get()
                : collect();

            if ($opositores->count() < 2) {
                $this->command?->warn("Faltan opositores en «{$nombreOposicion}»: no se crea su foro. Ejecuta antes UsuarioSeeder.");
                continue;
            }

            foreach ($hilos as $hilo) {
                $autor = $opositores->random();

                // Evita duplicados al relanzar el seeder: busca por comunidad y contenido, no por autor
                // (withTrashed: también entre las dadas de baja, por ejemplo las de un baneado)
                $publicacion = Publicacion::unguarded(fn () => Publicacion::withTrashed()->firstOrCreate(
                    [
                        'oposicion_id' => $oposicion->id,
                        'contenido' => $hilo['contenido'],
                    ],
                    [
                        'usuario_id' => $autor->id,
                        'created_at' => $this->fechaEntre($autor->created_at->addHour(), now()->subDay()),
                    ]
                ));

                if ($publicacion->wasRecentlyCreated) {
                    $publicaciones++;
                }

                $comentaristas = $opositores->where('id', '!=', $publicacion->usuario_id);
                $fecha = CarbonImmutable::instance($publicacion->created_at);

                foreach ($hilo['comentarios'] as $contenido) {
                    $comentarista = $comentaristas->random();
                    // Cada respuesta, después de la anterior y del alta de quien comenta
                    $fecha = $this->fechaTras($fecha->max($comentarista->created_at), 48);

                    $comentario = Comentario::unguarded(fn () => Comentario::withTrashed()->firstOrCreate(
                        [
                            'publicacion_id' => $publicacion->id,
                            'contenido' => $contenido,
                        ],
                        [
                            'usuario_id' => $comentarista->id,
                            'created_at' => $fecha,
                        ]
                    ));

                    if ($comentario->wasRecentlyCreated) {
                        $comentarios++;
                    }
                }
            }
        }

        $this->command?->info("Seeder del foro completado: {$publicaciones} publicaciones y {$comentarios} comentarios creados.");
    }

    /**
     * Oposición => lista de hilos (publicación y sus comentarios, en orden).
     */
    private function datos(): array
    {
        return [
            'Auxiliar Administrativo del Estado' => [
                [
                    'contenido' => 'Estoy empezando a preparar la oposición de Auxiliar Administrativo del Estado y me gustaría conocer vuestros métodos de organización. ¿Estudiáis un tema al día o preferís alternar varios temas durante la semana?',
                    'comentarios' => [
                        'Yo alterno dos temas por día y dejo el fin de semana para repasar. Me funciona mejor que ir tema por tema.',
                        'Al principio estudiaba un tema al día, pero se me olvidaba rápido. Ahora hago repasos cortos cada tres días.',
                        'Te recomiendo fijar un horario fijo y marcar los temas en el temario cuando los termines. Ayuda mucho a ver el avance.',
                    ],
                ],
                [
                    'contenido' => 'Me está costando memorizar algunos artículos de la Constitución Española. ¿Qué técnicas utilizáis para recordar los artículos más importantes? Me vendrían bien recomendaciones de esquemas, repasos o tarjetas de memoria.',
                    'comentarios' => [
                        'Los esquemas por títulos y las tarjetas con el número de artículo por un lado y el contenido por el otro me salvaron.',
                        'Prueba a explicar los artículos en voz alta como si se lo contaras a otra persona. Se queda mucho mejor.',
                        'Yo repaso con tarjetas de memoria espaciadas: al día siguiente, a los tres días y a la semana.',
                    ],
                ],
                [
                    'contenido' => 'Quiero incorporar más preguntas tipo test a mi rutina de estudio. ¿Cuántos test hacéis aproximadamente a la semana y cómo revisáis las respuestas incorrectas para no volver a cometer los mismos errores?',
                    'comentarios' => [
                        'Yo hago unos tres test por semana y apunto en una libreta cada fallo con el artículo correspondiente.',
                        'Después de cada test reviso solo los fallos y las preguntas que acerté dudando. Es lo que más me ha subido la nota.',
                    ],
                ],
                [
                    'contenido' => '¿A alguien más le cuestan los plazos de la Ley 39/2015? Siempre confundo cuándo se cuentan en días hábiles y cuándo en naturales, y en los test lo preguntan mucho.',
                    'comentarios' => [
                        'La regla general es fácil: si la norma no dice otra cosa, los plazos por días son hábiles, y se excluyen sábados, domingos y festivos.',
                        'Lo que a mí me ayudó fue leer el artículo 30 entero un par de veces. Es corto y responde casi todas las preguntas de plazos.',
                        'Ojo con los plazos por meses o años: se cuentan de fecha a fecha, no por días.',
                        'Gracias por sacar el tema, yo también fallaba siempre esas preguntas.',
                    ],
                ],
                [
                    'contenido' => 'Para el bloque II, ¿practicáis Word y Excel con el ordenador o solo estudiáis la teoría? No sé cuánto tiempo dedicarle.',
                    'comentarios' => [
                        'Yo practico con el ordenador. Las preguntas suelen ir de dónde está cada opción en los menús y eso solo se aprende usándolo.',
                        'Le dedico una tarde a la semana. Con eso voy bien, porque el bloque I me lleva mucho más tiempo.',
                        'Excel es el que más cuesta. Practica las funciones básicas (SUMA, PROMEDIO, SI, BUSCARV) y las referencias absolutas.',
                        'Haz los test de ofimática también: te das cuenta enseguida de qué parte de la teoría te falta.',
                    ],
                ],
                [
                    'contenido' => 'Acabo de terminar el tema 7, el de la Ley 19/2013. Mi consejo: centraos en el derecho de acceso a la información pública, que es lo que más preguntan.',
                    'comentarios' => [
                        'Totalmente. Y repasa bien los límites del derecho de acceso, que siempre cae alguno.',
                        'Gracias, me lo apunto para cuando llegue a ese tema.',
                    ],
                ],
                [
                    'contenido' => '¿Sabéis si en la próxima convocatoria cambia la estructura del examen? He leído cosas distintas en varios foros y no encuentro nada oficial.',
                    'comentarios' => [
                        'Lo único fiable es la convocatoria publicada en el BOE. Lo demás son rumores.',
                        'Yo seguiría preparándolo como hasta ahora. Si hay cambios, saldrán en la convocatoria con tiempo.',
                        'Guárdate el enlace del BOE de la última convocatoria y compáralo cuando salga la nueva.',
                    ],
                ],
                [
                    'contenido' => 'Hoy he aprobado por primera vez el test completo del tema de la Constitución. Parece poco, pero llevaba tres semanas atascándome con él y me ha dado muchísima moral.',
                    'comentarios' => [
                        '¡Enhorabuena! Esos pequeños avances son los que te mantienen.',
                        'Muy bien. Ahora a mantenerlo con repasos cortos.',
                        'A mí me pasó lo mismo con ese tema. Después todo empieza a encajar.',
                        '¡Ánimo! Se nota que le estás dedicando constancia.',
                        'Qué bien leer cosas así. ¡A por el siguiente!',
                    ],
                ],
                [
                    'contenido' => '¿Cómo organizáis los repasos de los temas ya estudiados? Cuantos más temas llevo, menos tiempo me queda para repasar los primeros.',
                    'comentarios' => [
                        'Yo dedico un día a la semana solo a repasar, sin temario nuevo.',
                        'Repaso con los test: si saco más de un 7 en un tema, lo dejo descansar dos semanas.',
                        'Haz resúmenes de una página por tema. Al final repasas el temario entero en una tarde.',
                    ],
                ],
                [
                    'contenido' => 'Recomendación: leed la Constitución directamente en el BOE, aunque sea un rato al día. Te acostumbras a la redacción y en el examen se nota.',
                    'comentarios' => [
                        'Muy buen consejo. Los enlaces del temario de aquí llevan directamente al texto consolidado.',
                        'Yo lo hago con los títulos que más cuestan, como el de las Cortes Generales.',
                    ],
                ],
                [
                    'contenido' => '¿Alguien estudia los fines de semana por la mañana y quiere hacer sesiones de test juntos por videollamada?',
                    'comentarios' => [
                        'Me apunto los sábados. Escríbeme y lo organizamos.',
                    ],
                ],
                [
                    'contenido' => 'Tema 13, función pública: llevo días con las situaciones administrativas y sigo confundiendo los tipos de excedencia. ¿Algún truco?',
                    'comentarios' => [
                        'Hazte una tabla con cada excedencia, quién puede pedirla, el tiempo mínimo y si reserva o no el puesto.',
                        'A mí me sirvió leerlas directamente en el EBEP, en el artículo 89. Son pocas líneas.',
                        'Haz los test de ese tema varias veces. Es de los que mejoran rápido practicando.',
                    ],
                ],
                [
                    'contenido' => 'Pregunta rápida: el Defensor del Pueblo, ¿lo estudiáis en el tema 3 junto a las Cortes Generales? Lo he visto en manuales distintos.',
                    'comentarios' => [],
                ],
            ],

            'Policía Nacional (Escala Básica)' => [
                [
                    'contenido' => '¿Cómo combináis el estudio del temario con la preparación de las pruebas físicas? Me cuesta encontrar un horario que me permita entrenar sin quitarle demasiadas horas a los temas.',
                    'comentarios' => [
                        'Yo entreno a primera hora y estudio por la tarde. Así llego al temario con la cabeza despejada.',
                        'Tres días de físico a la semana me bastan. El resto lo dedico al temario y a hacer test.',
                    ],
                ],
                [
                    'contenido' => 'Estoy con la Ley Orgánica 2/1986 de Fuerzas y Cuerpos de Seguridad y se me mezclan las funciones de la Policía Nacional y de la Guardia Civil. ¿Alguien tiene un esquema que le haya servido?',
                    'comentarios' => [
                        'Me hice una tabla con dos columnas, una por cuerpo, y fui rellenando sus funciones según las leía en la ley. Se ve mucho más claro.',
                        'Fíjate primero en las funciones que comparten y después en las exclusivas de cada cuerpo. A mí me ayudó a no mezclarlas.',
                    ],
                ],
                [
                    'contenido' => '¿Qué tal lleváis el bloque de Ciencias Sociales? Me parece el más difícil de preparar, porque no hay una ley concreta que estudiar.',
                    'comentarios' => [
                        'A mí me pasa igual. Me apoyo en el manual y hago muchos test para ver por dónde van las preguntas.',
                        'Hay temas, como el de derechos humanos, que sí tienen normas detrás. Empieza por esos.',
                        'Yo lo llevo con resúmenes cortos. Son temas de entender más que de memorizar.',
                        'El de drogodependencias se hace largo, pero las preguntas suelen ser de conceptos básicos.',
                    ],
                ],
                [
                    'contenido' => 'Para la ortografía (tema 37) me está funcionando hacer dictados con textos de periódico. ¿Alguien tiene más ideas?',
                    'comentarios' => [
                        'La web de la RAE tiene un apartado de dudas rápidas muy útil.',
                        'Yo apunto en una libreta cada palabra que escribo mal y la repaso una vez a la semana.',
                        'Leer en voz alta también ayuda con las tildes, sobre todo con las diacríticas.',
                    ],
                ],
                [
                    'contenido' => 'Duda con la detención: el plazo de 72 horas, ¿es el máximo absoluto? En mi manual lo explican de forma confusa.',
                    'comentarios' => [
                        'La Constitución (art. 17.2) dice que no puede durar más del tiempo estrictamente necesario y, como máximo, 72 horas. Después hay que poner al detenido en libertad o a disposición judicial.',
                        'Lo importante es lo de «el tiempo estrictamente necesario»: las 72 horas son el límite, no el plazo normal.',
                        'Hay un caso especial en la Ley de Enjuiciamiento Criminal para terrorismo y bandas armadas. Míralo en el artículo 520 bis.',
                        'Gracias, ahora lo tengo mucho más claro.',
                    ],
                ],
                [
                    'contenido' => 'He empezado con los temas técnicos (redes y sistemas operativos) y vengo de letras. ¿Algún consejo para entender el modelo OSI sin morir en el intento?',
                    'comentarios' => [
                        'Apréndete las siete capas en orden con una regla mnemotécnica y luego un ejemplo de qué hace cada una.',
                        'Compáralo con el modelo TCP/IP, que tiene menos capas. Al verlos juntos se entiende mejor.',
                        'Haz los test de ese tema aunque falles: las preguntas se repiten bastante.',
                    ],
                ],
                [
                    'contenido' => '¿Cuántas horas al día estudiáis? Yo llevo cuatro entre semana y me parece que no es suficiente.',
                    'comentarios' => [
                        'Cuatro horas bien aprovechadas valen más que ocho mirando el móvil.',
                        'Yo hago tres y media, pero también los sábados.',
                        'Depende de cuánto falte para el examen. Al principio con eso vas bien.',
                        'Lo importante es la constancia. Mejor cuatro horas todos los días que diez un día suelto.',
                        'Yo mido los temas terminados por semana, no las horas.',
                    ],
                ],
                [
                    'contenido' => 'Consejo para el tema 14: haced una tabla con las infracciones leves, graves y muy graves de la Ley de Seguridad Ciudadana. Me ha ahorrado muchos fallos.',
                    'comentarios' => [
                        'Buena idea. Yo añadiría una columna con la sanción de cada tipo.',
                        'Me la hago esta semana. Ese tema me está costando.',
                    ],
                ],
                [
                    'contenido' => '¿Alguien ha hecho ya las pruebas físicas en otra convocatoria? Me gustaría saber cómo es el circuito de agilidad en persona.',
                    'comentarios' => [
                        'Es más técnico de lo que parece. Practica los giros alrededor de los conos.',
                        'Entrena con cronómetro y en un suelo parecido al de un pabellón. Cambia mucho.',
                        'Mírate bien las bases de la convocatoria: allí viene el recorrido exacto.',
                    ],
                ],
                [
                    'contenido' => 'Hoy toca Código Penal, parte general. ¿Estudiáis los grados de ejecución con ejemplos? A mí me ayuda pensar en casos reales.',
                    'comentarios' => [
                        'Sí, con ejemplos se entiende mucho mejor la diferencia entre tentativa y consumación.',
                        'Yo me invento un caso para cada artículo y lo explico en voz alta.',
                    ],
                ],
                [
                    'contenido' => 'Estoy repasando el Estatuto de la víctima del delito (tema 22). Recomiendo leer entero el título preliminar: es corto y aclara mucho.',
                    'comentarios' => [
                        'Gracias por el aviso, lo leo esta tarde.',
                    ],
                ],
                [
                    'contenido' => 'Busco compañero o compañera de estudio en Madrid para quedar en una biblioteca un par de días por semana.',
                    'comentarios' => [
                        'Yo estudio en Madrid. ¿Qué zona te viene bien?',
                        'Si os juntáis, avisad. A lo mejor me uno algún día.',
                    ],
                ],
                [
                    'contenido' => 'Me he dado cuenta de que fallo más por leer rápido que por no saberme el temario. ¿Cómo trabajáis la concentración en los test?',
                    'comentarios' => [],
                ],
            ],
        ];
    }
}
