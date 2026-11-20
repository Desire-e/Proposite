import { useDelete } from '@/hooks/use-delete';
import { FeedbackModal } from './ui/FeedbackModal';

export function DeleteModal(
    { deletion, title }: 
    { deletion: ReturnType<typeof useDelete>; title: string }
) {

    const { form } = deletion;

    return (
        <FeedbackModal
            open={deletion.eliminableId !== null}
            tone="danger"
            title={title}
            description="La eliminación se hará de forma permanente."
            confirmLabel="Eliminar"
            processing={form.processing}
            onConfirm={deletion.submit}
            onClose={deletion.close}
        />
    );
}