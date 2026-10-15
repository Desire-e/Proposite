import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Link, useForm } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/Textarea';
import UserAvatar from '@/components/ui/UserAvatar';

import AppLayout from '@/layouts/AppLayout';
import PageLayout from '@/layouts/PageLayout';

type UserProfile = {
    name: string;
    email: string;
    biografia?: string;
    fecha_convocatoria?: string;    
    tests: number;
    publicaciones: number;
    respuestas: number;
}

type UpdateUserForm = {
    name?: string;
    biografia?: string;
    fecha_convocatoria?: string;
}

// type Flash = {
//     success?: string;
//     error?: string;
// }

export default function Perfil(
    { user, /*flash*/ } : 
    { user: UserProfile, /*flash?: Flash*/}
) {
    /** 
     * @isDirty indica si los datos del formulario han 
     * cambiado respecto a sus valores iniciales.
     * @setDefaults establece los datos actuales del formulario 
     * como nuevos valores iniciales. 
     */

    const { data, setData, post, processing, errors, clearErrors, isDirty, setDefaults, } = useForm<UpdateUserForm>({
        name: user.name,
        biografia: user.biografia ?? '',
        fecha_convocatoria: user.fecha_convocatoria ?? '',
    }); 

    const errorsInputStyle: string = 'border-error! placeholder:text-error! focus:border-2!'; 

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('perfil.update'), {
            onSuccess: () => {
                setDefaults();
            },
        });
    };


    return (
        <>
            <AppLayout headTitle={"Perfil | Proposite"}>
                <PageLayout className='lg:w-[50%]'>

                    {/* INFO USUARIO */}
                    
                    <section className='flex items-top gap-5'>
                        <UserAvatar
                            name={user.name}
                            size="lg"
                        />

                        <div className='flex flex-col'>
                            <h1 className='text-3xl font-heading font-medium'>{user.name}</h1>
                            <p className='text-sm text-muted-foreground'>{user.email}</p>
                        </div>
                    </section>

                    {/* TODO. */}
                    <section className='flex items-top justify-between '>
                        <div className='w-[30%] p-4 space-y-2 bg-muted-background/80
                        border-muted-foreground/20 border-1 rounded-md'>
                            <p className='text-muted-foreground text-xs tracking-widest'>TESTS REALIZADOS</p>
                            <p className='text-3xl font-heading'>{user.tests}</p>
                        </div>

                        <div className='w-[30%] p-4 space-y-2 bg-muted-background/80
                        border-muted-foreground/20 border-1 rounded-md'>
                            <p className='text-muted-foreground text-xs tracking-widest'>PUBLICACIONES</p>
                            <p className='text-3xl font-heading'>{user.publicaciones}</p>
                        </div>
                        
                        <div className='w-[30%] p-4 space-y-2 bg-muted-background/80
                        border-muted-foreground/20 border-1 rounded-md'>
                            <p className='text-muted-foreground text-xs tracking-widest'>RESPUESTAS</p>
                            <p className='text-3xl font-heading'>{user.respuestas}</p>
                        </div>
                    </section>

                    {/* CONFIGURACIÓN */}

                    <section className='flex flex-col gap-5 items-top px-6 py-7 
                    bg-muted-background/80 border-muted-foreground/20 border-1 rounded-md'>
                        <div className='space-y-2'>
                            <h2 className='text-xl font-heading'>Datos del opositor</h2>
                            <p className='text-sm text-muted-foreground'>Así te verán los demás en el foro de tu oposición.</p>
                        </div>

                        <form className='flex flex-col gap-5 items-top' onSubmit={submit} noValidate>
                            <div className='flex flex-col gap-3'>
                                <Label htmlFor="name" className='text-sm font-medium'>Nombre</Label>
                                <Input
                                    className={`bg-transparent ${errors.name ? errorsInputStyle : ''}`}
                                    id="name"
                                    type="text"
                                    autoComplete="name"
                                    defaultValue={user.name}
                                    value={data.name}
                                    onChange={(e) => {
                                        setData('name', e.target.value)
                                        clearErrors('name');
                                    }}
                                    disabled={processing} 
                                    placeholder="Nombre"
                                />

                                <InputError message={errors.name} />
                            </div>

                            {/* TODO. CREA COMPO TEXTAREA */}
                            {/* Biografía */}
                            <div className='flex flex-col gap-3'>
                                <Label htmlFor="biografia" className='text-sm font-medium'>Sobre mí</Label>
                                
                                <Textarea
                                    id="biografia"
                                    value={data.biografia}
                                    onChange={(e) => {
                                        setData('biografia', e.target.value);
                                        clearErrors('biografia');
                                    }}
                                    className='bg-transparent'
                                    rows={5} 
                                    placeholder='Ej.: Preparo la convocatoria de 2026 compaginándolo con trabajo. Busco constancia.'
                                    disabled={processing} 
                                >
                                </Textarea>
                            </div>

                            <div className='flex flex-col w-fit gap-3'>
                                <Label htmlFor="fecha-convocatoria" className='text-sm font-medium'>
                                    Fecha de tu convocatoria
                                </Label>

                                <Input
                                    className={`bg-transparent ${errors.name ? errorsInputStyle : ''}`}
                                    id="fecha-convocatoria"
                                    type="date"
                                    defaultValue={user.fecha_convocatoria}
                                    value={data.fecha_convocatoria}
                                    onChange={(e) => {
                                        setData('fecha_convocatoria', e.target.value)
                                        clearErrors('fecha_convocatoria');
                                    }}
                                    disabled={processing} 
                                />

                                <InputError message={errors.fecha_convocatoria} />
                            </div>

                            <Button type="submit" className="mt-5 w-fit p-3" disabled={processing || !isDirty}>
                                {/* {processing && <LoaderCircle className="h-4 w-4 animate-spin" />} */}
                                Guardar cambios
                            </Button>
                        </form>
                    </section>


                    {/* PUBLICACIONES */}

                    <section className='flex flex-col gap-5 items-top px-6 py-7 
                    bg-muted-background/80 border-muted-foreground/20 border-1 rounded-md'>
                        <h3 className='text-xl font-heading'>Mis últimas publicaciones</h3>

                        <ul className='space-y-5'>
                            <li>
                                <p className='text-sm font-medium'>Publicación de prueba QA 2026</p>
                                <p className='text-xs'>Hace 7 días</p>
                            </li>
                            <li>
                                <p className='text-sm font-medium'>Publicación de prueba 2 QA 2026</p>
                                <p className='text-xs'>Hace 8 días</p>
                            </li>
                            <li>
                                <p className='text-sm font-medium'>Publicación de prueba 3 QA 2026</p>
                                <p className='text-xs'>Hace 9 días</p>
                            </li>
                        </ul>

                    </section>

                    
                    
                    <Link 
                        className="" 
                        method="post" 
                        href={route('logout')} as="button"
                    >
                        Log out
                    </Link>

                </PageLayout>

            </AppLayout>
        </>

    );
}
