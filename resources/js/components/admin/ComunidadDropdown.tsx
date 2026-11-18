import { useState } from 'react';
import { BookOpen, ChevronDown } from 'lucide-react';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import InputError from '@/components/input-error';

/** Oposición en el selector de comunidad del administrador */

export type ComunidadOption = {
    id: string;
    nombre: string;
    usuarios_count?: number;
    reportes_count?: number;
}

type ComunidadDropdownProps = {
    comunidades: ComunidadOption[];
    
    // id de la comunidad elegida ('' si aún no hay ninguna)
    oposicion_id: string;

    // función al elegir: cada página decide (welcome guarda 
    // en el formulario, panel envía el cambio directamente)
    onChange: (id: string) => void;
    error?: string;
    disabled?: boolean;
    variant?: 'compact' | 'full';
};


/**
 * Selector de la comunidad (oposición) que modera el administrador.
 * No guarda la selección: solo muestra `oposicion_id` y avisa con `onChange`.
 */
export function ComunidadDropdown(
    { comunidades, oposicion_id, onChange, error, disabled = false, variant = 'full' }: 
    ComunidadDropdownProps
) {

    // Estado abierto/cerrado
    const [open, setOpen] = useState(false);

    // Opción seleccionada
    const seleccionada = comunidades.find((c) => c.id === oposicion_id);


    // Estilos de variantes
    const base = `inline-flex justify-between items-center bg-white appearance-none 
    rounded-sm text-sm border border-border outline-none transition focus:border-primary 
    focus:ring-2 focus:ring-ring/30 disabled:cursor-not-allowed disabled:opacity-50 
    md:h-fit px-3 gap-2`;

    const styles = {
        compact: `md:py-0 py-2 w-full md:max-w-[50%]`,
        full: `py-3 w-full`,
    };


    return (
        <>
            <DropdownMenu open={open} onOpenChange={setOpen}>
                <DropdownMenuTrigger asChild>
                    <button
                        type="button"
                        disabled={disabled}
                        aria-invalid={Boolean(error)}
                        aria-label="Elegir oposición"
                        className={`${base} ${styles[variant]}
                        ${error 
                            ? `border-error-foreground! placeholder:text-error-foreground! focus:border-2!` 
                            : ``
                        }`}
                    >
                        <div className='flex gap-2 items-center text-left'>
                            <BookOpen
                                className="pointer-events-none size-5 text-secondary mr-2 my-2"
                                strokeWidth={2}
                            />
                            {seleccionada?.nombre ?? 'Selecciona una oposición'}
                        </div>

                        <ChevronDown className="text-foreground! size-6 md:size-5" strokeWidth={1.5} />
                    </button>
                </DropdownMenuTrigger>

                <DropdownMenuContent
                    className="bg-white border-1 rounded-sm border-muted-foreground/30"
                    align="center"
                >
                    {comunidades.map((c) => (
                        <DropdownMenuItem
                            className='hover:bg-background text-foreground md:text-sm text-base
                            focus:bg-hover focus:text-accent-foreground'
                            key={c.id}
                            onSelect={() => onChange(c.id)}
                        >
                            {c.nombre}
                        </DropdownMenuItem>
                    ))}
                </DropdownMenuContent>
            </DropdownMenu>

            <InputError className='!font-normal' message={error} />
        </>
    );
}
