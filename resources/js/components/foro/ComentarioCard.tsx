import { usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';
import { type Comentario } from '@/types/models';

import { SectionCard } from '@/components/ui/SectionCard';
import UserAvatar from '@/components/ui/UserAvatar';
import { AccionesContenido } from '@/components/AccionesContenido';
import { publishedSince } from '@/lib/dates';
import { useUntilNow } from '@/hooks/use-until-now';

type ComentarioCardProps = {
    comentario: Comentario;
    id: string;
    onEliminar: () => void;
    onReportar: () => void;
};

/**
 * Comentario del hilo de una publicación
 */
export function ComentarioCard({ comentario, id, onEliminar, onReportar }: ComentarioCardProps) {

    const untilNow = useUntilNow();

    const { auth } = usePage<SharedData>().props;
    const esAutor = comentario.usuario_id === auth.user.id;

    return (
        <SectionCard as="article" className="relative">
            <div id={id} className="flex items-start justify-between gap-4">

                <div className="flex gap-4">
                    <UserAvatar userId={comentario.usuario_id} username={comentario.usuario.name} />

                    <div className="min-w-0">
                        <div className="flex flex-wrap items-baseline gap-x-2">
                            <h3 className="text-sm font-bold">
                                {comentario.usuario.name}
                            </h3>

                            <span className="text-xs text-neutral-foreground">
                                {publishedSince(comentario.created_at, untilNow)}
                            </span>
                        </div>

                        <p className="mt-3 text-sm leading-7 text-neutral-foreground">
                            {comentario.contenido}
                        </p>
                    </div>
                </div>

                <div className="relative z-10 shrink-0">
                    <AccionesContenido
                        esAutor={esAutor}
                        onEliminar={onEliminar}
                        onReportar={onReportar}
                    />
                </div>
            </div>
        </SectionCard>
    );
}
