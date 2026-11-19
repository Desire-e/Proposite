import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { type Recurso } from '@/types/models';

/**
 * Añadir o editar un recurso de un tema (administrador)
 */
export function useRecursoForm() {

    // Crear: id del tema. 
    // Editar: el recurso. 
    // null = modal cerrado
    const [temaId, setTemaId] = useState<string | null>(null);
    const [recurso, setRecurso] = useState<Recurso | null>(null);

    const form = useForm({ titulo: '', recurso_url: '' });

    const abierto = temaId !== null || recurso !== null;
    const editando = recurso !== null;

    const openCreate = (id: string) => setTemaId(id);

    const openEdit = (r: Recurso) => {
        setRecurso(r);
        // Rellena el formulario con los datos actuales
        form.setData({ titulo: r.titulo, recurso_url: r.recurso_url });
    };

    const close = () => {
        setTemaId(null);
        setRecurso(null);
        
        form.reset();
        form.clearErrors();
    };

    const submit = () => {
        const opciones = { preserveScroll: true, onSuccess: close };

        if (recurso) {
            form.patch(route('admin.temario.recursos.update', recurso.id), opciones);
        } else if (temaId) {
            form.post(route('admin.temario.recursos.store', temaId), opciones);
        }
    };

    return { abierto, editando, openCreate, openEdit, close, submit, form };
}
