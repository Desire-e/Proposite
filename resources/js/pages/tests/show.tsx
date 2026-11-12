import AppLayout from '@/layouts/AppLayout';
import { ArrowRight, CheckCircle2, Clock3, BookOpen, ListChecks, Trophy } from 'lucide-react'
import { type Test } from '@/types/models';

import { SectionCard } from '@/components/ui/SectionCard';
import { CtaCard } from '@/components/ui/CtaCard';
import { BackLink } from '@/components/ui/BackLink';
import { duracionRecomendada } from '@/lib/tests';
import { Link } from '@inertiajs/react';

type Usuario = {
    mejor_nota: number | null;
    ultima_nota: number | null;
}


export default function TestShow({ test, usuario }: { test: Test, usuario: Usuario }) {

    const duracion = duracionRecomendada(test.preguntas_count);

    return (
        <AppLayout headTitle={`Test de ${test.titulo}`}>
            <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">
                <BackLink routeName="tests.index">Volver a tests</BackLink>

                <div className="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start">
                    <section>
                            <h1 className="mt-5 max-w-3xl font-heading lg:text-6xl text-4xl font-bold 
                            leading-[1.02] text-primary">
                                Test de {test.titulo}
                            </h1>

                            <p className="mt-5 max-w-2xl text-md leading-8 text-neutral-foreground">
                                {test.descripcion}
                            </p>

                            <div className="mt-8 flex flex-wrap gap-2">
                                <span className="flex items-center rounded-full border 
                                border-border bg-tertiary px-4 py-2 text-sm font-semibold">
                                    <BookOpen className="mr-2 inline size-4 text-secondary" />
                                    Tema {test.numero_tema}
                                </span>
                                <span className="flex items-center rounded-full border 
                                border-border bg-tertiary px-4 py-2 text-sm font-semibold">
                                    <ListChecks className="mr-2 inline size-4 text-secondary" />
                                    {test.preguntas_count} preguntas
                                </span>

                                <span className="flex items-center rounded-full border border-border 
                                bg-tertiary px-4 py-2 text-sm font-semibold">
                                    <Clock3 className="mr-2 inline size-4 text-secondary" />
                                    Duración recomendada: {duracion}
                                </span>

                                {/* TODO. Nivel de dificultad */}
                                {/* <span className="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">
                                    Nivel {difficulty}
                                </span> */}
                            </div>
                    </section>

                    <aside className="relative overflow-hidden rounded-3xl bg-primary p-7 text-primary-foreground shadow-xl shadow-primary/10">
                        <div className="absolute -right-12 -top-12 size-40 rounded-full bg-secondary/30" />

                        <div className="relative">
                            <p className="text-xs font-bold uppercase tracking-[0.16em] text-tertiary">
                                Tu mejor nota
                            </p>

                            <p className="mt-5 lg:text-5xl text-4xl font-bold">
                                {usuario.mejor_nota != null ? usuario.mejor_nota : '—'}
                            </p>

                            <p className="mt-1 text-sm text-tertiary">
                                {usuario.ultima_nota != null
                                ? `Última nota: ${usuario.ultima_nota}`
                                : 'Todavía no has realizado este test'}
                            </p>

                        </div>
                    </aside>
                </div>

                <div className="mt-12 grid gap-6 lg:grid-cols-[1fr_320px]">
                    <SectionCard
                        title="Antes de empezar"
                        size="xl"
                        className="rounded-3xl p-6 lg:p-8"
                    >

                        <div className="mt-8 space-y-7">
                            <div className="flex gap-3">
                                <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-secondary" />

                                <p className="leading-6">
                                    <span className='font-bold'>Responde a tu ritmo.</span>
                                    <br />
                                    <span className="text-neutral-foreground text-sm">
                                        Tendrás {duracion} recomendados para completar las{' '}
                                        {test.preguntas_count} preguntas del test.
                                    </span>
                                </p>
                            </div>

                            <div className="flex gap-3">
                                <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-secondary" />

                                <p className="leading-6">
                                    <span className='font-bold'>Comprueba tu resultado.</span>
                                    <br />
                                    <span className="text-neutral-foreground text-sm">
                                        Al terminar verás tus aciertos, fallos y la explicación de
                                        cada respuesta.
                                    </span>
                                </p>
                            </div>

                            <div className="flex gap-3">
                                <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-secondary" />

                                <p className="leading-6">
                                    <span className='font-bold'>Vuelve a los temas clave.</span>
                                    <br />
                                    <span className="text-neutral-foreground text-sm">
                                        Usa tus resultados para decidir qué parte del temario
                                        necesitas repasar.
                                    </span>
                                </p>
                            </div>
                        </div>

                        <Link
                            href={route('tests.intento.create', test.id)}
                            // type="button"
                            className="mt-9 inline-flex w-full items-center justify-center gap-2 rounded-xl 
                            bg-primary px-5 py-3.5 text-sm font-bold text-primary-foreground 
                            transition hover:bg-tertiary hover:text-tertiary-foreground sm:w-auto"
                        >
                            Empezar test
                            <ArrowRight className="size-4" />
                        </Link>
                    </SectionCard>

                    <aside className="space-y-5">

                        <CtaCard
                            icon={ListChecks}
                            title="Siguiente paso"
                            description="Repasa tus temas antes de volver a intentarlo."
                            link={{ label: 'Abrir mi temario', route: 'temario.index' }}
                            className="rounded-3xl p-6"
                        />

                        <SectionCard
                            icon={Trophy}
                            title="La comunidad avanza"
                            size="md"
                            className="rounded-3xl p-6"
                        >
                            <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                                <span className="text-primary font-bold">
                                    {test.intentos_comunidad_count ?? 0} opositores
                                </span>{' '}
                                ya han realizado este test y están comparando su evolución.
                            </p>
                        </SectionCard>
                    </aside>
                </div>
            </div>
        </AppLayout>
    )
}