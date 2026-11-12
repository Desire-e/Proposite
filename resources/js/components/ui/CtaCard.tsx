import { Link } from '@inertiajs/react';
import { ArrowRight, type LucideIcon } from 'lucide-react';
import { cn } from '@/lib/utils';
import { SectionCard } from '@/components/ui/SectionCard';

type CtaCardProps = {
    title: string;
    description: string;
    link: { label: string, route: string };
    brow?: string;
    icon?: LucideIcon;
    size?: 'md' | 'lg';
    // enlace con aspecto de botón (por defecto, enlace de texto)
    boton?: boolean;
    className?: string;
};

/**
 * Card de llamada a la acción: título, texto y enlace con flecha
 */
export function CtaCard({
    title,
    description,
    link,
    brow,
    icon,
    size = 'md',
    boton = false,
    className = '',
}: CtaCardProps) {
    return (
        <SectionCard
            brow={brow}
            title={title}
            icon={icon}
            size={size}
            className={cn('bg-tertiary text-primary', className)}
        >
            <p className="mt-2 text-sm leading-6 text-tertiary-foreground">
                {description}
            </p>

            <Link
                href={route(link.route)}
                className={boton
                    ? 'mt-4 inline-flex w-fit items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-primary-foreground transition hover:opacity-90'
                    : 'mt-4 inline-flex items-center gap-2 text-sm font-bold text-primary transition hover:text-tertiary-foreground'
                }
            >
                {link.label}
                <ArrowRight className="size-4" />
            </Link>
        </SectionCard>
    );
}
