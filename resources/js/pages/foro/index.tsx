import { Bell, UserRound, } from 'lucide-react';
import { type Oposicion, type PublicacionFeed } from '@/types/models';

import AppLayout from '@/layouts/AppLayout';
import { PageHero } from '@/components/PageHero';
import { FormularioContenido } from '@/components/foro/FormularioContenido';
import { PublicacionCard } from '@/components/foro/PublicacionCard';
import { SectionCard } from '@/components/ui/SectionCard';
import { SectionHeading } from '@/components/ui/SectionHeading';
import { CtaCard } from '@/components/ui/CtaCard';

import { ReportModal } from "@/components/ReportModal";
import { useReport } from "@/hooks/use-report";
import { DeleteModal } from '@/components/DeleteModal';
import { useDelete } from '@/hooks/use-delete';


type ForoProps = {
    oposicion: Oposicion;
    publicaciones?: PublicacionFeed[];
    miembros: number;
}


export default function Feed(
    { oposicion, publicaciones = [], miembros = 0 }: ForoProps
) {

    /**
     * Buscador y filtros
     */
    // const [query, setQuery] = useState('')
    // const [activeFilter, setActiveFilter] = useState('Más recientes')

    // const visiblePosts = useMemo(
    //     () =>
    //         posts.filter((post) =>
    //             `${post.name} ${post.content} ${post.tag ?? ''}`
    //                 .toLowerCase()
    //                 .includes(query.toLowerCase()),
    //         ),
    //     [posts, query],
    // )


    /**
     * Eliminar publicación propia
     */
    const deletePublicacion = useDelete('foro.publicaciones.delete');

    /**
     * Reportar publicación ajena
     */
    const reportPublicacion = useReport('foro.publicaciones.reportes.store');


    return (
        <AppLayout headTitle={`Foro de ${oposicion.nombre} | Proposite`}>

            {/* Modal eliminar publicación */}
            <DeleteModal deletion={deletePublicacion} title="¿Eliminar esta publicación?" />

            {/* Modal reportar publicación */}
            <ReportModal report={reportPublicacion} title="Reportar publicación" />

            <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">

                <PageHero
                    eyebrow="Comunidad"
                    title={`Foro de ${oposicion.nombre}`}
                    description="Un espacio para resolver dudas, compartir recursos y avanzar acompañado con personas que preparan la misma oposición."
                >
                    <div className="mt-5 flex flex-wrap gap-3">
                        <span className="rounded-full bg-background px-3 py-1.5 text-xs font-semibold
                        text-tertiary-foreground">
                            {miembros.toLocaleString('es-ES')} opositores
                        </span>
                    </div>
                </PageHero>


                {/* Contenido principal */}
                <div className="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <section>

                        {/* Crear publicación */}
                        <FormularioContenido
                            url={route('foro.publicaciones.store')}
                            label="Crear una publicación"
                            placeholder="¿Qué quieres compartir con tu comunidad?"
                            submitLabel="Publicar"
                            rows={6}
                            preserveScroll={false}
                        />

                        {/* Conversaciones */}
                        <div className="mt-7 flex flex-col gap-4">
                            <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                <SectionHeading eyebrow="Conversaciones" title="Lo que está pasando" />

                                {/* TODO. Buscador y filtros */}

                                {/* <div className="flex items-center gap-2">
                                    <label className="flex items-center gap-2 rounded-xl border border-border bg-background px-3 py-2">
                                        <Search className="size-4 text-neutral-foreground" />

                                        <span className="sr-only">
                                            Buscar publicaciones
                                        </span>

                                        <input
                                            value={query}
                                            onChange={(event) =>
                                                setQuery(event.target.value)
                                            }
                                            className="w-32 bg-transparent text-xs outline-none placeholder:text-neutral-foreground"
                                            placeholder="Buscar"
                                        />
                                    </label>

                                    <button
                                        type="button"
                                        onClick={() =>
                                            setActiveFilter(
                                                activeFilter === 'Más recientes'
                                                    ? 'Más populares'
                                                    : 'Más recientes',
                                            )
                                        }
                                        className="inline-flex items-center gap-1 rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold text-neutral-foreground"
                                    >
                                        {activeFilter}
                                        <ChevronDown className="size-3" />
                                    </button>
                                </div> */}
                            </div>

                            {/* Listado de publicaciones */}
                            {publicaciones.map((publicacion) => (
                                <PublicacionCard
                                    key={publicacion.id}
                                    publicacion={publicacion}
                                    onEliminar={() => deletePublicacion.open(publicacion.id)}
                                    onReportar={() => reportPublicacion.open(publicacion.id)}
                                />
                            ))}
                        </div>
                    </section>

                    {/* Barra lateral */}
                    <aside className="space-y-4">
                        {/* Normas de la comunidad */}
                        <SectionCard icon={UserRound} title="Normas de la comunidad" size="md">
                            <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                                Un lugar seguro para preguntar, compartir y apoyarnos durante la preparación.
                            </p>

                            <ul className="mt-4 mx-3 space-y-3 text-xs leading-5 list-disc text-neutral-foreground">
                                <li>Respeta los ritmos y experiencias de los demás.</li>
                                <li>Comparte información útil y contrastada.</li>
                                <li>No publiques datos personales.</li>
                            </ul>
                        </SectionCard>

                        {/* Acceso al temario */}
                        <CtaCard
                            icon={Bell}
                            title="¿Necesitas ayuda?"
                            description="Explora el temario oficial y participa en conversaciones sobre tus temas."
                            link={{ label: 'Ir al temario', route: 'temario.index' }}
                        />
                    </aside>
                </div>

            </div>

        </AppLayout>

    )
}
