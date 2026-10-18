import { FormEventHandler, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, ChevronDown, Eye, EyeOff, GraduationCap, ChevronRight
} from 'lucide-react';

// import AppLayout from '@/layouts/AppLayout';
import { AuthLayout, type asideContentData } from '@/layouts/AuthLayout';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import AccentText from '@/components/ui/AccentText';


import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
} from '@/components/ui/dropdown-menu';


const aside: asideContentData = {
    brow: "Un camino compartido",
    title: "Tu camino empieza aquí.",
    description: "Crea tu espacio y empieza a opositar con una comunidad que te entiende.",
    phrase: '“Los días pesan menos cuando no los recorres solo.”',
} 


type Oposicion = {
    id: string;
    nombre: string;
};

type RegisterProps = {
    oposiciones: Oposicion[];
};

type RegisterForm = {
    oposicion_id: string;
    name: string;
    email: string;
    password: string;
    acepta_terminos: boolean;
};

export default function Register({
    oposiciones = [],
}: RegisterProps) {

    /**
     * Hook useForm
     * 
     * @data Contiene los valores actuales de los campos (data.email ...) 
     * @setData Actualiza los valores del formulario (setData('name', 'Ana') ...)
     * @post Envía los datos por POST a la ruta indicada. Laravel recibe, valida y procesa.
     * @processing Un booleano que indica si la petición está en curso. 
     * @errors Contiene los errores de validación que devuelve el servidor. 
     * @clearErrors Limpia los errores
     * @reset Para restablecer uno o más campos a valores iniciales. 
     */

    const { data, setData, post, processing, errors, clearErrors, reset,} = 
        // Indica a useForm qué estructura de datos debe gestionar
        useForm<RegisterForm>({
            // Datos iniciales
            oposicion_id: '',
            name: '',
            email: '',
            password: '',
            acepta_terminos: false,
        });


    const [showPassword, setShowPassword] = useState(false);

    const [openDropdown, setOpenDropdown] = useState(false);
    const selectedOposicion = oposiciones.find((oposicion) => (oposicion.id === data.oposicion_id));



    const submit: FormEventHandler = (event) => {
        event.preventDefault();

        post(route('register'), 
            // Opciones
            {
                // onFinish Se ejecuta cuando la petición de Inertia termina, con o sin éxito
                onFinish: () => reset('password'),
            }
        );
    };


    return (
        <AuthLayout
            headTitle="Crear cuenta"
            className="!min-h-screen !bg-neutral"
            asideContent={aside}  
        >

            <section className="flex items-center justify-center px-6 py-10 sm:px-10">
                <div className="w-full max-w-md">
                    <div className="mb-8 flex items-center justify-between lg:hidden">
                        <Link
                            href={route('welcome')}
                            className="inline-flex items-center gap-2 text-sm font-semibold text-neutral-foreground"
                        >
                            <ArrowLeft className="size-4" />
                            Volver
                        </Link>

                        <span className="font-heading text-xl font-bold text-primary">
                            proposite
                        </span>
                    </div>

                    <div className="mb-7">
                        <span className="text-xs font-bold uppercase tracking-[0.18em] text-secondary">
                            Da el primer paso
                        </span>

                        <h2 className="mt-3 font-heading text-4xl font-bold tracking-tight text-foreground">
                            Crear cuenta
                        </h2>

                        <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                            Únete a una comunidad que entiende tu objetivo.
                        </p>
                    </div>

                    <form
                        onSubmit={submit}
                        noValidate
                        className="flex flex-col gap-4"
                    >
                        <div className="flex flex-col gap-2 text-sm font-medium text-foreground">
                            <Input
                                value={data.name}
                                placeholder="Nombre"
                                autoComplete="name"
                                disabled={processing}
                                onChange={(event) => {
                                    setData('name', event.target.value);
                                    clearErrors('name');
                                }}
                                errors={errors.name ? true : false}
                            />

                            <InputError className='!font-normal' message={errors.name} />
                        </div>


                        <div className="flex flex-col gap-2 text-sm font-medium text-foreground">
                            <Input
                                value={data.email}
                                placeholder="Email"
                                type="email"
                                autoComplete="email"
                                disabled={processing}
                                onChange={(event) => {
                                    setData('email', event.target.value);
                                    clearErrors('email');
                                }}
                                errors={errors.email ? true : false}
                            />

                            <InputError className='!font-normal' message={errors.email} />
                        </div>


                        <div className="flex flex-col gap-2 text-sm font-medium text-foreground">
                            <div className="relative">
                                <Input
                                    type={ showPassword ? 'text' : 'password'}
                                    autoComplete="new-password"
                                    value={data.password}
                                    disabled={processing}
                                    placeholder="Contraseña"
                                    onChange={(event) => { 
                                        setData('password', event.target.value,);
                                        clearErrors('password');
                                    }}
                                    errors={errors.password ? true : false}
                                />

                                <Button
                                    type="button"
                                    aria-label={ showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña' }
                                    onClick={() => setShowPassword((value) => !value,)}
                                    className={`${errors.password ? 'text-error!' : ''}
                                    absolute right-3 top-1/2 -translate-y-1/2 
                                    text-neutral-foreground hover:text-primary`}
                                >
                                    {showPassword ? 
                                    (<EyeOff className="size-4" />) : 
                                    (<Eye className="size-4" />)}
                                </Button>
                            </div>
                            <InputError className='!font-normal' message={errors.password} />
                        </div>


                        <div className="flex flex-col gap-2 text-sm font-medium text-foreground">
                            <DropdownMenu open={openDropdown} onOpenChange={setOpenDropdown}>
                                <DropdownMenuTrigger asChild>
                                    <button
                                        type="button" disabled={processing} 
                                        aria-label="Elegir oposición" aria-invalid={Boolean(errors.oposicion_id)}
                                        className={`inline-flex justify-between items-center px-3 py-2 w-full bg-white 
                                        appearance-none rounded-sm text-sm border border-border outline-none 
                                        transition focus:border-primary focus:ring-2 focus:ring-ring/30
                                        disabled:cursor-not-allowed disabled:opacity-50
                                        ${errors.oposicion_id ? 'border-error! placeholder:text-error! focus:border-2!' : ''}`}
                                    >
                                        <div className='flex gap-2 items-center text-left'>
                                            <GraduationCap 
                                                className='pointer-events-none size-5 text-secondary' 
                                                strokeWidth={1.5} 
                                            />
                                            {/* nombre de la oposición seleccionada */}
                                            {selectedOposicion?.nombre ?? 'Elige tu oposición'}
                                        </div>

                                        {openDropdown ? 
                                        (<ChevronRight className="text-foreground! md:size-5 size:8" strokeWidth={1.5} />) : 
                                        (<ChevronDown className="text-foreground! md:size-5 size:8" strokeWidth={1.5} />)}
                                    </button>
                                </DropdownMenuTrigger>
                                
                                <DropdownMenuContent 
                                    className="bg-white border-1 rounded-sm border-muted-foreground/30"
                                    align="center"
                                >
                                    {oposiciones.map((oposicion) => (

                                    <DropdownMenuItem 
                                        className='hover:bg-background text-foreground md:text-sm text-base
                                        focus:bg-hover focus:text-accent-foreground'
                                        key={oposicion.id} 
                                        onSelect={() => {
                                            setData('oposicion_id', oposicion.id);
                                            clearErrors('oposicion_id');
                                        }}
                                    >
                                        {oposicion.nombre}
                                    </DropdownMenuItem>
                                    
                                    ))}
                                </DropdownMenuContent>
                            </DropdownMenu>
                            <InputError className='!font-normal' message={errors.oposicion_id} />
                        </div>


                        <p className="-mt-2 text-xs font-normal text-neutral-foreground">
                            Selecciona la oposición a la que te estás
                            presentando.
                        </p>

                        <div className="flex flex-col items-start gap-2 mt-2 text-sm font-normal 
                        leading-5 text-neutral-foreground">
                            <label htmlFor="terminos" className='text-sm flex gap-3 items-center'> 
                                <Checkbox
                                    id="terminos"
                                    checked={data.acepta_terminos}
                                    disabled={processing}
                                    onCheckedChange={(checked) => {
                                        setData('acepta_terminos', checked === true,);
                                        clearErrors('acepta_terminos');
                                    }}
                                />
                                <span>
                                    Acepto los{' '}
                                    <AccentText underline={true}>
                                        <a href="#terminos">
                                            Términos y condiciones.
                                        </a>
                                    </AccentText>
                                </span>
                            </label>
                            <InputError className='!font-normal' message={errors.acepta_terminos} />
                        </div>


                        <Button
                            type="submit"
                            disabled={processing}
                            className="mt-2 h-12 w-full rounded-xl bg-primary font-semibold 
                            text-primary-foreground shadow-lg shadow-primary/20 transition hover:bg-tertiary-foreground"
                        >
                            {processing ? 'Creando cuenta…' : 'Crear mi cuenta'}
                        </Button>
                    </form>

                    <div className="my-6 h-px bg-border" />

                    <p className="text-sm text-neutral-foreground text-center">
                        ¿Ya tienes una cuenta?{' '}
                        <AccentText underline={true}>
                            <Link href={route('login')}>
                                Inicia sesión
                            </Link>
                        </AccentText>
                    </p>
                </div>
            </section>
        </AuthLayout>
    );
}