import { useRecursoForm } from '@/hooks/use-recurso-form';
import { FeedbackModal } from './ui/FeedbackModal';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error';

export function RecursoModal(
    { recursoForm }: { recursoForm: ReturnType<typeof useRecursoForm> }
) {
    const { form, editando } = recursoForm; 
    // abierto, editando, openCreate, openEdit, close, submit, form

    return (
        <FeedbackModal
            open={recursoForm.abierto}
            tone="normal"
            title={editando ? 'Editar recurso' : 'Nuevo recurso'}
            description={editando ? 'Modifica el título o el enlace.' : 'Añade un enlace de estudio para este tema.'}
            confirmLabel={editando ? 'Guardar' : 'Añadir'}
            processing={form.processing}
            onConfirm={recursoForm.submit}
            onClose={recursoForm.close}
        >
            <div className="mt-4 flex flex-col gap-4">
                <div>
                    <Label htmlFor="titulo">Título</Label>
                    <Input
                        id="titulo"
                        value={form.data.titulo}
                        onChange={(e) => { form.setData('titulo', e.target.value); form.clearErrors('titulo'); }}
                        errors={!!form.errors.titulo}
                        disabled={form.processing}
                    />
                    <InputError message={form.errors.titulo} />
                </div>

                <div>
                    <Label htmlFor="recurso_url">Enlace</Label>
                    <Input
                        id="recurso_url"
                        type="url"
                        placeholder="https://"
                        value={form.data.recurso_url}
                        onChange={(e) => { form.setData('recurso_url', e.target.value); form.clearErrors('recurso_url'); }}
                        errors={!!form.errors.recurso_url}
                        disabled={form.processing}
                    />
                    <InputError message={form.errors.recurso_url} />
                </div>
            </div>
        </FeedbackModal>
    );
}
