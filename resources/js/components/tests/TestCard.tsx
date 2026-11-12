import { Link } from '@inertiajs/react';
import { ArrowRight, CheckCircle2, Clock3 } from 'lucide-react';
import { type TestListado } from '@/types/models';

import { SectionCard } from '@/components/ui/SectionCard';
import { duracionRecomendada } from '@/lib/tests';

/**
 * Test del listado de tests disponibles
 */
export function TestCard({ test }: { test: TestListado }) {

    // != null: una nota de 0 también cuenta como test realizado
    const realizado = test.ultima_nota != null;

    return (
        <SectionCard
            as="article"
            className="group transition hover:-translate-y-0.5 hover:border-primary/50 hover:shadow-md"
        >
            <div className="flex items-start justify-between gap-4">
                <span className={`inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold
                ${realizado
                    ? 'bg-success text-success-foreground'
                    : 'text-tertiary-foreground bg-tertiary'
                }`}>
                    {realizado
                        ? (<><CheckCircle2 className="size-4" /> Completado</>)
                        : (<>Disponible</>)
                    }
                </span>
            </div>

            <h3 className="mt-5 text-lg font-bold">{test.titulo}</h3>
            <p className="mt-1 text-sm text-neutral-foreground">{test.descripcion}</p>

            <div className="mt-5 flex flex-wrap gap-3 text-xs text-neutral-foreground">
                <span>{test.preguntas_count} preguntas</span>

                {/* TODO. Temporizador de tests y nivel de dificultad */}

                <span className="inline-flex items-center gap-1">
                    <Clock3 className="size-3.5" /> {duracionRecomendada(test.preguntas_count)}
                </span>
            </div>

            <div className="mt-5 flex items-center justify-between border-t border-border pt-4">
                <span className="text-xs text-neutral-foreground">
                    {test.intentos_comunidad_count} intentos de la comunidad
                    {realizado && ` · Tu última nota ${test.ultima_nota}`}
                </span>

                <Link
                    href={route('tests.show', test.id)}
                    className="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5
                    text-xs font-bold text-primary-foreground transition hover:bg-tertiary
                    hover:text-tertiary-foreground"
                >
                    {realizado ? 'Repetir test' : 'Empezar test'}
                    <ArrowRight className="size-4" />
                </Link>
            </div>
        </SectionCard>
    );
}
