import { BookOpen, Check, MessageCircle, } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import Hero from '@/components/Hero';
import { TitledCards, type TitledCardData } from '@/components/ui/TitledCards';
import { StepsCards, type StepCardData } from '@/components/ui/StepsCards';


const comoFunciona: TitledCardData[] = [
    { 
        icon: MessageCircle, 
        title: 'Comparte sin filtros', 
        description: 'Celebra avances y encuentra personas que entienden exactamente tu proceso.', 
    },
    { 
        icon: BookOpen, 
        title: 'Resuelve tus dudas', 
        description: 'Preguntas, respuestas y recursos útiles de opositores como tú.', 
    },
    { 
        icon: Check, 
        title: 'Mide tu avance', 
        description: 'Tests y seguimiento para convertir la constancia en progreso.', 
    },
];

const pasos: StepCardData[] = [
    {
        number: '01', 
        title: 'Regístrate', 
        description: 'Crea tu cuenta y elige la oposición a la que te presentas.'
    },
    {
        number: '02', 
        title: 'Consulta el temario', 
        description: 'Accede al temario oficial y marca tu progreso tema a tema.'
    },
    {
        number: '03', 
        title: 'Participa en el foro', 
        description: 'Comparte dudas, publica y encuentra respuestas útiles.'
    },
    {
        number: '04', 
        title: 'Mide tu avance', 
        description: 'Haz tests y sigue tu evolución con estadísticas.'
    },
];

export default function Welcome() {
    return (
        <AppLayout headTitle="Proposite | Comunidad de opositores">
                
            <Hero />

            <section id="como-funciona" className="mx-auto w-full max-w-7xl px-5 py-24 lg:px-10">
                <div className="max-w-2xl">
                    <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                        Tu oposición, acompañada
                    </p>

                    <h2 className="mt-4 font-heading text-4xl font-bold tracking-tight text-foreground sm:text-5xl">
                        Todo lo que necesitas para seguir.
                    </h2>

                    <p className="mt-5 text-lg leading-8 text-neutral-foreground">
                        Apoyo académico y compañía real en cada etapa: desde tu
                        primer tema hasta el día del examen.
                    </p>
                </div>

                <TitledCards cards={comoFunciona} />
            </section>


            <section
                id="como-empezar"
                className="bg-tertiary px-5 py-24"
            >
                <div className="mx-auto max-w-7xl">
                    <p className="text-xs font-bold uppercase tracking-[0.16em] text-primary">
                        Empieza sin complicarte
                    </p>

                    <h2 className="mt-4 font-heading text-4xl font-bold text-foreground">
                        Tu camino, paso a paso.
                    </h2>

                    <StepsCards steps={pasos} />
                </div>
            </section>

        </AppLayout>
    );
}
