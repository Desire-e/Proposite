import { useState } from 'react'
import { ArrowRight, Check, Clock3, Flag, ChevronUp } from 'lucide-react'
import AppLayout from '@/layouts/AppLayout'
import { BackLink } from '@/components/ui/BackLink'
import { duracionRecomendada } from '@/lib/tests';
import { FormEventHandler } from 'react';
import { useForm } from '@inertiajs/react';
import { SectionCard } from '@/components/ui/SectionCard';
import { ProgressBar } from '@/components/ui/ProgressBar';
import { SectionHeading } from '@/components/ui/SectionHeading';

// TODO. models.ts
export type TestTryProps = {
    test: {
        id: string;
        titulo: string;
        preguntas: Pregunta[];
    };
}

type Pregunta = {
    id: string;
    enunciado: string;
    opciones: Opcion[];
}

type Opcion = {
    id: string;
    contenido: string;
}

type PostRespuestas = {
    pregunta_id: string;
    respuesta_id: string;
}


export default function TestTry({ test }: TestTryProps) {

    /** 
     * Almacena nuevas respondidas en el estado de datos dell formulario 
     */
    function selectAnswer(preguntaId: string, respuestaId: string) {
        const existeRespuesta = data.respuestas.some((r) => r.pregunta_id === preguntaId);

        const actualizaRespuestas = existeRespuesta 
            // si ya existe pregunta con respuesta (se quiso cambiar) 
            ? data.respuestas.map((respuesta) =>
                respuesta.pregunta_id === preguntaId
                    ? { 
                        ...respuesta, 
                        respuesta_id: respuestaId 
                    }
                    : respuesta
            )
            // si no existe ya pregunta con respuesta, se almacena
            : [...data.respuestas, { pregunta_id: preguntaId, respuesta_id: respuestaId }];
            
            // Actualiza datos para mandar formulario
            setData('respuestas', actualizaRespuestas);
    }

    const duracion = duracionRecomendada(test.preguntas.length);


    /** 
     * Panel de preguntas colapsable (mobile) 
     */
    const [panelAbierto, setPanelAbierto] = useState(false);


    /** 
     * Enviar intento 
     */
    const { data, setData, post, processing, reset } = 
        useForm({ 
            respuestas: [] as PostRespuestas[]
        });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(
            route('tests.intento.store', test.id), 
            {
                preserveScroll: true,
                onSuccess: () => reset('respuestas'),
            }
        );
    };

    return (

    <AppLayout headTitle={`Tests de ${test.titulo}`}>
        <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">
            
            <BackLink routeName="tests.show" id={test.id}>Salir del test</BackLink>


            {/* TODO. CREAR COMPONENTE tests/TestHeading.tsx */}

            <section className="border-b border-border pt-8 pb-8 gap-6 flex flex-wrap justify-between">
                <div>
                    <SectionHeading 
                        as='h1'
                        eyebrow='Test en curso' 
                        eyebrowIcon={
                            <span className="size-2 rounded-full bg-secondary" />
                        } 
                        titleClassName='font-semibold'
                        title={test.titulo}
                        size='xl'
                    />

                    <div className="mt-3 flex flex-wrap flex-col justify-between md:gap-5 gap-10 
                    sm:flex-row sm:items-end w-full">
                        <div className='flex flex-col gap-3 my-5 text-neutral-foreground'>
                            <ul className="leading-7 list-disc list-inside">
                                <li>Selecciona una única respuesta por pregunta.</li>
                                <li>Tus resultados se mostrarán al finalizar.</li>
                                <li>Si sales del test sin finalizar, tu intento no será guardado.</li>
                            </ul>

                            <div className="flex items-center gap-2 text-sm font-semibold text-neutral-foreground">
                                <Clock3 className="size-4 text-secondary" />
                                {duracion} recomendados
                            </div>
                        </div>
                    </div>
                </div>

                <SectionCard as="div" className="p-5 lg:w-[30%] h-fit">
                    <div className="flex items-center gap-2 text-sm font-bold">
                        <Flag className="size-4 text-secondary" />
                        Recuerda
                    </div>

                    <p className="mt-3 text-sm leading-6 text-neutral-foreground">
                        No verás si tus respuestas son correctas o incorrectas hasta completar el test.
                    </p>
                </SectionCard>
            </section>


            <form noValidate onSubmit={submit} className="mt-8 grid gap-8 lg:grid-cols-[1fr_280px]">
                <div className="space-y-5">

                    {/* TODO. CREAR COMPONENTE tests/QuestionContainer.tsx */}
                            
                    {/* Preguntas */}
                    {test.preguntas.map((pregunta, i) => (
                    <SectionCard 
                        as='div'
                        className="sm:p-7"
                        key={pregunta.id}
                        id={`pregunta-${pregunta.id}`}
                    >
                        <div className="flex items-start gap-4">
                            <span className="flex size-8 shrink-0 items-center justify-center rounded-full 
                            bg-tertiary text-sm font-bold text-primary">
                                {i + 1}
                            </span>
                            <h2 className="text-base font-bold leading-7">
                                {pregunta.enunciado}
                            </h2>
                        </div>

                        {/* TODO. CREAR COMPONENTE tests/ResponseOption.tsx */}
                        <div className="min-w-0 flex-1">
                            <div className="mt-5 grid gap-2">
                                {/* Opciones de respuesta */}
                                {pregunta.opciones.map((opcion) => {

                                    // Verifica seleccionada:
                                    // si la id de pregunta es la de esta pregunta
                                    // si la id de opcion es la de esta opcion 
                                    const selected = data.respuestas.some(
                                        r => r.pregunta_id === pregunta.id
                                            && r.respuesta_id === opcion.id
                                    )

                                    return (
                                    <button
                                        key={opcion.id}
                                        type="button"
                                        role="radio"
                                        aria-checked={selected}
                                        onClick={() => selectAnswer(pregunta.id, opcion.id)}
                                        className={`flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm 
                                            ${selected 
                                                ? `border border-primary bg-tertiary font-semibold text-primary`
                                                : `border border-border bg-white text-neutral-foreground 
                                                transition hover:border-secondary hover:bg-tertiary/60`
                                            }`
                                        }
                                    >
                                        <span
                                            className={`flex size-5 shrink-0 items-center justify-center rounded-full
                                                ${selected 
                                                    ? ` border border-primary bg-primary text-primary-foreground` 
                                                    : `border border-border bg-background`}
                                        `}>
                                            {selected && <Check className="size-3" />}
                                        </span>
                                        {opcion.contenido}
                                    </button>
                                    )
                                })}
                            </div>
                        </div>
                    </SectionCard>

                    ))}
                </div>

                {/* TODO. CREAR COMPONENTE tests/CollapsableQuestionsMenu.tsx */}

                <aside className={`
                    fixed inset-x-0 bottom-0 z-10 rounded-t-2xl border-t border-border 
                    bg-tertiary p-4 transition-transform duration-300 
                    lg:self-start
                    lg:static lg:inset-auto lg:rounded-2xl lg:border lg:p-0 lg:space-y-5 
                    lg:bg-background lg:border-0 lg:sticky lg:top-30 lg:h-fit lg:translate-y-0
                    ${ panelAbierto ? 'translate-y-0' : 'translate-y-[calc(100%-3.5rem)]'}
                `}>

                    {/* Mobile - Botón para abrir/cerrar colapsable */}
                    <button
                        type="button"
                        onClick={() => setPanelAbierto((estado) => !estado)}
                        className="mb-4 flex w-full items-center justify-between lg:hidden"
                    >
                        <span className="text-sm font-semibold">
                            {data.respuestas.length} de {test.preguntas.length} respondidas
                        </span>
                        <div className='flex text-primary gap-2'>
                            <span className="text-sm ">Finalizar</span>
                            <ChevronUp className={`size-5 transition-transform ${panelAbierto ? 'rotate-180' : ''}`} />
                        </div>
                    </button>


                    <div className="rounded-2xl lg:border lg:border-border bg-tertiary p-5">

                        {/* Desktop */}
                        <p className="font-semibold hidden lg:block">
                            {data.respuestas.length} de {test.preguntas.length} respondidas
                        </p>

                        {/* Línea de avance */}
                        <ProgressBar 
                            porcentaje={(data.respuestas.length / test.preguntas.length) * 100} 
                            color="primary" className='mt-3'
                        />

                        {/* Ruta rápida de preguntas */}
                        <div className="mt-5 flex lg:grid grid-cols-5 gap-5">
                            {test.preguntas.map((pregunta, i) => (
                            <a
                                key={pregunta.id}
                                href={`#pregunta-${pregunta.id}`}
                                className={
                                data.respuestas.some(r => r.pregunta_id === pregunta.id)
                                    ? `flex size-9 items-center justify-center rounded-lg 
                                    bg-primary text-sm font-semibold text-primary-foreground`
                                    : `flex size-9 items-center justify-center rounded-lg 
                                    bg-secondary text-sm font-semibold text-secondary-foreground`
                                }
                            >
                                {i + 1}
                            </a>
                            ))}
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={processing || data.respuestas.length === 0}
                        className="inline-flex w-full items-center justify-center gap-2
                        rounded-xl bg-primary px-5 py-3.5 text-sm font-bold text-primary-foreground 
                        transition hover:bg-tertiary hover:text-tertiary-foreground 
                        disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Finalizar test
                        <ArrowRight className="size-4" />
                    </button>
                </aside>
            </form>
        </div>
    </AppLayout>

  )
}