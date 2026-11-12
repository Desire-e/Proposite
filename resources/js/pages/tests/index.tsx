import AppLayout from '@/layouts/AppLayout'
import { BookOpenCheck, CheckCircle2, Trophy } from 'lucide-react'
import { type TestListado } from '@/types/models'

import { PageHero } from '@/components/PageHero'
import { TestCard } from '@/components/tests/TestCard'
import { StatCard } from '@/components/ui/StatCard'
import { CtaCard } from '@/components/ui/CtaCard'
import { SectionHeading } from '@/components/ui/SectionHeading'

type Usuario = {
    oposicion: string,
    numero_tests_realizados: number,
    porcentaje_tests_aprobados: number,
    ranking: number;
}

export default function TestsIndex({ usuario, tests }: {usuario: Usuario, tests: TestListado[]}) {

    // const [difficulty, setDifficulty] = useState<'Todos' | Test['difficulty']>('Todos')

    // const [query, setQuery] = useState('')
    // const listaTests = Array.isArray(tests) ? tests : defaultTests
    // const visibleTests = useMemo(
    // () =>
    //     listaTests.filter(
    //     (test) =>
    //         (difficulty === 'Todos' || test.difficulty === difficulty) &&
    //         `${test.title} ${test.topic}`.toLowerCase().includes(query.toLowerCase()),
    //     ),
    // [listaTests, difficulty, query],
    // )


  return (
        <AppLayout headTitle={`Tests de ${usuario.oposicion}`}>
            <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">

                <PageHero
                    eyebrow="Practica a tu ritmo"
                    title={`Tests de ${usuario.oposicion}`}
                    description="Pon a prueba lo que has estudiado, detecta tus puntos débiles y avanza con una preparación más segura."
                />

                {/* Estadísticas */}
                <section className="mt-8 grid gap-4 sm:grid-cols-3">
                    <StatCard
                        icon={BookOpenCheck}
                        value={usuario.numero_tests_realizados}
                        label="tests realizados"
                    />
                    <StatCard
                        icon={CheckCircle2}
                        value={`${usuario.porcentaje_tests_aprobados}%`}
                        label="tests aprobados"
                    />
                    <StatCard
                        icon={Trophy}
                        value={`Top # ${usuario.ranking}`}
                        label="de tu comunidad"
                    />
                </section>

                {/* Biblioteca de tests */}
                <section className="mt-10">
                    <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                        <SectionHeading eyebrow="Biblioteca de práctica" title="Tests disponibles" size="lg" />

                        {/* TODO. Buscador y filtros */}

                        {/* <div className="flex flex-wrap gap-2">
                            <label className="flex items-center gap-2 rounded-xl border border-border bg-background px-3 py-2">
                                <Search className="size-4 text-neutral-foreground" />
                                <span className="sr-only">Buscar tests</span>
                                <input
                                    value={query}
                                    onChange={(event) => setQuery(event.target.value)}
                                    className="w-36 bg-transparent text-xs outline-none placeholder:text-neutral-foreground"
                                    placeholder="Buscar test"
                                />
                            </label>

                            <label className="flex items-center gap-2 rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold">
                                <Filter className="size-4 text-primary" />
                                <span className="sr-only">Filtrar por dificultad</span>
                                <select
                                    value={difficulty}
                                    onChange={(event) => setDifficulty(event.target.value as typeof difficulty)}
                                    className="bg-transparent outline-none"
                                >
                                    <option>Todos</option>
                                    <option>Inicial</option>
                                    <option>Intermedio</option>
                                    <option>Avanzado</option>
                                </select>
                            </label>
                        </div> */}
                    </div>

                    <div className="mt-5 grid gap-4 lg:grid-cols-2">
                        {tests.map((test) => (
                            <TestCard key={test.id} test={test} />
                        ))}
                    </div>

                    {/* {visibleTests.length === 0 && (
                        <div className="mt-5 rounded-2xl border border-dashed border-border bg-neutral p-10 text-center text-sm text-neutral-foreground">
                        No hemos encontrado tests con esos criterios.
                        </div>
                    )} */}
                </section>

                {/* Llamada a temario */}
                <CtaCard
                    brow="Sigue preparando"
                    title="Repasa antes de ponerte a prueba"
                    description="Consulta el temario y vuelve a los temas que más cuestan."
                    link={{ label: 'Ir al temario', route: 'temario.index' }}
                    size="lg"
                    boton
                    className="mt-10 p-6"
                />
            </div>

        </AppLayout>
  )
}
