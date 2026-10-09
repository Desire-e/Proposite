import { Link } from '@inertiajs/react';

export default function Hero() {

    return (
        <div className="flex flex-col justify-center gap-6 bg-hover">
            {/* TODO. Layout Hero image */}
            {/* <div></div> */}

            <div className="w-[90%] mx-auto">
                <div className='flex flex-col gap-10 max-w-[100%] lg:max-w-[45%] py-20'>
                    <h1 className='text-5xl lg:text-6xl font-semibold' style={{fontFamily: "var(--heading-font)"}}>
                        Opositar es duro, hazlo acompañado
                    </h1>

                    <p>
                        Conecta con estudiantes de tu misma oposición, haz uso de los recursos públicos del 
                        temario oficial, que te ayudarán en tu etapa de estudios, realiza tests, sigue tu progreso 
                        y llega más lejos en comunidad.
                    </p>

                    <div className='flex gap-5'>
                        <Link
                        href={route('register')}
                        className="border-2 px-6 py-2"
                        style={{color:'var(--primary-foreground)', borderColor:'var(--primary)', background:'var(--primary)'}} 
                        >
                            Comenzar
                        </Link>

                        <Link
                        href="#content"
                        className="border-2 px-6 py-2"
                        style={{color:'var(--primary)', borderColor:'var(--primary)'}} 
                        >
                            Saber más
                        </Link>

                    </div>
                </div>                  
            </div>  
        </div>
    );
}
