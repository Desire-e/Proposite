import { Head } from '@inertiajs/react';

import { AppHeader } from '@/components/AppHeader';
import { AppFooter } from '@/components/AppFooter';

import PageLayout from '@/layouts/PageLayout';

import Hero from '@/components/Hero';

import { StepsCards, type StepCardData } from '@/components/ui/StepsCards';
import { TitledCards, type TitledCardData } from '@/components/ui/TitledCards';
import { Users, BookOpenText, SquarePen, CalendarFold } from 'lucide-react';

/**
 * Página de inicio Landing
 */


export default function Landing() {
    
    // TODO. Datos reales
    const productsData: TitledCardData[] = [
        {
            icon: Users,
            title:"Comunidad",
            description:"Egestas elit dui scelerisque ut eu purus aliquam vitae habitasse.",
        },
        {
            icon: BookOpenText,
            title:"Recursos",
            description:"Id eros pellentesque facilisi id mollis faucibus commodo enim.",
        },
        {
            icon: SquarePen,
            title:"Tests",
            description:"Nunc, pellentesque velit malesuada non massa arcu.",
        },
        {
            icon: CalendarFold,
            title:"Seguimiento",
            description:"Imperdiet purus pellentesque sit mi nibh sit integer faucibus.",
        },                        
        
    ];

    // TODO. Datos reales
    const stepsData: StepCardData[] = [
        {
            number: 1, 
            title: "Regístrate", 
            description: "Crea tu cuenta y elige la oposición a la que te presentas."
        }, 
        {
            number: 2, 
            title: "Consulta el temario", 
            description: "Accede al temario oficial y marca tu progreso tema a tema"
        }, 
        {
            number: 3, 
            title: "Participa en el foro", 
            description: "Comparte dudas, publica, comenta y marca lo que te sirvió."
        }, 
        {
            number: 4, 
            title: "Haz tests y mide tu avance", 
            description: "Pon a prueba lo que sabes y sigue tu evolución con estadísticas."
        }, 
    ];    


    return (
        <>
            {/* Gestionar las etiquetas <title> y <meta> del documento HTML */}
            <Head title="Proposite" />
            
            <AppHeader />
                
            <Hero />

            <PageLayout>
                <section className='w-full space-y-8'>
                        <h2 className='text-center text-4xl font-semibold' style={{fontFamily: "var(--heading-font)"}}>
                            Todo lo que necesitas, en un mismo lugar
                        </h2>
        
                        <p className='text-center'>Proposite reúne herramientas y personas para que te centres en lo importante, tu propósito.</p>

                    <TitledCards 
                    cards={productsData}
                    />
                </section>


                <section id="how-to-start" className='w-full space-y-8'>
                    <h3 className='text-center text-4xl font-semibold' style={{fontFamily: "var(--heading-font)"}}>
                        Empieza en cuatro pasos
                    </h3>
                    
                    <p className='text-center'>Así de sencillo es sumarte a tu comunidad de oposición</p>
                    
                    <StepsCards 
                    steps={stepsData}
                    />
                </section>

            </PageLayout>

            <AppFooter />
        </>
    );
}
