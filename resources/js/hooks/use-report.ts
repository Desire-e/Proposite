import { useState } from "react";
import { useForm } from "@inertiajs/react";

/**
 * Reportar publicación/usuario/comentario
 */

export function useReport(routeName: string) {

    // Obtener reportable actual
    const [reportableId, setReportableId] = useState<string | null>(null);
    // Formulario de envío
    const form = useForm({ motivo: '' });

    const open = (id: string) => setReportableId(id);
    const close = () => {
        setReportableId(null);
        form.reset();
        form.clearErrors();
    };

    const submit = () => {
        if (!reportableId) return;

        form.post(
            route(routeName, reportableId), 
            { 
                preserveScroll: true, 
                onSuccess: close 
            }
        );
    };

    return { reportableId, open, close, submit, form };
}