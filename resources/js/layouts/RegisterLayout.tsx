import AuthLayout from "./AuthLayout";

export default function RegisterLayout({children}: {children: React.ReactNode}) {
    return (
        // min-h-screen mínimo la altura de la ventana.
        <main className='flex lg:flex-row flex-col w-full min-h-screen '>
            
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
                {children}
            </AuthLayout>
        </main>
    );
}