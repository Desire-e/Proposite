import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

/**
 * Enlace "Volver a…" con flecha, a la ruta con nombre @param routeName
 */
export function BackLink({ routeName, id = null, children, className = ''}: 
    { 
    routeName: string, 
    id?: string|null, 
    children: React.ReactNode,
    className?: string,
}) {
    
    return (
        <Link
            href={id ? route(routeName, id) : route(routeName)}
            className={`inline-flex items-center gap-2 text-sm font-semibold text-neutral-foreground
            transition hover:text-primary ${className}`}
        >
            <ArrowLeft className="size-4" />
            {children}
        </Link>
    );
}
