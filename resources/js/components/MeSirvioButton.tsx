import { Heart } from 'lucide-react';
import { router } from '@inertiajs/react';
import { useState } from 'react';

type MeSirvioButtonProps = {
    publicacionId: string;
    activo: boolean;
    total: number;
    variant?: 'compact' | 'full';
    disabled?: boolean;
};

/** 
 * Marca o desmarca "me sirvió" actualizando la interfaz 
 * antes de recibir respuesta (optimista) 
 */
export function MeSirvioButton({ publicacionId, activo, total, variant = 'compact', disabled = false }: MeSirvioButtonProps) {
    
    // Estado local: corazón activo, total
    const [isActivo, setIsActivo] = useState(activo);
    const [totalActivo, setTotalActivo] = useState(total);


    const toggle = () => {
        // Estado siguiente: si estaba activo, se desactiva y baja el total;
        // si no, se activa y sube el total
        const siguienteActivo = !isActivo;
        const siguienteTotal = siguienteActivo ? totalActivo + 1 : totalActivo - 1;

        // Cambio optimista: la interfaz se actualiza antes de la respuesta
        setIsActivo(siguienteActivo);
        setTotalActivo(siguienteTotal);

        router.post(
            route('foro.publicaciones.me-sirvio.toggle', publicacionId),
            {},
            {
                preserveScroll: true,
                onError: () => {
                    // Si servidor rechazó la acción, deshace cambios
                    setIsActivo(isActivo);
                    setTotalActivo(totalActivo);
                },
            },
        );
    };


    // Estilos
    const base = 'inline-flex items-center gap-2 rounded-xl transition';
    const styles = {
        compact: `gap-1.5 text-xs ${isActivo ? 'font-bold text-primary' : 'text-neutral-foreground hover:text-primary'}`,
        full: `text-sm font-semibold ${isActivo ? 'text-primary' : 'text-neutral-foreground hover:text-primary'}`,
    };

    return (
        <button
            type="button"
            onClick={toggle}
            disabled={disabled}
            aria-pressed={isActivo}
            className={`${base} ${styles[variant]}`}
        >
            <Heart className="size-4" fill={isActivo ? 'currentColor' : 'none'} />
            <span>{totalActivo}</span>
            {variant === 'full' && <span>Me sirvió</span>}
        </button>
    );
}