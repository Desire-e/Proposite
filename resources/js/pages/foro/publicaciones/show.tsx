
import { MessageCircle } from 'lucide-react'
import { Link, usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';
import { type Comentario, type UsuarioBasico } from '@/types/models';

import AppLayout from '@/layouts/AppLayout'
import UserAvatar from '@/components/ui/UserAvatar'
import { SectionCard } from '@/components/ui/SectionCard';
import { SectionHeading } from '@/components/ui/SectionHeading';
import { BackLink } from '@/components/ui/BackLink';
import { AccionesContenido } from '@/components/AccionesContenido';
import { MeSirvioButton } from '@/components/MeSirvioButton';
import { FormularioContenido } from '@/components/foro/FormularioContenido';
import { ComentarioCard } from '@/components/foro/ComentarioCard';

import { publishedSince } from '@/lib/dates';
import { useUntilNow } from '@/hooks/use-until-now';

import { useReport } from '@/hooks/use-report';
import { ReportModal } from '@/components/ReportModal';
import { DeleteModal } from '@/components/DeleteModal';
import { useDelete } from '@/hooks/use-delete';

type Publicacion = {
    id: string;
    oposicion_id: string;
    contenido: string;
    created_at: string;
}

type PublicacionProps = {
    usuario_autor: UsuarioBasico;
    publicacion: Publicacion;
    me_sirvio: number;
    me_sirvio_activo: boolean;
    comentarios?: Comentario[];
}

export default function Publicacion({
    usuario_autor, publicacion, me_sirvio, me_sirvio_activo, comentarios = []
}: PublicacionProps) {

    /** Eliminar publicación propia */
    const deletePublicacion = useDelete('foro.publicaciones.delete');

    /** Reportar publicación ajena */
    const reportPublicacion = useReport('foro.publicaciones.reportes.store');

    /** Eliminar comentario propio */
    const deleteComentario = useDelete('foro.comentarios.delete')

    /** Reportar comentario ajeno */
    const reportComentario = useReport('foro.comentarios.reportes.store');

    /** Saber si la publicación es propia o ajena */
    const { auth } = usePage<SharedData>().props;
    const esAutor = usuario_autor.id === auth.user.id;

    const untilNow = useUntilNow();

    return (
        <AppLayout headTitle="Publicación | Proposite">
            {/* Modal eliminar publicación */}
            <DeleteModal deletion={deletePublicacion} title="¿Eliminar esta publicación?" />

            {/* Modal reportar publicación */}
            <ReportModal report={reportPublicacion} title="Reportar publicación" />

            {/* Modal eliminar comentario */}
            <DeleteModal deletion={deleteComentario} title="¿Eliminar este comentario?" />

            {/* Modal reportar comentario */}
            <ReportModal report={reportComentario} title="Reportar comentario" />


            <div className="mx-auto max-w-4xl px-6 py-8 lg:px-8 lg:py-10">
                <BackLink routeName="foro.index">Volver al foro</BackLink>

                <SectionCard as="article" className="mt-4 rounded-3xl p-6 shadow-sm sm:p-9">
                    <div className="flex items-start justify-between gap-4">
                        <div className="flex items-center gap-3">

                            <UserAvatar
                                username={usuario_autor.name}
                                userId={usuario_autor.id}
                            />

                            <div>
                                <h1 className="text-base font-bold">{usuario_autor.name}</h1>

                                <p className="mt-1 text-xs text-neutral-foreground">
                                    {publishedSince(publicacion.created_at, untilNow)}
                                </p>
                            </div>
                        </div>

                        {/* Eliminar / Reportar publicación */}
                        <div className='relative z-10'>
                            <AccionesContenido
                                esAutor={esAutor}
                                onEliminar={() => deletePublicacion.open(publicacion.id)}
                                onReportar={() => reportPublicacion.open(publicacion.id)}
                            />
                        </div>
                    </div>

                    <p className="mt-5 text-base leading-7 text-foreground">
                        {publicacion.contenido}
                    </p>

                    <div style={{visibility: 'hidden'}} id="comentarios"></div>

                    <div className="mt-5 flex flex-wrap items-center gap-7 border-t border-border pt-5">
                        <MeSirvioButton 
                            publicacionId={publicacion.id}
                            activo={me_sirvio_activo}
                            total={me_sirvio}
                            variant='full'
                        />

                        <Link
                            href={`${route('foro.publicaciones.show', publicacion.id)}#comentarios`}
                            className="inline-flex items-center gap-2 rounded-xl transition
                            text-sm font-semibold text-neutral-foreground hover:text-primary"
                        >
                            <MessageCircle className="size-4" />
                            {comentarios.length} Comentarios
                        </Link>
                    </div>
                </SectionCard>

                <section className="mt-10">
                    <SectionHeading eyebrow="Hilo de conversación" title="Comentarios" size="lg" />

                    <FormularioContenido
                        url={route('foro.publicaciones.comentarios.store', publicacion.id)}
                        label="Escribe un comentario"
                        placeholder="Responde a la comunidad..."
                        submitLabel="Responder"
                        className="mt-6 bg-tertiary p-4"
                    />

                    {/* TODO. Message status - accesibilidad */}

                    {/* Comentarios */}

                    <div className="mt-6 space-y-4">
                        {comentarios.map((comentario) => (
                            <ComentarioCard
                                id={`comentario-${comentario.id}`} 
                                key={comentario.id}
                                comentario={comentario}
                                onEliminar={() => deleteComentario.open(comentario.id)}
                                onReportar={() => reportComentario.open(comentario.id)}
                            />
                        ))}
                    </div>
                </section>
            </div>
        </AppLayout>
  )
}
