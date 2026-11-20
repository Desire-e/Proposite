import { type FormEventHandler } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { ShieldCheck, ArrowRight, Check, Users, } from 'lucide-react'
import { AuthLayout } from '@/layouts/AuthLayout';
import { BackLink } from '@/components/ui/BackLink';

import { ComunidadDropdown, type ComunidadOption } from '@/components/admin/ComunidadDropdown';
import { SharedData } from '@/types';


export default function AdminWelcome(
    { comunidades }: { comunidades: ComunidadOption[]}
) {
    
    const { auth } = usePage<SharedData>().props;
    const admin = auth.user;

    const { data, setData, post, processing, errors, clearErrors,} =
        useForm<{oposicion_id: string}>({
            oposicion_id: '',
        });

    // post de este formulario: envía data (oposicion_id) en el cuerpo de la petición
    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(route('admin.select'));
    };


    const selectedOposicion = comunidades.find((c) => (c.id === data.oposicion_id));

    return (
        <AuthLayout headTitle='Área de administración | Proposite' className='mx-auto max-w-4xl'>

            <div className="mt-10 m-5 items-left">
                <BackLink routeName={'logout'} method={'post'}>Volver a inicio</BackLink>
            </div>

            <div className="flex flex-col flex-1 items-center mx-5">
                <div className="w-full max-w-2xl"> 
                    
                    <div className="mx-auto flex size-14 items-center justify-center 
                    rounded-2xl bg-tertiary text-primary">
                        <ShieldCheck className="size-7" />
                    </div>

                    <div className="mt-6 text-center">
                        <p className="text-sm font-bold uppercase tracking-[0.16em] text-secondary">
                            Área de administración
                        </p>

                        <h1 className="mt-3 font-heading text-4xl font-bold text-primary sm:text-5xl">
                            Bienvenido/a {admin.name}
                        </h1>

                        <p className="mx-auto mt-4 max-w-lg  leading-6 text-neutral-foreground">
                            Elige una oposición para acceder a las herramientas de moderación y recursos de su comunidad.
                        </p>
                    </div>


                    <form
                        onSubmit={submit}
                        className="mt-9 rounded-xl border border-border bg-white p-5 shadow-sm sm:p-8"
                    >
                        <div className='flex flex-col gap-2 text-sm font-medium text-foreground'>
                            <ComunidadDropdown
                                comunidades={comunidades}
                                oposicion_id={data.oposicion_id}
                                onChange={(id) => {
                                    setData('oposicion_id', id);
                                    clearErrors('oposicion_id');
                                }}
                                error={errors.oposicion_id}
                                disabled={processing}
                            />
                        </div>

                        {selectedOposicion && (
                        <div className="mt-5 rounded-2xl bg-tertiary p-4">
                            <div className="flex items-start gap-3">
                                <div className="flex size-9 shrink-0 items-center justify-center rounded-xl bg-background text-primary">
                                    <Check className="size-5" />
                                </div>

                                <div className="min-w-0 flex-1">
                                    <p className="text-sm font-bold text-primary">
                                    {selectedOposicion.nombre}
                                    </p>

                                    <p className="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-tertiary-foreground">
                                    <span className="inline-flex items-center gap-1">
                                        <Users className="size-3.5" />
                                        {selectedOposicion.usuarios_count ?? 0} miembros
                                    </span>

                                    <span>
                                        {selectedOposicion.reportes_count ?? 0} reportes pendientes
                                    </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        )}

                        <button
                        type="submit"
                        disabled={!selectedOposicion}
                        className="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 
                        rounded-xl bg-primary px-5 text-sm font-bold text-primary-foreground transition hover:bg-tertiary hover:text-tertiary-foreground disabled:cursor-not-allowed disabled:opacity-45"
                        >
                            Ir a la comunidad
                            <ArrowRight className="size-4" />
                        </button>

                        <p className="mt-4 text-center text-xs text-neutral-foreground">
                            Solo tendrás acceso a las herramientas de la comunidad seleccionada.
                        </p>
                    </form>

                </div>
            </div>
        </AuthLayout>
    )
}