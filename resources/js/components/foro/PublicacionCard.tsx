import { Link, usePage } from '@inertiajs/react';
import { MessageCircle } from 'lucide-react';
import { type SharedData } from '@/types';
import { type PublicacionFeed } from '@/types/models';

import { SectionCard } from '@/components/ui/SectionCard';
import UserAvatar from '@/components/ui/UserAvatar';
import { AccionesContenido } from '@/components/AccionesContenido';
import { MeSirvioButton } from '@/components/MeSirvioButton';
import { publishedSince } from '@/lib/dates';
import { useUntilNow } from '@/hooks/use-until-now';


type PublicacionCardProps = {
    publicacion: PublicacionFeed;
    onEliminar: () => void;
    onReportar: () => void;
};

/**
 * Publicación del listado del foro.
 * Toda la card enlaza a la publicación; avatar, menú y acciones
 * quedan por encima del enlace (z-10) para seguir siendo clicables.
 */
export function PublicacionCard({ publicacion, onEliminar, onReportar }: PublicacionCardProps) {

    const { auth } = usePage<SharedData>().props;
    const esAutor = publicacion.usuario.id === auth.user.id;

    const urlPublicacion = route('foro.publicaciones.show', publicacion.id);

    const untilNow = useUntilNow();

    return (
        <SectionCard as="article" className="relative">
            {/* Cabecera: avatar, fecha y menú siempre en la misma fila */}
            <div className="flex items-start gap-3">
                <div className="relative z-10 shrink-0">
                    <UserAvatar
                        userId={publicacion.usuario.id}
                        username={publicacion.usuario.name}
                    />
                </div>

                <p className="mt-0.5 min-w-0 flex-1 text-xs text-neutral-foreground">
                    {publishedSince(publicacion.created_at, untilNow)}
                </p>

                <div className="relative z-10 shrink-0">
                    <AccionesContenido
                        esAutor={esAutor}
                        onEliminar={onEliminar}
                        onReportar={onReportar}
                    />
                </div>
            </div>

            {/* Cuerpo: ancho completo en móvil, alineado con la fecha en escritorio
                (pl-15 = avatar size-12 + gap-3) */}
            <div className="md:pl-15 md:-mt-7">
                <div className="min-w-0">
                    <Link
                        href={urlPublicacion}
                        className="block after:absolute after:inset-0 after:content-['']"
                    >
                        <p className="mt-4 text-sm leading-7 text-foreground">{publicacion.contenido}</p>
                    </Link>

                    {/* Acciones de la publicación */}
                    <div className="relative z-10 mt-5 flex items-center gap-5
                    border-t border-border pt-4 text-xs text-neutral-foreground">
                        <MeSirvioButton
                            publicacionId={publicacion.id}
                            activo={publicacion.me_sirvio_activo}
                            total={publicacion.me_sirvio_count}
                        />

                        <Link
                            href={`${urlPublicacion}#comentarios`}
                            className="inline-flex items-center rounded-xl transition gap-1.5
                            text-xs text-neutral-foreground hover:text-primary"
                        >
                            <MessageCircle className="size-4" />
                            {publicacion.comentarios_count}
                        </Link>
                    </div>
                </div>
            </div>
        </SectionCard>
    );
}
