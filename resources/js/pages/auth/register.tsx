import { Link, useForm } from '@inertiajs/react';

import { FormEventHandler } from 'react';
import { useState } from 'react';

import { ArrowLeft, ChevronRight, ChevronDown, GraduationCap } from 'lucide-react';
// import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import AccentText from '@/components/ui/AccentText';

import AppLayout from '@/layouts/AppLayout';
import AuthLayout from '@/layouts/AuthLayout';

import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
} from '@/components/ui/dropdown-menu';


// Laravel asignará el rol opositor al crear la cuenta, en lugar de confiar en el valor recibido del cliente
// enum Rol { ADMIN = "administrador", OPOSITOR = "opositor" }


type RegisterForm = {
    oposicion_id: string;
    name: string;
    email: string;
    password: string;
    fecha_convocatoria?: string; // por petición http viene en string
    acepta_terminos: boolean;
}

type Oposicion = {
    id: string;
    nombre: string;
};
type RegisterProps = {
    oposiciones: Oposicion[];
};

export default function Register(
    { oposiciones = [] }: RegisterProps
) {
    
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

    const { data, setData, post, processing, errors, clearErrors, reset } = 
        // Indica a useForm qué estructura de datos debe gestionar
        useForm<RegisterForm>({
            // Datos iniciales
            oposicion_id: '',
            name: '',
            email: '',
            password: '',
            acepta_terminos: false,
        });


    const errorsInputStyle: string = 'border-error! placeholder:text-error! focus:border-2!'; 

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(
            route('register'), 
            // Opciones
            {
                // onFinish Se ejecuta cuando la petición de Inertia termina, con o sin éxito
                onFinish: () => reset('password'), 
            }
        );
    };


    const [openDropdown, setOpenDropdown] = useState(false);
    
    const selectedOposicion = oposiciones.find((oposicion) => (oposicion.id === data.oposicion_id));

    
    return (
        <>
            <AppLayout headTitle='Registro de nueva cuenta' className='lg:flex-row!'>
                
                {/* TODO. Cambiar imagen placeholder a otra */}
                <div className="relative w-full shrink-0 lg:w-1/2">
                    <img 
                        src="/images/placeholder.png" alt="" 
                        // lg:absolute lg:inset-0 lg:h-full la imagen ocupa el espacio de su contenedor en escritorio.
                        // object-cover mantiene las proporciones de la imagen
                        className="block h-40 w-full object-cover lg:absolute lg:inset-0 lg:h-full"
                    />
                </div>

                <AuthLayout>

                    <Link 
                        href={route('welcome')}
                        className="absolute lg:-top-[-30px] -top-[-20px] lg:right-[50px] right-[20px] 
                        flex inline-flex gap-2 items-center text-sm text-muted-foreground 
                        transition-colors hover:text-foreground"
                    >
                        <ArrowLeft className='size-4' />
                        Volver
                    </Link>
                    

                    <div className={`flex flex-col gap-4 pb-5 ${processing ? 'opacity-50!' : ''}`}>
                        <h1 className="text-3xl font-bold">Crear cuenta</h1>
                        <h2 className="md:text-sm text-base">Bienvenido a Proposite. Completa los datos para unirte.</h2>
                    </div>
                    

                    <form 
                        className= {` flex flex-col gap-6 ${processing ? 'opacity-50!' : ''}`} onSubmit={submit} 
                        noValidate // omite validación nativa del navegador
                    >

                        <div className="flex flex-col gap-2">
                            <Input
                                className={`${errors.name ? errorsInputStyle : ''}`}
                                id="name"
                                type="text"
                                autoComplete="name"
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
                                // el tipo de dato introducido para poder rellenar automáticamente información guardada
                                autoComplete="new-password" 
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

                        <div className="flex flex-col gap-2">
                            <DropdownMenu open={openDropdown} onOpenChange={setOpenDropdown}>
                                <DropdownMenuTrigger asChild>
                                    <button
                                        type="button" disabled={processing} 
                                        aria-label="Elegir oposición" aria-invalid={Boolean(errors.oposicion_id)}
                                        className={`w-full bg-background inline-flex justify-between items-center px-3 py-2 
                                        text-muted-foreground/80 md:text-sm text-base border-1 rounded-sm border-muted-foreground/30 
                                        hover:bg-gray-100 focus:border-muted-foreground focus:outline-hidden focus:bg-hover 
                                        disabled:cursor-not-allowed disabled:opacity-50 
                                        ${ selectedOposicion && "text-foreground!"}
                                        ${errors.oposicion_id ? errorsInputStyle : ''} `}
                                    >
                                        <div className='flex gap-2 items-center text-left'>
                                            <GraduationCap className='md:size-5 size:8' strokeWidth={1.5} />
                                            {/* nombre de la oposición seleccionada */}
                                            {selectedOposicion?.nombre ?? 'Elige tu oposición'}
                                        </div>

                                        {openDropdown ? 
                                        (<ChevronRight className="text-foreground! md:size-5 size:8" strokeWidth={1.5} />) : 
                                        (<ChevronDown className="text-foreground! md:size-5 size:8" strokeWidth={1.5} />)}
                                    </button>
                                </DropdownMenuTrigger>
                                
                                <DropdownMenuContent 
                                    className="bg-background border-1 rounded-sm border-muted-foreground/30"
                                    align="center"
                                >
                                    {oposiciones.map((oposicion) => (

                                    <DropdownMenuItem 
                                        className='hover:bg-hover text-foreground md:text-sm text-base
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

                            <p className='text-sm text-muted-foreground'>
                                Selecciona la oposición a la que te estás presentando
                            </p>

                            <InputError message={errors.oposicion_id} />
                        </div>

                        <div className="flex flex-col gap-2">
                            <div className="flex items-center gap-2 text-sm">
                                <Checkbox 
                                    id="terminos"
                                    checked={data.acepta_terminos}
                                    onCheckedChange={(checked) => {
                                        setData('acepta_terminos', checked === true);
                                        clearErrors('acepta_terminos');
                                    }}
                                    disabled={processing}
                                />
                                
                                <label htmlFor="terminos" className='md:text-sm text-base'> 
                                    Acepto los {' '}
                                    <AccentText underline={true}>términos y condiciones</AccentText>
                                </label>
                            </div>

                            <InputError message={errors.acepta_terminos} />
                        </div>

                        <Button 
                            type="submit" 
                            className="mt-5 w-full md:text-base text-lg p-6 " 
                            disabled={processing}
                        >
                            {/* {processing && <LoaderCircle className="h-4 w-4 animate-spin" />} */}
                            Registrarme
                        </Button>
                    </form>


                    <hr className={`text-current/30 ${processing ? 'opacity-50!' : ''}`}  />


                    <div className={`flex items-center gap-2 text-sm ${processing ? 'opacity-50!' : ''}`}>
                        ¿Tienes ya una cuenta?
                        {' '}
                        <AccentText underline={true}>
                            <Link href={route('login')}>Inicia sesión</Link>
                        </AccentText>
                    </div>

                </AuthLayout>
            </AppLayout>

        </>

    );
}
