import { BackLink } from '@/components/ui/BackLink'
import { SectionCard } from '@/components/ui/SectionCard'
import { SectionHeading } from '@/components/ui/SectionHeading'
import AppLayout from '@/layouts/AppLayout'
import { Link } from '@inertiajs/react'
import { CheckCircle2, CircleX, RotateCcw, Trophy } from 'lucide-react'


type ResultProps = {
    test: Test;
    correccion: Correccion[];
    nota: { 
        actual: number; 
        anterior: number | null 
    };
}


type Test = {
    id: string;
    titulo: string;
    temario_id: string;
}

type Correccion = {
    pregunta: Pregunta;
    opciones: Opcion[];
}


type Pregunta = {
    id: string;
    enunciado: string;
}

type Opcion = {
    id: string;
    contenido: string;
    es_correcta: boolean;
    selected: boolean;
}


export default function TestResult({test, correccion, nota }: ResultProps) {

    const correctCount = correccion.filter(
        (corr) => corr.opciones.some((opc) => opc.selected && opc.es_correcta)
    ).length;

    return (

    <AppLayout headTitle={`Resultado del test`}>
        <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">

            <BackLink routeName="tests.index">Salir del test</BackLink>

            {/* TODO. CREAR COMPONENTE tests/TestHeading.tsx */}
            <section className="border-b border-border pt-8 pb-8 gap-6 flex flex-wrap justify-between">
                <div>
                    <SectionHeading 
                        as='h1'
                        eyebrow='Test completado' 
                        eyebrowClassName='text-success-foreground' 
                        eyebrowIcon={
                            <span className="size-2 rounded-full bg-success-foreground" />
                        } 
                        titleClassName='font-semibold'
                        title={test.titulo}
                        size='xl'
                    />

                    <div className="mt-3 flex flex-wrap flex-col justify-between md:gap-5 gap-10 
                    sm:flex-row sm:items-end w-full">
                        <p className='flex flex-col gap-3 my-5 text-neutral-foreground'>
                            Revisa tus respuestas. Aquí puedes ver qué has acertado y qué temas conviene repasar.
                        </p>
                    </div>

                    <Link
                        href={route('tests.intento.create', test.id)}
                        className="mt-3 inline-flex items-center gap-2 rounded-xl bg-primary 
                        px-4 py-3 text-sm font-bold text-primary-foreground"
                    >
                        <RotateCcw className="size-4" />
                        Repetir test
                    </Link>
                </div>

                <div className="rounded-3xl border border-border bg-tertiary p-7">
                    <div className="flex items-center gap-3">
                        <div className="flex size-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground">
                            <Trophy className="size-6" />
                        </div>

                        <div>
                            <p className="text-xs text-tertiary-foreground">
                            Tu nota total
                            </p>

                            <p className="text-4xl font-bold text-primary">
                                {nota.actual}
                                <span className="text-lg font-normal text-tertiary-foreground">
                                    {' '}/ 10
                                </span>
                            </p>
                        </div>
                    </div>

                    <div className="mt-7 border-t border-border pt-5">
                        <div className="flex justify-between text-sm">
                            <span className="text-tertiary-foreground">
                                Anterior intento
                            </span>

                            <strong className="text-primary">
                                {nota.anterior} / 10
                            </strong>
                        </div>

                        {nota.anterior !== null && (
                        <p className="mt-2 text-xs text-secondary">
                            {nota.actual > nota.anterior
                            ? 'Has mejorado respecto a tu intento anterior.'
                            : 'Repasa los fallos para seguir mejorando.'}
                        </p>)}
                    </div>
                </div>
            </section>

            <section className="mt-10">
                <div className="mb-5 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                            Revisión detallada
                        </p>

                        <h2 className="mt-2 font-serif text-3xl font-bold">
                            Tus respuestas
                        </h2>
                    </div>

                    <span className="rounded-full bg-tertiary px-3 py-1.5 text-xs font-bold text-tertiary-foreground">
                        {correctCount} de {correccion.length} correctas
                    </span>
                </div>

                <div className="space-y-5">
                    {correccion.map((corr, index) => {
                        return (
                            <SectionCard 
                                as='div'
                                className="sm:p-7"
                                key={corr.pregunta.id}
                            >
                                <div className="flex items-start gap-4">
                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-full 
                                    bg-tertiary text-sm font-bold text-primary">
                                        {index + 1}
                                    </span>
                                    <h3 className="text-base font-bold leading-7">
                                        {corr.pregunta.enunciado}
                                    </h3>
                                </div>

                                {/* TODO. CREAR COMPONENTE tests/ResponseOption.tsx */}
                                <div className="min-w-0 flex-1">
                                    <div className="mt-5 grid gap-2">
                                        {corr.opciones.map((opc) => {
                                            return (
                                                <div
                                                    key={opc.id}
                                                    className={`flex w-full items-center gap-3 rounded-xl px-4 py-3 
                                                        text-left text-sm text-neutral-foreground bg-white border
                                                        ${opc.selected ? `font-semibold` : ``}
                                                        ${opc.es_correcta ? `bg-success! text-success-foreground!` : ``}
                                                        ${opc.es_correcta && opc.selected
                                                            ? `border-success-foreground/50!`
                                                            : opc.selected 
                                                                ? `border-error-foreground/50! text-error-foreground! bg-error!` 
                                                                : ``
                                                        }`
                                                    }
                                                >
                                                    <span
                                                        className={`flex size-5 shrink-0 items-center justify-center 
                                                        ${opc.es_correcta 
                                                            ? `text-success-foreground` 
                                                            : `text-error-foreground`}`
                                                        }>
                                                            {opc.es_correcta ? (
                                                                <CheckCircle2 className="size-4" />
                                                            ) : opc.selected ? (
                                                                <CircleX className="size-4" />
                                                            ) : ('')
                                                            }
                                                    </span>

                                                    {opc.contenido}
                                                </div>
                                            )
                                        })}
                                    </div>

                                    {/* <a
                                        href="/temario"
                                        className="mt-4 inline-flex items-center gap-1 text-xs font-bold text-primary"
                                    >
                                        Repasar este tema
                                        <ChevronRight className="size-3.5" />
                                    </a> */}
                                </div>
                            </SectionCard>
                        )
                    })}
                </div>
            </section>
        </div>
    </AppLayout>
    )
}