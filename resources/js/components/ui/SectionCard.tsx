import { Link } from '@inertiajs/react';
import { ArrowRight, type LucideIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

type SectionCardProps = {
    as?: 'section' | 'article' | 'div';
    brow?: string;
    title?: string;
    icon?: LucideIcon;
    link?: { label: string, route: string } | null;
    size?: 'md' | 'lg' | 'xl';
    className?: string;
    id?: string;
    children?: React.ReactNode;
};

const titleSizes = {
    md: 'text-xl',
    lg: 'text-2xl',
    xl: 'text-2xl lg:text-3xl',
};

/**
 * Card blanca redondeada de la aplicación.
 *
 * Cabecera opcional: icono, antetítulo, título y enlace a la derecha.
 * Sin título, es solo el contenedor de children.
 *
 * El título hereda el color de la card: para cambiarlo,
 * pasa el color en className (p. ej. 'bg-tertiary text-primary').
 */
export function SectionCard({
    as: Tag = 'section', 
    brow, 
    title,
    icon: Icon, 
    link = null, 
    size = 'lg', 
    className = '', 
    id = '',
    children,
}: SectionCardProps) {
    return (
        <Tag className={cn('rounded-2xl border border-border bg-white p-5', className)} id={id}>
            {Icon && (
                <div className="mb-4 flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <Icon className="size-5" />
                </div>
            )}

            {title && (
                <div className="flex items-start justify-between gap-3">
                    <div>
                        {brow && (
                            <span className="text-xs font-extrabold uppercase tracking-[0.13em] text-secondary">
                                {brow}
                            </span>
                        )}
                        <h2 className={cn(
                            'mb-0 font-heading font-bold tracking-[-0.04em]',
                            titleSizes[size],
                            brow && 'mt-2',
                        )}>
                            {title}
                        </h2>
                    </div>

                    {link &&
                        <Link
                            href={route(link.route)}
                            className="inline-flex shrink-0 items-center gap-1 text-sm font-bold text-primary"
                        >
                            {link.label}
                            <ArrowRight size={14} />
                        </Link>
                    }
                </div>
            )}

            {children}

        </Tag>
    );
}
