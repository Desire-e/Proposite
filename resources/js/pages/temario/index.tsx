// import { useMemo, useState } from 'react'
import { useState, useEffect, useMemo } from 'react'
import { Check, ExternalLink, FileText, 
    // BookOpen, Filter, GraduationCap, ChevronRight, Search, Target, 
} from 'lucide-react'
import { router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout';

type Recurso = {
    subido_por: string;  
    temario_id: string; 
    titulo: string;
    recurso_url: string;
}

type Oposicion = {
    id: string;
    nombre: string;
    descripcion: string;
};

type Tema = {
    id: string;
    bloque: string | null;
    numero_tema: string;
    titulo: string;
    recursos?: Recurso[] | null;
    checked: boolean;
}

type Bloque = { 
    nombre: string | null; 
    temas: Tema[] 
};



export default function Temario(
    {temario, oposicion}: {temario: Tema[], oposicion: Oposicion}
    // { _opposition, callLabel = 'Convocatoria 2026', _topics, studiedTopicIds = [], urls = {}, }:  
    // SyllabusIndexProps
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
                {/* Hero */}
                <section className="rounded-3xl border border-border bg-tertiary px-6 py-7 lg:px-10 lg:py-9">
                    <div className="flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                        <div className="max-w-2xl">
                            <div className="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                                Tu preparación
                            </div>

                            <h1 className="font-serif text-3xl font-bold tracking-tight text-primary lg:text-5xl">
                                Temario de {oposicion.nombre}
                            </h1>

                            <p className="mt-3 max-w-xl text-sm leading-6 text-neutral-foreground">
                                Consulta el temario oficial, marca cada tema cuando lo hayas estudiado. Tu progreso se guardará 
                                en tu perfil para que puedas volver cuando quieras.
                            </p>

                   
                        </div>

                        {/* Progreso */}
                        <div className="w-full max-w-xs rounded-2xl bg-background p-5 shadow-sm">
                            <div className="mb-3 flex items-end justify-between">
                                <span className="text-xs font-semibold text-neutral-foreground">
                                    Progreso global
                                </span>

                                <strong className="text-2xl font-bold text-primary">
                                    {porcentaje}%
                                </strong>
                            </div>

                            <div className="h-2 overflow-hidden rounded-full bg-tertiary">
                                <span
                                    className="block h-full rounded-full bg-secondary transition-all"
                                    style={{ width: `${porcentaje}%` }}
                                />
                            </div>
                            <p className="mt-3 text-xs text-neutral-foreground">
                                {completados} de {temarioLocal.length} temas marcados
                                como estudiados
                            </p>
                        </div>
                    </div>
                </section>

                {/* Temas */}
                <section className='mt-8 gap-4'>
                    <div className="mb-4 flex items-center justify-between">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                                Índice del BOE
                            </p>

                            <h2 className="mt-1 font-serif text-2xl font-bold text-foreground">
                                Todos los temas
                            </h2>
                        </div>
                    </div>

                    {/* TODO. */}
                    {/* Listado de temas (temario oficial) */}
                    <div className="flex flex-col gap-3">
                        
                        {bloques.map((bloque) => (
                        <div key={bloque.nombre ?? 'sin-bloque'} className="flex flex-col gap-3">
                            {/* Mostrar el título solo si existe un bloque */}
                            {bloque.nombre && (
                                <h3 className="font-serif text-xl font-bold text-foreground">
                                    {bloque.nombre}
                                </h3>
                            )}

                            {/* Recorrer los temas de este bloque */}
                            <div className="flex flex-col gap-3">
                                {bloque.temas.map((tema) => (
                                <article
                                    key={tema.id}
                                    className={`rounded-2xl border bg-background p-5 transition-colors 
                                        ${tema.checked ? 'border-secondary/50 bg-tertiary/40' : 'border-border'}
                                    `}
                                >
                                    <div className="flex gap-4">
                                        <button
                                            type="button"
                                            disabled={savingCheck}
                                            onClick={() => toggleCheck(tema.id, tema.checked)}
                                            aria-pressed={tema.checked}
                                            aria-label={`${tema.checked ? 'Desmarcar' : 'Marcar'} tema 
                                                ${tema.numero_tema} como estudiado
                                            `}
                                            className={`mt-0.5 flex size-6 shrink-0 items-center justify-center 
                                                rounded-lg border-2 transition-colors ${tema.checked ? 
                                                    'border-primary bg-primary text-primary-foreground' : 
                                                    'border-secondary bg-background text-transparent hover:bg-tertiary'
                                            }`}
                                        >
                                            <Check className="size-4" />
                                        </button>

                                        <div className="min-w-0 flex-1">
                                            <div className="flex flex-col justify-between gap-2 sm:flex-row">
                                                <div>
                                                    <p className="text-xs font-bold uppercase tracking-[0.12em] text-secondary">
                                                        Tema {tema.numero_tema}
                                                    </p>

                                                    <h3 className={`mt-1 text-base font-bold ${tema.checked ? 
                                                    'text-primary' : 'text-foreground'}`}>
                                                        {tema.titulo}
                                                    </h3>
                                                </div>

                                                {tema.checked && (
                                                    <span className="inline-flex h-fit items-center gap-1 rounded-full 
                                                    bg-tertiary px-2.5 py-1 text-[11px] font-bold text-tertiary-foreground">
                                                        <Check className="size-3" />
                                                        Estudiado
                                                    </span>
                                                )}
                                            </div>

                                            {/* <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                                                ejemplo de resumen del tema 
                                            </p> */}

                                            <div className="mt-4 flex flex-wrap items-center gap-3">
                                                {tema.recursos?.map((recurso) => (
                                                    <a
                                                        key={`${recurso.temario_id}-${recurso.titulo}`}
                                                        href={recurso.recurso_url}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-secondary"
                                                    >
                                                        <FileText className="size-3.5" />
                                                        {recurso.titulo}
                                                        <ExternalLink className="size-3" />
                                                    </a>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </article>
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