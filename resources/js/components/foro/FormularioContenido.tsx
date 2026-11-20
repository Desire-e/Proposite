import { FormEventHandler } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { Send } from 'lucide-react';
import { type SharedData } from '@/types';

import { SectionCard } from '@/components/ui/SectionCard';
import UserAvatar from '@/components/ui/UserAvatar';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/Textarea';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';

type FormularioContenidoProps = {
    // url a la que se envía el formulario (POST)
    url: string;
    label: string;
    placeholder: string;
    submitLabel: string;
    rows?: number;
    preserveScroll?: boolean;
    className?: string;
};

/**
 * Formulario para escribir un contenido (publicación o comentario),
 * con el avatar del usuario actual
 */
export function FormularioContenido({
    url,
    label,
    placeholder,
    submitLabel,
    rows = 4,
    preserveScroll = true,
    className = '',
}: FormularioContenidoProps) {

    const { auth } = usePage<SharedData>().props;

    const { data, setData, post, processing, errors, clearErrors, reset } =
        useForm<{ contenido: string }>({ contenido: '' });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();

        post(url, {
            preserveScroll,
            onSuccess: () => reset('contenido'),
        });
    };

    return (
        <SectionCard as="div" className={className}>
            <div className="flex flex-col items-start gap-3 md:flex-row">
                <UserAvatar size="sm" userId={auth.user.id} username={auth.user.name} />

                <form className="w-full min-w-0 flex-1" noValidate onSubmit={submit}>
                    <Label htmlFor="contenido" className="sr-only">
                        {label}
                    </Label>

                    <Textarea
                        id="contenido"
                        value={data.contenido}
                        onChange={(e) => {
                            setData('contenido', e.target.value);
                            clearErrors('contenido');
                        }}
                        errors={!!errors.contenido}
                        rows={rows}
                        placeholder={placeholder}
                        disabled={processing}
                        className="resize-none"
                    />
                    <InputError message={errors.contenido} />

                    <div className="mt-3 flex items-center justify-end gap-3">
                        <Button
                            type="submit"
                            // también impide enviar solo espacios
                            disabled={processing || !data.contenido.trim()}
                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-primary
                            px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
                        >
                            <Send className="size-4" />
                            {submitLabel}
                        </Button>
                    </div>
                </form>
            </div>
        </SectionCard>
    );
}
