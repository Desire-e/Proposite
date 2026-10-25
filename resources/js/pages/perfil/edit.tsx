import AppLayout from '@/layouts/AppLayout';
import { ArrowLeft, Save } from 'lucide-react'
import { Link, useForm} from '@inertiajs/react';

import { FormEventHandler } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/Textarea';

type EditableUserData = {
    name: string;
    biografia?: string;
    fecha_convocatoria?: string
}

type EditProps = { user: EditableUserData; };

// type Flash = {
//     success?: string;
//     error?: string;
// }

export default function Edit({ user }: EditProps) {

    /** 
     * @isDirty indica si los datos del formulario han 
     * cambiado respecto a sus valores iniciales.
     * @setDefaults establece los datos actuales del formulario 
     * como nuevos valores iniciales. 
     */

    const { data, setData, patch, processing, errors, clearErrors, isDirty, setDefaults, } = useForm<EditableUserData>({
        name: user.name,
        biografia: user.biografia ?? '',
        fecha_convocatoria: user.fecha_convocatoria ?? '',
    }); 


    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        patch(route('perfil.update'), {
            onSuccess: () => {
                setDefaults();
            },
        });
    };


    return (
        <AppLayout headTitle={'Editar perfil | Proposite'}>
            <div className="mx-auto max-w-3xl my-5">
                <Link
                    href={route('perfil.show')}
                    className="inline-flex items-center gap-2 text-sm font-semibold text-neutral-foreground 
                    transition hover:text-primary"
                >
                    <ArrowLeft className="size-4" />
                    Volver a mi perfil
                </Link>

                <section className="mt-8 rounded-2xl border border-border bg-white p-6 shadow-sm sm:p-10">
                    <div className="max-w-xl">
                        <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                            Tu identidad en la comunidad
                        </p>

                        <h1 className="mt-3 font-heading text-4xl font-bold tracking-tight text-primary">
                            Editar perfil
                        </h1>

                        <p className="mt-3 leading-7 text-neutral-foreground">
                            Actualiza la información que compartes con otros opositores.
                        </p>
                    </div>


                    <form className='flex flex-col items-top mt-10 gap-6' onSubmit={submit} noValidate>
                        <div className='flex flex-col gap-2'>
                            <Label htmlFor="name" className='text-sm font-semibold'>
                                Nombre
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                autoComplete="name"
                                defaultValue={user.name}
                                value={data.name}
                                onChange={(e) => {
                                    setData('name', e.target.value)
                                    clearErrors('name');
                                }}
                                errors={errors.name ? true : false}
                                disabled={processing} 
                                placeholder="Nombre"
                            />

                            <InputError message={errors.name} />
                        </div>

                        <div className='flex flex-col gap-2'>
                            <Label htmlFor="biografia" className='text-sm font-semibold'>
                                Sobre mí
                            </Label>

                            <Textarea
                                id="biografia"
                                value={data.biografia}
                                onChange={(e) => {
                                    setData('biografia', e.target.value);
                                    clearErrors('biografia');
                                }}
                                errors={errors.biografia ? true : false}
                                rows={5} 
                                placeholder='Ej.: Preparo la convocatoria de 2026 compaginándolo con trabajo. Busco constancia.'
                                disabled={processing} 
                            >
                            </Textarea>
                        </div>

                        <div className='flex flex-col w-fit gap-2'>
                            <Label htmlFor="fecha-convocatoria" className='text-sm font-semibold'>
                                Fecha de tu convocatoria
                            </Label>

                            <Input
                                id="fecha-convocatoria"
                                type="date"
                                defaultValue={user.fecha_convocatoria}
                                value={data.fecha_convocatoria}
                                onChange={(e) => {
                                    setData('fecha_convocatoria', e.target.value)
                                    clearErrors('fecha_convocatoria');
                                }}
                                errors={errors.fecha_convocatoria ? true : false}
                                disabled={processing} 
                            />

                            <InputError message={errors.fecha_convocatoria} />
                        </div>


                        <div className="flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end">
                            <Link
                                href={route('perfil.show')}
                                className="rounded-xl px-5 py-3 text-center text-sm font-semibold text-neutral-foreground 
                                transition hover:bg-tertiary"
                            >
                                Cancelar
                            </Link>

                            <Button
                                type="submit"
                                disabled={processing || !isDirty}
                                className="inline-flex items-center justify-center gap-2 rounded-xl bg-primary 
                                px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
                            >
                                <Save className="size-4" />
                                Guardar cambios
                            </Button>
                        </div>
                    </form>
                </section>

            </div>
        </AppLayout>
    )
}