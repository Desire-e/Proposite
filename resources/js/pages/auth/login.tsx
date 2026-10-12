import LoginLayout from '@/layouts/LoginLayout';
import { Head, Link, useForm, } from '@inertiajs/react';
// import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { ArrowLeft } from 'lucide-react';
import AccentText from '@/components/ui/AccentText';


type LoginForm = {
    email: string;
    password: string;
    // remember: boolean;
}

// interface LoginProps {
//     status?: string;
//     canResetPassword?: boolean;
// }


export default function Login ( /* { loginData }: { loginData: LoginForm } */ ) {
// export default function Login ({ status, canResetPassword }: LoginProps) { 

    const { data, setData, post, processing, errors, clearErrors, reset } = 
        useForm<LoginForm>({
            email: '',
            password: '',
            // remember: false,
        });


    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(
            route('login'), 
            { onFinish: () => reset('password'), }
        );
    };

    
    const errorsInputStyle: string = 'border-error! placeholder:text-error! focus:border-2!'; 


    return (
        <>
            <Head title="Inicio de sesión" />

            <LoginLayout>

                <Link 
                    href={route('home')}
                    className="absolute lg:-top-8 -top-10 right-0 flex inline-flex gap-2 items-center
                    text-sm text-muted-foreground 
                    transition-colors hover:text-foreground"
                >
                    <ArrowLeft className='size-4' />
                    Volver
                </Link>
                

                <div className="flex flex-col gap-3 pb-5 text-center">
                    <h1 className="text-3xl font-bold">Inicio de sesión</h1>
                    <h2 className="text-sm">Bienvenido de nuevo ¡Sigamos avanzando!</h2>
                </div>
                

                <form className="flex flex-col gap-6" onSubmit={submit} noValidate>
                    <div className="flex flex-col gap-2">
                        <Input
                            className={`${errors.email ? errorsInputStyle : ''}`}
                            id="email"
                            type="email"
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => {
                                setData('email', e.target.value)
                                clearErrors('email');
                            }}
                            disabled={processing} 
                            placeholder="Email"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="flex flex-col gap-2">
                        <Input
                            className={`${errors.password ? errorsInputStyle : ''}`}
                            id="password"
                            type="password"
                            autoComplete="password" 
                            value={data.password}
                            onChange={(e) => {
                                setData('password', e.target.value)
                                clearErrors('password');
                            }}
                            disabled={processing} 
                            placeholder="Contraseña"
                        />
                        <InputError message={errors.password} />
                    </div>


                    
                    {/* REMEMBER ME
                    <div className="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" />
                        <Label htmlFor="remember">Remember me</Label>
                    </div> */}


                    <Button type="submit" className="mt-5 w-full md:text-md text-base p-6" disabled={processing}>
                        {/* {processing && <LoaderCircle className="h-4 w-4 animate-spin" />} */}
                        Iniciar sesión
                    </Button>
                </form>

                <hr className='text-current/30' />

                <div className="flex items-center gap-2 text-sm">
                    ¿No tienes una cuenta? {' '}

                    <AccentText underline={true}>
                        <Link href={route('register')}>Iniciar sesión</Link>
                    </AccentText>
                </div>

            </LoginLayout>
        </>
        
      
    );
}
