import { ArrowRight, Ban, Check, Flag, MessageCircle, Trash2, Users } from 'lucide-react';
import { formatDMY } from '@/lib/dates';
import { Reporte } from '@/types/models';
import { Link } from '@inertiajs/react';

type ReportePendienteCardProps = {
    reporte: Reporte;
    onOmitir?: (reporte: Reporte) => void;
    onEliminar?: (reporte: Reporte) => void;
    onSuspender?: (reporte: Reporte) => void;
};

/** Icono según el reportable tipo */
function iconoReportable(tipo: string) {
    if (tipo === 'usuario') return Users;
    if (tipo === 'comentario') return MessageCircle;
    if (tipo === 'publicacion') return MessageCircle;
    return Flag;
}

/** Etiqueta legible según el tipo de reportable */
function etiquetaReportable(tipo: string) {
    if (tipo === 'usuario') return 'Usuario';
    if (tipo === 'comentario') return 'Comentario';
    if (tipo === 'publicacion') return 'Publicación';

    return 'Contenido';
}

export default function ReportePendienteCard(
    { reporte, onOmitir, onEliminar, onSuspender }
    : ReportePendienteCardProps
) {

    const Icon = iconoReportable(reporte.reportable_tipo);

    return (
        <article className="p-5 rounded-xl bg-white border border-border">
            <div className="flex gap-4">
                <div className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-tertiary text-primary">
                    <Icon className="size-5" />
                </div>

                <div className="flex flex-col gap-2 min-w-0 flex-1">
                    <span className="text-sm font-bold text-tertiary-foreground">
                        {etiquetaReportable(reporte.reportable_tipo)}
                    </span>

                    <p className="text-sm">{reporte.motivo}</p>

                    <p className="text-xs text-neutral-foreground">
                        {formatDMY(reporte.created_at)}
                    </p>

                    <Link 
                        // TODO poner route() a la publicacion/perfil
                        href='#ejemplo'
                        className="inline-flex items-center gap-2 mt-2 text-xs font-medium 
                        text-tertiary-foreground"
                    >
                        Ver {etiquetaReportable(reporte.reportable_tipo)} <ArrowRight className="size-3"/>
                    </Link>
                </div>
            </div>


            <div className="mt-4 flex flex-wrap justify-end gap-2">
                <button
                    type="button"
                    onClick={() => onOmitir?.(reporte)}
                    className="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 text-xs font-semibold 
                    text-neutral-foreground transition hover:bg-tertiary"
                >
                    <Check className="size-3.5" />
                    Omitir
                </button>

                <button
                    type="button"
                    onClick={() => onEliminar?.(reporte)}
                    className="inline-flex items-center gap-1.5 rounded-lg border border-error! bg-error text-error-foreground 
                    px-3 py-2 text-xs font-semibold transition hover:bg-neutral hover:text-error-foreground hover:border 
                    hover:border-error-foreground!"
                >
                    <Trash2 className="size-3.5" />
                    Eliminar contenido
                </button>

                <button
                    type="button"
                    onClick={() => onSuspender?.(reporte)}
                    className="inline-flex items-center gap-1.5 rounded-lg bg-error-foreground px-3 py-2 text-xs font-semibold 
                    text-primary-foreground border border-error-foreground transition hover:bg-neutral hover:text-error-foreground 
                    hover:border hover:border-error-foreground!"
                >
                    <Ban className="size-3.5" />
                    Suspender usuario
                </button>
            </div>
        </article>
    );
}
