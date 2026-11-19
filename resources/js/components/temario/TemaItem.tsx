import { Check, ExternalLink } from 'lucide-react';
import { type Recurso, type Tema } from '@/types/models';
import { SectionCard } from '@/components/ui/SectionCard';
import { AddRecursoButton } from '../AddRecursoButton';
import { RecursoActions } from '../RecursoActions';

type TemaItemProps = {
    tema: Tema;
    disabled: boolean;
    onToggle: () => void;
    esAdmin: boolean;
    onAddRecurso?: () => void;
    onEditRecurso?: (recurso: Recurso) => void;
    onDeleteRecurso?: (id: string) => void;
}


/**
 * Tema del temario: botón para marcarlo como estudiado y sus recursos
 */
export function TemaItem(
{ 
    tema, disabled, onToggle, esAdmin, 
    onAddRecurso, onEditRecurso, onDeleteRecurso 
}
: TemaItemProps
) {

    return (
        <SectionCard
            as="article"
            className={`transition-colors ${tema.checked ? 'border-secondary/50 bg-tertiary/40' : ''}`}
        >
            <div className="flex gap-4">
                {/* si no se mandan acciones admin, significa que no es admin */}
                {!esAdmin && (
                    <button
                        type="button"
                        disabled={disabled}
                        onClick={onToggle}
                        aria-pressed={tema.checked}
                        aria-label={`${tema.checked ? 'Desmarcar' : 'Marcar'} tema ${tema.numero_tema} como estudiado`}
                        className={`mt-0.5 flex size-6 shrink-0 items-center justify-center
                            rounded-lg border-2 transition-colors ${tema.checked ?
                                'border-primary bg-primary text-primary-foreground' :
                                'border-secondary bg-background text-transparent hover:bg-tertiary'
                        }`}
                    >
                        <Check className="size-4" />
                    </button>
                )}
                <div className="min-w-0 flex-1">
                    <div className="flex flex-col justify-between gap-2 sm:flex-row">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-[0.12em] text-secondary">
                                Tema {tema.numero_tema}
                            </p>

                            <h3 className={`mt-1 text-base font-bold ${tema.checked ?
                            'text-primary' : 'text-foreground'}`}>
                                {tema.titulo}
                            </h3>
                        </div>

                        {tema.checked && (
                            <span className="inline-flex h-fit items-center gap-1 rounded-full
                            bg-tertiary px-2.5 py-1 text-[11px] font-bold text-tertiary-foreground">
                                <Check className="size-3" />
                                Estudiado
                            </span>
                        )}
                    </div>

                    <div className="mt-4 flex flex-col items-left gap-3">
                        {tema.recursos?.map((recurso) => (
                            <div key={recurso.id} className='flex flex-wrap items-center gap-3'>
                                <a
                                    href={recurso.recurso_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex max-w-full gap-1
                                    text-xs font-bold text-primary hover:text-secondary"
                                >
                                    {/* shrink-0: los iconos mantienen su tamaño; el que se ajusta es el texto */}
                                    <span className="min-w-0 break-words">{recurso.titulo}</span>
                                    <ExternalLink className="size-3 shrink-0" />
                                </a>
                    
                                {esAdmin && onEditRecurso && onDeleteRecurso && (
                                    <RecursoActions
                                        onEdit={() => onEditRecurso(recurso)}
                                        onDelete={() => onDeleteRecurso(recurso.id)}
                                    />
                                )}
                            </div>
                        ))}

                        {esAdmin && onAddRecurso && 
                            <AddRecursoButton onClick={onAddRecurso} />
                        }
                    </div>
                </div>
            </div>
        </SectionCard>
    );
}
