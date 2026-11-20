import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { type ReactNode } from 'react';

type BackLinkProps = {
    routeName: string;
    id?: string | null;
    children: ReactNode;
    className?: string;
    // 'post' para acciones como cerrar sesión (routeName="logout")
    method?: 'get' | 'post';
};

/**
 * Enlace "Volver a…" con flecha, a la ruta con nombre @param routeName
 */
export function BackLink({ routeName, id = null, children, className = '', method = 'get' }: BackLinkProps) {
    return (
        <Link
            href={id ? route(routeName, id) : route(routeName)}
            method={method}
            // Un POST no debe ser un <a>: se renderiza como <button>
            as={method === 'get' ? 'a' : 'button'}
            className={`inline-flex items-center gap-2 text-sm font-semibold text-neutral-foreground
            transition hover:text-primary ${className}`}
        >
            <ArrowLeft className="size-4" />
            {children}
        </Link>
    );
}
