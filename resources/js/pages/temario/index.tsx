import { useState, useEffect, useMemo } from 'react'
import { router } from '@inertiajs/react'
import { type Oposicion, type Tema } from '@/types/models';

import AppLayout from '@/layouts/AppLayout';
import { PageHero } from '@/components/PageHero';
import { TemaItem } from '@/components/temario/TemaItem';
import { SectionHeading } from '@/components/ui/SectionHeading';
import { ProgressBar } from '@/components/ui/ProgressBar';

type Bloque ={ 
    nombre: string | null; 
    temas: Tema[] 
};



export default function Temario(
    {temario, oposicion}: {temario: Tema[], oposicion: Oposicion}
){

    // Copia local del temario
    const [temarioLocal, setTemarioLocal] = useState<Tema[]>(temario);

    // Sincroniza temario nuevo enviado por el servidor
    useEffect(() => { setTemarioLocal(temario); }, [temario]);


    // Temas checkados (estudiados)
    const completados = temarioLocal.filter((tema) => tema.checked).length;

    // Porcentaje de avance
    const porcentaje = temario.length
        ? Math.round((completados / temario.length) * 100)
        : 0


    /**
     * Guardar el resultado de un cálculo entre renderizados 
     * (agrupación de temas por bloque) en caché y reutilizarlo 
     * mientras sus dependencias no hayan cambiado
     */
    const bloques = useMemo<Bloque[]>(() => {

        // Guarda información mediante pares de key/value (bloqueNombre/temas[])
        const mapa = new Map<string | null, Tema[]>();

        temarioLocal.forEach((tema) => {
            // Obtener bloque (k) del tema actual, si tiene
            const bloqueKey = tema.bloque?.trim() || null;
        
            // Si el mapa todavía no contiene ese bloque (k), crea un 
            // grupo vacío donde se guardarán los temas de ese bloque
            if (!mapa.has(bloqueKey)) mapa.set(bloqueKey, []);
            
            // Añade el tema actual, al array (v) asociado al bloque (k)
            mapa.get(bloqueKey)! // Busca una entrada del mapa y devuelve su valor
            .push(tema); // añade el tema actual al final del array
        });

        // Crea un array a partir del mapa y transforma cada entrada en un objeto
        return Array.from(mapa, ([nombre, temas]) => ({ nombre, temas }));
    
    }, [temarioLocal]);


    /**
     * Cambia el estado checked de un tema localmente
     */
    const setLocalChecked = (id: string, checked: boolean) => {
        setTemarioLocal(
            // el valor más reciente del estado anterior
            (temasActuales) => {
            return temasActuales.map((tema) => {
                // Si no es el tema buscado, quedará igual
                if (tema.id !== id) { return tema; }

                // Si es el tema buscado, crea una copia del anterior
                // modificando solo el nuevo estado de checked
                return {...tema, checked: checked,};
            });
        });
    };

    /**
     * Bloquear botón mientras se está guardando el estado en el 
     * servidor (evitar bug por doble click) 
     */
    const [savingCheck, setSavingCheck] = useState(false);


    /**
     * Cambiar estado check local y en servidor
     */
    const toggleCheck = (id: string, currentChecked: boolean) => {

        // Actualización local instantánea
        setLocalChecked(id, !currentChecked);

        // Actualización servidor
        // PATCH = cambios parciales
        router.patch(
            route('temario.check'),
            { 
                checked: !currentChecked, 
                tema: id 
            },
            { 
                // Indica a Inertia que conserve la posición del scroll durante la navegación
                preserveScroll: true,
                
                onStart: () => setSavingCheck(true),
                onFinish: () => setSavingCheck(false),

                // Si falla, deshace el estado local
                onError: () => setLocalChecked(id, currentChecked),
            }, 
        );
    };


    return (
        <AppLayout headTitle="Temario oficial | Proposite">

            <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">
                <PageHero
                    eyebrow="Tu preparación"
                    title={`Temario de ${oposicion.nombre}`}
                    description="Consulta el temario oficial, marca cada tema cuando lo hayas estudiado. Tu progreso se guardará en tu perfil para que puedas volver cuando quieras."
                    aside={
                        // Progreso
                        <div className="w-full max-w-xs rounded-2xl bg-background p-5 shadow-sm">
                            <div className="mb-3 flex items-end justify-between">
                                <span className="text-xs font-semibold text-neutral-foreground">
                                    Progreso global
                                </span>

                                <strong className="text-2xl font-bold text-primary">
                                    {porcentaje}%
                                </strong>
                            </div>

                            <ProgressBar porcentaje={porcentaje} />

                            <p className="mt-3 text-xs text-neutral-foreground">
                                {completados} de {temarioLocal.length} temas marcados
                                como estudiados
                            </p>
                        </div>
                    }
                />

                {/* Temas */}
                <section className='mt-8 gap-4'>
                    <SectionHeading
                        eyebrow="Índice del BOE"
                        title="Todos los temas"
                        className="mb-4 text-foreground"
                    />

                    {/* Listado de temas (temario oficial) */}
                    <div className="flex flex-col gap-3">
                        
                        {bloques.map((bloque) => (
                        <div key={bloque.nombre ?? 'sin-bloque'} className="flex flex-col gap-3">
                            {/* Mostrar el título solo si existe un bloque */}
                            {bloque.nombre && (
                                <h3 className="font-heading text-xl font-bold text-foreground">
                                    {bloque.nombre}
                                </h3>
                            )}

                            {/* Recorrer los temas de este bloque */}
                            <div className="flex flex-col gap-3">
                                {bloque.temas.map((tema) => (
                                    <TemaItem
                                        key={tema.id}
                                        tema={tema}
                                        disabled={savingCheck}
                                        onToggle={() => toggleCheck(tema.id, tema.checked)}
                                    />
                                ))}
                            </div>
                        </div>
                        ))}
                    </div>
                </section>
            </div>

        </AppLayout>
    )
}