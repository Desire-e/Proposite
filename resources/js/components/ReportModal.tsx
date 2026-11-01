import { useReport } from '@/hooks/use-report';
import { FeedbackModal } from './ui/FeedbackModal';
import { Label } from "@/components/ui/label";
import Textarea from "@/components/ui/Textarea";
import InputError from "@/components/input-error";

export function ReportModal(
    { report, title }: 
    { report: ReturnType<typeof useReport>; title: string }
) {

    const { form } = report;

    return (
        <FeedbackModal
            // abierto si hay id publicación en el estado
            open={report.reportableId !== null}
            tone="warning"
            title={title}
            description="Cuéntanos qué ocurre. Un moderador lo revisará."
            confirmLabel="Enviar reporte"
            processing={form.processing}
            onConfirm={report.submit}
            onClose={report.close}
        >
            <div className="mt-4">
                <Label htmlFor="motivo" className="sr-only">Motivo del reporte</Label>
                <Textarea
                    id="motivo"
                    rows={4}
                    value={form.data.motivo}
                    onChange={(e) => { 
                        form.setData('motivo', e.target.value); 
                        form.clearErrors('motivo'); 
                    }}
                    errors={!!form.errors.motivo}
                    placeholder="Describe el motivo del reporte"
                    disabled={form.processing}
                />
                <InputError message={form.errors.motivo} />
            </div>
        </FeedbackModal>
    );
}