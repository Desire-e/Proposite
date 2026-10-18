import { ArrowRight, CalendarDays, Edit3, GraduationCap, Target, Trophy, Users, } from "lucide-react";
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import UserAvatar from "@/components/ui/UserAvatar";

type Oposicion = {
    nombre: string;
    descripcion: string;
}

type Progress = {
    total_temas: number;
    temas_estudiados: number;
    porcentaje: number;
}

type Stats = {
    tests_realizados: number;
    tests_aprobados: number;
    nota_media: number | null;
}

type BestSubject = {
    titulo: string;
    nota_media: number;
}

type RecentActivity = {
    id: string;
    tipo: 'publicacion' | 'comentario';
    contenido: string;
    created_at: string;
}

type UserProfile = {
    name: string;
    oposicion: Oposicion;
    biografia?: string;
    signed_up_at: string;
    anio_convocatoria?: string;

    progreso: Progress;
    estadisticas: Stats;
    tema_mas_fuerte: BestSubject;
    actividad_reciente: RecentActivity[];
};

// type UserProfile = {
//     name: string;
//     oposicion: Oposicion;
//     biografia?: string;
//     signed_up_at: string;
//     anio_convocatoria?: string;
// }

type PerfilProps = { user: UserProfile; };


export default function Perfil({ user }: PerfilProps) {

    /**
     * Formatea @dateStr completa, a mes y año en string 
     */    
    function formatMY(dateStr: string): string {

        const date = new Date(dateStr)
        
        // comprueba si es fecha válida, convirtiendo a ms desde 1970 y comprobando isNaN 
        if (Number.isNaN(date.getTime())) return '' 

        return new Intl.DateTimeFormat('es-ES', {
            month: 'long',
            year: 'numeric',
            timeZone: 'UTC',
        }).format(date) // Aplica las opciones de formato a la fecha y devuelve resultado en texto
    }

    console.log('Nota media', user.estadisticas.nota_media);


    return (
        <AppLayout headTitle={"Perfil | Proposite"}>

            <div className="mx-auto w-full max-w-6xl px-4 py-6">

                {/* Profile hero */}
                <section className="flex flex-col px-4 py-5 md:flex-col md:px-9 md:py-8  
                gap-6 rounded-2xl border border-border bg-white shadow-sm">
                    <div className="text-xs font-extrabold uppercase tracking-[0.13em] text-secondary">
                        <span className="mr-2 inline-block size-2 rounded-full bg-success-foreground" />
                        Perfil de opositor
                    </div>

                    <div className="flex md:flex-row flex-col gap-6 items-center justify-between">
                        <div className="flex flex-col gap-2 md:w-[50%] lg:w-[60%]">
                            
                            <div className="flex gap-4 sm:gap-5">
                                <UserAvatar 
                                    size='lg'
                                    className="font-heading font-bold text-primary bg-primary/10 border-1 border-primary!" 
                                />

                                {/* Nombre, oposición */}
                                <div>
                                    <h1 className="mb-1 mt-2 font-heading text-3xl 
                                    font-bold leading-tight tracking-[-0.06em] md:text-4xl">
                                        {user.name}
                                    </h1>

                                    <p className="m-0 flex items-center gap-2 text-sm font-bold 
                                    text-primary">
                                        <GraduationCap className='md:size-4 size-6' /> 
                                        {user.oposicion.nombre}
                                    </p>
                                </div>
                            </div>

                            <div className="flex flex-col gap-2 text-xs">
                                <p className="mb-3 mt-3 leading-relaxed text-neutral-foreground">
                                    {user.biografia ?? ''}
                                </p>

                                {/* Fecha registro, convocatoria */}
                                <div className="flex flex-col gap-2 text-neutral-foreground sm:flex-row sm:gap-5">
                                    <span className="flex items-center gap-2">
                                        <CalendarDays size={15} /> En Proposite desde {formatMY(user.signed_up_at)}
                                    </span>
                                    {user.anio_convocatoria &&
                                    <span className="flex items-center gap-2">
                                        <Target size={15} /> Convocatoria {user.anio_convocatoria}
                                    </span>}
                                </div>
                            </div>
                        </div>

                        <Link
                            href={route('opositor.perfil.edit')}
                            className="inline-flex shrink-0 items-center gap-2 rounded-lg border border-border 
                            px-4 py-3 text-xs font-semibold text-primary transition-colors hover:bg-tertiary"
                        >
                            <Edit3 size={15} /> Editar perfil
                        </Link>
                    </div>
                </section>



                <div className="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_0.8fr]">
                    {/* TODO.  */}
                    {/* ProfileCard.tsx */}

                    {/* Progress */}
                    <section className="rounded-2xl border border-border bg-white p-5 md:p-6">
                        <div className="flex items-start justify-between gap-3">
                            <div>
                                <span className="text-xs font-extrabold uppercase tracking-[0.13em] text-secondary">
                                    Tu preparación
                                </span>

                                <h2 className="mb-0 mt-2 font-heading text-2xl font-bold tracking-[-0.04em]">
                                    Avance del temario
                                </h2>
                            </div>

                            <Link
                                href={route('temarios.index')}
                                className="inline-flex shrink-0 items-center gap-1 text-xs font-bold text-primary"
                            >
                                Ver temario
                                <ArrowRight size={14} />
                            </Link>
                        </div>

                        <div className="my-6 flex items-center gap-4 sm:gap-6">
                            <div 
                                style={{
                                    background: `radial-gradient(circle at center, #fff 62%, transparent 63%), 
                                    conic-gradient(var(--primary) ${user.progreso.porcentaje}%, var(--tertiary) 0%)`,
                                }}
                                className="flex flex-col size-23 shrink-0  items-center justify-center 
                                rounded-full text-primary md:size-27">
                                <span className="font-heading text-xl font-bold md:text-2xl">
                                    {user.progreso.porcentaje}%
                                </span>
                                <span className="mt-0.5 text-xs text-neutral-foreground">
                                    completado
                                </span>
                            </div>

                            <div className="min-w-0 flex-1">
                                <span className="text-sm font-bold">
                                    {user.progreso.porcentaje < 30 ? 'Sigamos avanzando' : 
                                        user.progreso.porcentaje >= 30 && user.progreso.porcentaje < 80 ? 'Vas por buen camino' : 
                                        'Tu aprobado está en camino'
                                    }
                                </span>

                                <p className="mb-3 mt-2 text-xs leading-relaxed text-neutral-foreground">
                                    Has estudiado {user.progreso.temas_estudiados} de {user.progreso.total_temas} temas del temario oficial.
                                </p>

                                
                                {/* <div
                                    className="h-[7px] overflow-hidden rounded-full bg-tertiary"
                                    role="progressbar"
                                    aria-label="Temario completado"
                                    aria-valuenow={user.progreso.porcentaje}
                                    aria-valuemin={0}
                                    aria-valuemax={100}
                                >
                                    <span 
                                        style={{ width: `${user.progreso.porcentaje}%` }}
                                        className="block h-full rounded-full bg-primary" 
                                    />
                                </div> */}
                            </div>
                        </div>
                    </section>

                    {/* Statistics */}
                    <section className="rounded-2xl border border-border bg-white p-5 md:p-6">
                        <div className="flex items-start justify-between">
                            <div>
                                <span className="text-xs font-extrabold uppercase tracking-[0.13em] text-secondary">
                                    Tu rendimiento
                                </span>

                                <h2 className="mb-0 mt-2 font-heading text-2xl font-bold tracking-[-0.04em]">
                                    Una mirada rápida
                                </h2>
                            </div>


                            <Link
                                href={route('temarios.index')}
                                className="inline-flex shrink-0 items-center gap-1 text-xs font-bold text-primary"
                            >
                                Ver estadísticas
                                <ArrowRight size={14} />
                            </Link>
                        </div>

                        <div className="mt-6 grid grid-cols-3 gap-2 sm:gap-2.5">
                            <div className="rounded-[11px] bg-neutral px-2 py-3.5 sm:px-3">
                                <span className="block font-bold font-heading text-2xl font-bold text-primary">
                                    {user.estadisticas.nota_media}
                                </span>

                                <span className="mt-2 block text-xs">
                                    Nota media
                                </span>
                            </div>

                            <div className="rounded-[11px] bg-neutral px-2 py-3.5 sm:px-3">
                                <span className="block font-bold font-heading text-2xl font-bold text-primary">
                                    {user.estadisticas.tests_realizados}
                                </span>

                                <span className="mt-2 block text-xs">
                                    Tests realizados
                                </span>
                            </div>

                            <div className="rounded-[11px] bg-neutral px-2 py-3.5 sm:px-3">
                                <span className="block font-bold font-heading text-2xl font-bold text-primary">
                                    {user.estadisticas.tests_aprobados}
                                </span>

                                <span className="mt-2 block text-xs">
                                    Tests aprobados
                                </span>
                            </div>
                        </div>

                        <div className="mt-5 flex items-center gap-2 border-t border-border pt-4 text-xs text-neutral-foreground">
                            {/* TODO. Insertar datos con seeder en Laravel */}
                            {user.tema_mas_fuerte ? 
                            (<>
                                <Trophy size={16} className="shrink-0 text-secondary" />
                                <span>
                                    Tu tema más fuerte es {' '}
                                    <span className="font-bold text-primary">
                                        {user.tema_mas_fuerte.titulo} 
                                    </span>
                                </span>
                            </>) : (
                                <span>Aún no has realizado ningún test.</span>                                
                            )}
                        </div>
                    </section>


                    {/* TODO. Laravel, last 2 publicaciones / comentarios */}
                    <section className="rounded-2xl border border-border bg-white p-5 md:p-6">
                        <div className="flex items-start justify-between">
                            <div>
                                <span className="text-xs font-extrabold uppercase tracking-[0.13em] text-secondary">
                                    En comunidad
                                </span>

                                <h2 className="mb-0 mt-2 font-heading text-2xl font-bold tracking-[-0.04em]">
                                    Tu actividad reciente
                                </h2>
                            </div>
                        </div>

                        <div className="my-6 flex flex-col gap-5">

                            {user.actividad_reciente.length > 0 ? (
                            user.actividad_reciente.map((actividad) =>{
                                const accion = actividad.tipo === 'comentario' ? "Comentaste" : "Publicaste";
                                
                                return (
                                    <div className="flex gap-3">
                                        <div>
                                            <span className="text-sm font-bold">{accion}</span>
                                            <p className="my-1 text-xs text-neutral-foreground">
                                                {actividad.contenido}
                                            </p>
                                            <small className="text-xs text-neutral-foreground">
                                                {actividad.created_at}
                                            </small>
                                        </div>
                                    </div>
                                );
                            })
                            ) : (
                                <div className="flex gap-3">
                                    <span className="text-sm">Aún no tienes actividad.</span>
                                </div>
                            )}
                        </div>

                        {/* <button
                            type="button"
                            className="inline-flex items-center gap-2 border-0 bg-transparent p-0 text-xs font-bold text-primary"
                        >
                            Ver toda mi actividad
                            <ExternalLink size={14} />
                        </button> */}
                    </section>


                    {/* Community */}
                    <aside className="relative overflow-hidden rounded-2xl border border-border bg-primary 
                    p-5 md:p-6 text-secondary-foreground flex flex-col justify-between">
                        <div className="absolute right-[26px] top-6 grid size-[45px] place-items-center rounded-full 
                        bg-secondary text-secondary-foreground">
                            <Users size={22} />
                        </div>
                        <div>
                            <span className="text-xs font-extrabold uppercase tracking-[0.13em] text-primary-foreground/70">
                                Tu comunidad
                            </span>

                            <h2 className="mt-5 max-w-[220px] font-heading text-xl font-bold tracking-[-0.04em]">
                                {user.oposicion.nombre}
                            </h2>
                            <p className="mb-5 mt-3 max-w-[280px] text-sm leading-relaxed text-primary-foreground/70">
                                Comparte avances, resuelve dudas y encuentra a otros opositores.
                            </p>
                        </div>

                        <Link
                            href={route('foro.index')}
                            className="inline-flex items-center gap-2 rounded-lg bg-white w-fit 
                            px-4 py-3 text-xs font-semibold text-primary transition-colors hover:bg-indigo-50"
                        >
                            Ir al foro
                            <ArrowRight size={15} />
                        </Link>
                    </aside>
                </div>
            </div>
        </AppLayout>
        
    );
}