'use client'

import { useMemo, useState } from 'react'
import { ArrowRight, BookOpenCheck, CheckCircle2, Clock3, Filter, Search, Sparkles, Trophy } from 'lucide-react'
// import { AppHeader } from '../../Components/AppHeader'
// import { AppFooter } from '../../Components/AppFooter'

type Test = {
  id: number
  title: string
  topic: string
  questions: number
  duration: string
  difficulty: 'Inicial' | 'Intermedio' | 'Avanzado'
  attempts: number
  score?: number
  completed?: boolean
}

export type TestsIndexProps = {
  opposition?: string
  tests?: Test[]
  urls?: {
    home?: string
    syllabus?: string
    forum?: string
  }
}

const defaultTests: Test[] = [
  { id: 1, title: 'Organización del Estado', topic: 'Temas 1–5', questions: 30, duration: '25 min', difficulty: 'Inicial', attempts: 248, score: 8.4, completed: true },
  { id: 2, title: 'Derecho administrativo', topic: 'Temas 6–12', questions: 40, duration: '35 min', difficulty: 'Intermedio', attempts: 186, score: 7.8, completed: true },
  { id: 3, title: 'Función pública y personal', topic: 'Temas 13–18', questions: 35, duration: '30 min', difficulty: 'Intermedio', attempts: 124 },
  { id: 4, title: 'Presupuesto y actividad económica', topic: 'Temas 19–24', questions: 40, duration: '35 min', difficulty: 'Avanzado', attempts: 98 },
  { id: 5, title: 'Test global de repaso', topic: 'Temario completo', questions: 50, duration: '45 min', difficulty: 'Avanzado', attempts: 76 },
]

