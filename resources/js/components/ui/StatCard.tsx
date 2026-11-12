import { type LucideIcon } from 'lucide-react';
import { cn } from '@/lib/utils';
import { SectionCard } from '@/components/ui/SectionCard';

type StatCardProps = {
    value: React.ReactNode;
    label: string;
    icon?: LucideIcon;
    iconClassName?: string;
    variant?: 'card' | 'tile';
};

/**
 * Dato destacado: valor y etiqueta.
 *
 * card: card blanca con icono (tests).
 * tile: recuadro gris compacto, para agrupar varios dentro de otra card (perfil).
 */
export function StatCard({ value, label, icon: Icon, iconClassName = '', variant = 'card' }: StatCardProps) {

    if (variant === 'tile') {
        return (
            <div className="rounded-[11px] bg-neutral px-2 py-3.5 sm:px-3">
                <span className="block font-heading text-2xl font-bold text-primary">
                    {value}
                </span>

                <span className="mt-2 block text-xs">
                    {label}
                </span>
            </div>
        );
    }

    return (
        <SectionCard as="div">
            {Icon && <Icon className={cn('size-5 text-primary', iconClassName)} />}
            <p className="mt-4 text-2xl font-bold">{value}</p>
            <p className="text-sm text-neutral-foreground">{label}</p>
        </SectionCard>
    );
}
