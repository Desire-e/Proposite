import { useState } from "react";
import { useForm } from "@inertiajs/react";

/**
 * Eliminar publicación/usuario/comentario
 */

export function useDelete(routeName: string) {

    // Obtener eliminable actual
    const [eliminableId, setEliminableId] = useState<string | null>(null);
    
    // Formulario de envío
    const form = useForm({});

    const open = (id: string) => setEliminableId(id);
    const close = () => { setEliminableId(null); };
    
    const submit = () => {
        if (!eliminableId) return;

        form.delete(
            route(routeName, eliminableId), 
            { 
                preserveScroll: true,
                // only: ['publicaciones'],
                onFinish: close,
            }
        );
    };

    return { eliminableId, open, close, submit, form };
}