export default function TestsIndex({
  opposition = 'Administrativo del Estado',
  tests = defaultTests,
  urls = {},
}: TestsIndexProps) {
  const [query, setQuery] = useState('')
  const [difficulty, setDifficulty] = useState<'Todos' | Test['difficulty']>('Todos')

  const visibleTests = useMemo(
    () =>
      tests.filter(
        (test) =>
          (difficulty === 'Todos' || test.difficulty === difficulty) &&
          `${test.title} ${test.topic}`.toLowerCase().includes(query.toLowerCase()),
      ),
    [tests, difficulty, query],
  )

  return (
    <div className="min-h-screen bg-background text-foreground">
      {/* <AppHeader /> */}

      <main className="mx-auto max-w-7xl px-5 py-8 lg:px-8 lg:py-10">
        {/* Migas de pan */}
        <div className="mb-7 flex flex-wrap items-center gap-2 text-sm text-neutral-foreground">
          <a href={urls.home ?? '/'} className="hover:text-primary">
            Inicio
          </a>
          <span>/</span>
          <strong className="text-foreground">Tests</strong>
        </div>

        {/* Cabecera */}
        <section className="overflow-hidden rounded-3xl border border-border bg-tertiary px-6 py-8 lg:px-10 lg:py-10">
          <div className="grid gap-8 lg:grid-cols-[1fr_280px] lg:items-end">
            <div>
              <div className="mb-3 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                <Sparkles className="size-4" /> Practica a tu ritmo
              </div>
              <h1 className="font-serif text-4xl font-bold tracking-tight text-primary lg:text-5xl">
                Tests de {opposition}
              </h1>
              <p className="mt-3 max-w-2xl text-sm leading-6 text-tertiary-foreground">
                Pon a prueba lo que has estudiado, detecta tus puntos débiles y avanza con una preparación más segura.
              </p>
            </div>

            <div className="rounded-2xl bg-background p-5">
              <div className="flex items-center gap-3">
                <div className="flex size-11 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                  <Trophy className="size-5" />
                </div>
                <div>
                  <p className="text-xs text-neutral-foreground">Tu media</p>
                  <strong className="text-2xl">8,1</strong>
                  <span className="ml-2 text-xs font-semibold text-secondary">+0,4 este mes</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Estadísticas */}
        <section className="mt-8 grid gap-4 sm:grid-cols-3">
          <div className="rounded-2xl border border-border bg-background p-5">
            <BookOpenCheck className="size-5 text-primary" />
            <p className="mt-4 text-2xl font-bold">27</p>
            <p className="text-sm text-neutral-foreground">tests realizados</p>
          </div>

          <div className="rounded-2xl border border-border bg-background p-5">
            <CheckCircle2 className="size-5 text-secondary" />
            <p className="mt-4 text-2xl font-bold">68%</p>
            <p className="text-sm text-neutral-foreground">preguntas acertadas</p>
          </div>

          <div className="rounded-2xl border border-border bg-background p-5">
            <Trophy className="size-5 text-primary" />
            <p className="mt-4 text-2xl font-bold">Top 24%</p>
            <p className="text-sm text-neutral-foreground">de tu comunidad</p>
          </div>
        </section>

        {/* Biblioteca de tests */}
        <section className="mt-10">
          <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
              <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                Biblioteca de práctica
              </p>
              <h2 className="mt-1 font-serif text-3xl font-bold">Tests disponibles</h2>
            </div>

            <div className="flex flex-wrap gap-2">
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
            </div>
          </div>

          <div className="mt-5 grid gap-4 lg:grid-cols-2">
            {visibleTests.map((test) => (
              <article
                key={test.id}
                className="group rounded-2xl border border-border bg-background p-5 transition hover:-translate-y-0.5 hover:border-primary/50 hover:shadow-md"
              >
                <div className="flex items-start justify-between gap-4">
                  <div className="flex size-11 items-center justify-center rounded-xl bg-tertiary text-primary">
                    <BookOpenCheck className="size-5" />
                  </div>

                  {test.completed ? (
                    <span className="inline-flex items-center gap-1 rounded-full bg-tertiary px-3 py-1 text-xs font-bold text-tertiary-foreground">
                      <CheckCircle2 className="size-3.5" /> Completado
                    </span>
                  ) : (
                    <span className="rounded-full bg-neutral px-3 py-1 text-xs font-semibold text-neutral-foreground">
                      Disponible
                    </span>
                  )}
                </div>

                <h3 className="mt-5 text-lg font-bold">{test.title}</h3>
                <p className="mt-1 text-sm text-neutral-foreground">{test.topic}</p>

                <div className="mt-5 flex flex-wrap gap-3 text-xs text-neutral-foreground">
                  <span>{test.questions} preguntas</span>
                  <span className="inline-flex items-center gap-1">
                    <Clock3 className="size-3.5" /> {test.duration}
                  </span>
                  <span className="rounded-full bg-tertiary px-2 py-1 font-semibold text-tertiary-foreground">
                    {test.difficulty}
                  </span>
                </div>

                <div className="mt-5 flex items-center justify-between border-t border-border pt-4">
                  <span className="text-xs text-neutral-foreground">
                    {test.attempts} intentos{test.score ? ` · Tu nota ${test.score}` : ''}
                  </span>
                  <button
                    type="button"
                    className="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-primary-foreground transition hover:bg-tertiary hover:text-tertiary-foreground"
                  >
                    {test.completed ? 'Repetir test' : 'Empezar test'} <ArrowRight className="size-4" />
                  </button>
                </div>
              </article>
            ))}
          </div>

          {visibleTests.length === 0 && (
            <div className="mt-5 rounded-2xl border border-dashed border-border bg-neutral p-10 text-center text-sm text-neutral-foreground">
              No hemos encontrado tests con esos criterios.
            </div>
          )}
        </section>

        {/* Llamada a temario */}
        <section className="mt-10 flex flex-col justify-between gap-4 rounded-2xl border border-border bg-tertiary p-6 sm:flex-row sm:items-center">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">Sigue preparando</p>
            <h2 className="mt-1 font-serif text-2xl font-bold text-primary">
              Repasa antes de ponerte a prueba
            </h2>
            <p className="mt-2 text-sm text-tertiary-foreground">
              Consulta el temario y vuelve a los temas que más cuestan.
            </p>
          </div>

          <a
            href={urls.syllabus ?? '/mi-temario'}
            className="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-primary-foreground"
          >
            Ir al temario <ArrowRight className="size-4" />
          </a>
        </section>
      </main>

      {/* <AppFooter /> */}
    </div>
  )
}