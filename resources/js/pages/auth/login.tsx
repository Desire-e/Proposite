import { FormEventHandler, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Eye, EyeOff, } from 'lucide-react';

import { AuthLayout, type asideContentData } from '@/layouts/AuthLayout';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AccentText from '@/components/ui/AccentText';

type LoginForm = {
    email: string;
    password: string;
};

const aside: asideContentData = {
    brow: "Tu preparación continúa",
    title: "Que bien verte de nuevo.",
    description: "Retoma tu preparación, consulta tu progreso y sigue avanzando acompañado por la comunidad.",
    phrase: "Comunidad, recursos y constancia en un mismo lugar.",
} 

const errorsInputStyle: string = 'border-error! placeholder:text-error! focus:border-2!'; 


export default function Login() {
    const { data, setData, post, processing, errors, clearErrors, reset, } = 
    useForm<LoginForm>({
        email: '',
        password: '',
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit: FormEventHandler = (event) => {
        event.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
            <AuthLayout 
                headTitle="Inicio de sesión" 
                className="!min-h-screen !bg-neutral"
                asideContent={aside}    
            >

                <section className="flex items-center justify-center px-6 py-10 sm:px-10">
                    <div className="w-full max-w-md">
                        <div className="mb-10 flex items-center justify-between lg:hidden">
                            <Link
                                href={route('welcome')}
                                className="inline-flex items-center gap-2 text-sm font-semibold text-neutral-foreground"
                            >
                                <ArrowLeft className="size-4" /> Volver
                            </Link>
                        </div>

                        <div className="mb-8">
                            <span className="inline-flex items-center gap-2 
                            text-xs font-bold uppercase tracking-[0.18em] text-secondary">
                                <span className="size-2 rounded-full bg-secondary" />
                                Proposite
                            </span>
                            <h2 className="mt-3 font-heading text-4xl font-bold tracking-tight text-foreground">
                                Inicio de sesión
                            </h2>
                            <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                                Bienvenido de nuevo. Sigamos avanzando.
                            </p>
                        </div>

                        <form onSubmit={submit} noValidate className="flex flex-col gap-5">
                            <div className="flex flex-col gap-2 text-sm font-medium text-foreground">
                                <Input
                                    className={`${errors.email ? errorsInputStyle : ''}`}
                                    id="email"
                                    type="email"
                                    autoComplete="email"
                                    value={data.email}
                                    aria-invalid={Boolean(errors.email)}
                                    onChange={(e) => {
                                        setData('email', e.target.value)
                                        clearErrors('email');
                                    }}
                                    disabled={processing} 
                                    placeholder="Email"
                                />
                                <InputError className='!font-normal' message={errors.email} />
                            </div>

                            <div className="flex flex-col gap-2 text-sm font-medium text-foreground">

                                <div className="relative">
                                    <Input
                                        className={`${errors.password ? errorsInputStyle : ''}`}
                                        type={ showPassword ? 'text' : 'password' }
                                        autoComplete="current-password"
                                        value={data.password}
                                        disabled={processing}
                                        placeholder="Contraseña"
                                        aria-invalid={Boolean(errors.password)}
                                        onChange={(event) => {
                                            setData('password', event.target.value,);
                                            clearErrors('password');
                                        }}
                                    />

                                    <Button
                                        type="button"
                                        aria-label={showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña' }
                                        onClick={() => setShowPassword((value) => !value,)}
                                        className={`${errors.email ? errorsInputStyle : ''}
                                        absolute right-3 top-1/2 -translate-y-1/2 
                                        text-neutral-foreground hover:text-primary`}
                                    >
                                        {showPassword ? 
                                        ( <EyeOff className="size-4" />) : 
                                        ( <Eye className="size-4" /> )}
                                    </Button>
                                </div>
                                <InputError className='!font-normal' message={errors.password} />
                            </div>

                            <Button
                                type="submit"
                                disabled={processing}
                                className="mt-3 h-12 w-full rounded-xl bg-primary font-semibold 
                                text-primary-foreground shadow-lg shadow-primary/20 
                                transition hover:bg-tertiary-foreground"
                            >
                                {processing ? 'Entrando…' : 'Iniciar sesión'}
                            </Button>
                        </form>

                        <div className="my-7 h-px bg-border" />

                        <p className="text-sm text-neutral-foreground">
                            ¿No tienes una cuenta?{' '}
                            <AccentText underline={true}>
                                <Link href={route('register')}>Regístrate</Link>
                            </AccentText>
                        </p>
                    </div>
                </section>
            </AuthLayout>
    );
}