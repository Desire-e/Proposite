import { ArrowRight, CalendarDays, Edit3, GraduationCap, Target, Trophy, Users } from "lucide-react";
import AppLayout from '@/layouts/AppLayout';
import UserAvatar from "@/components/ui/UserAvatar";
import { SectionCard } from "@/components/ui/SectionCard";
import { StatCard } from "@/components/ui/StatCard";
import { AccionesContenido } from '@/components/AccionesContenido';
import { Link } from '@inertiajs/react';

import { publishedSince } from '@/lib/dates';
import { useUntilNow } from '@/hooks/use-until-now';
import { formatMY } from "@/lib/dates";

import { ReportModal } from "@/components/ReportModal";
import { useReport } from "@/hooks/use-report";


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
    texto: string;
    publicacion_id?: string; // comentario asociado a una publicacion
    created_at: string;
}

type UserProfile = {
    id: string;
    name: string;
    oposicion: Oposicion;
    biografia?: string;
    signed_up_at: string;
    anio_convocatoria?: string;

    progreso: Progress;
    estadisticas: Stats;
    tema_mas_fuerte: BestSubject | null;
    actividad_reciente: RecentActivity[];
};


type PerfilProps = { user: UserProfile; es_propio: boolean };

export default function Perfil({ user, es_propio }: PerfilProps) {

    /**
     * Reportar usuario
     * Inicializa con ruta de reportes
     */
    const reportUsuario = useReport('opositores.reportes.store');

    /**
     * Mensaje de porcentaje de progreso de temario
     */
    const { porcentaje } = user.progreso;

    const mensajeProgreso = porcentaje < 30
        ? (es_propio ? 'Sigamos avanzando' : 'Está empezando')
        : porcentaje < 80
            ? (es_propio ? 'Vas por buen camino' : 'Va por buen camino')
            : (es_propio ? 'Tu aprobado está en camino' : 'Está cerca de la meta');


    /** Hook propio para actualizar desde cuando se publicó comentario/publicacion */
    const untilNow = useUntilNow();


    return (

        <AppLayout headTitle={es_propio ? "Perfil | Proposite" : `${user.name} | Proposite`}>

            {/* Modal reportar usuario */}
            <ReportModal report={reportUsuario} title="Reportar usuario" />

            <div className="mx-auto w-full max-w-6xl px-4 py-6">

                {/* Profile hero */}
                <SectionCard className="relative flex flex-col gap-6 p-6 shadow-sm md:px-9 md:py-8">

                    <div className="flex md:flex-row flex-col gap-6 md:items-center justify-between">
                        <div className="flex flex-col gap-2 lg:w-[50%] lg:w-[60%]">
                            
                            <div className="grid grid-cols-[auto_1fr] items-center gap-x-4 gap-y-2 sm:gap-x-5">
                                <UserAvatar
                                    size="lg"
                                    className="md:row-span-2"
                                    username={user.name}
                                />

                                <h1 className="min-w-0 break-words font-heading text-3xl font-bold leading-tight
                                    tracking-[-0.06em] md:self-end md:text-4xl">
                                    {user.name}
                                </h1>

                                <p className="col-span-2 flex items-center gap-2 text-sm font-bold text-primary
                                    md:col-span-1 md:col-start-2 md:self-start">
                                    <GraduationCap className="size-4 shrink-0" />
                                    {user.oposicion.nombre}
                                </p>
                            </div>

                            <div className="flex flex-col gap-2 text-sm">

                                {user.biografia &&
                                    <p className="mb-3 mt-3 leading-relaxed text-neutral-foreground">
                                        {user.biografia}
                                    </p>
                                }


                                {/* Fecha registro, convocatoria */}
                                <div className="flex flex-col gap-2 text-neutral-foreground sm:flex-row sm:gap-5">
                                    <span className="flex items-center gap-2 mt-2">
                                        <CalendarDays size={15} /> En Proposite desde {formatMY(user.signed_up_at)}
                                    </span>

                                    {user.anio_convocatoria &&
                                    
                                    <span className="flex items-center gap-2">
                                        <Target size={15} /> Convocatoria {user.anio_convocatoria}
                                    </span>}
                                </div>
                            </div>
                        </div>

                        {/* Solo puede verlo el usuario si no es su propio perfil */}
                        {!es_propio ? (
                            <div className="absolute right-5 md:right-10 top-3 md:top-6">
                                <AccionesContenido
                                    esAutor={false}
                                    onReportar={() => reportUsuario.open(user.id)}
                                />
                            </div>
                        ) : (
                            <Link
                                href={route('perfil.edit')}
                                className="inline-flex w-max md:w-auto shrink-0 items-center gap-2 rounded-lg border border-border 
                                px-4 py-3 text-xs font-semibold text-primary transition-colors hover:bg-tertiary"
                            >
                                <Edit3 size={15} /> Editar perfil
                            </Link>
                        )}

                    </div>
                </SectionCard>


                <div className="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_0.8fr]">
                    
                    <SectionCard 
                        brow={es_propio ? "Tu preparación" : "Su preparación"}
                        title={"Avance del temario"} 
                        link={es_propio ? { label: "Ver temario", route: "temario.index" } : undefined}
                        className="md:p-6"
                    >
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
                                <span className="font-bold">
                                    {mensajeProgreso}
                                </span>

                                <p className="mb-3 mt-2 text-sm leading-relaxed text-neutral-foreground">
                                    {es_propio ? 'Has estudiado' : `${user.name} ha estudiado`}{' '}
                                    {user.progreso.temas_estudiados} de {user.progreso.total_temas} temas del temario oficial.
                                </p>
                            </div>
                        </div>
                    </SectionCard>


                    <SectionCard 
                        brow={es_propio ? "Tu rendimiento" : "Su rendimiento"} 
                        title="Una mirada rápida"
                        className="md:p-6"
                    >
                        <div className="mt-6 grid grid-cols-3 gap-2 sm:gap-2.5">
                            <StatCard variant="tile" value={user.estadisticas.nota_media} label="Nota media" />
                            <StatCard variant="tile" value={user.estadisticas.tests_realizados} label="Tests realizados" />
                            <StatCard variant="tile" value={user.estadisticas.tests_aprobados} label="Tests aprobados" />
                        </div>

                        <div className="mt-5 flex items-center gap-2 border-t border-border pt-4 text-xs text-neutral-foreground">
                            {user.tema_mas_fuerte ? 
                            (<>
                                <Trophy size={16} className="shrink-0 text-secondary" />
                                <span>
                                    {es_propio ? 
                                        'Tu tema más fuerte es ' : 
                                        'Su tema más fuerte es '
                                    }
                                    <span className="font-bold text-primary">
                                        {user.tema_mas_fuerte.titulo.slice(0, user.tema_mas_fuerte.titulo.indexOf('.'))}
                                    </span>
                                </span>
                            </>) : (
                                <span>
                                    {es_propio ? 
                                        'Aún no has realizado ningún test.' : 
                                        'Aún no ha realizado ningún test.'
                                    }
                                </span>
                            )}
                        </div>
                    </SectionCard>
                    
                    <SectionCard 
                        brow={"En comunidad"} 
                        title={"Actividad reciente"} 
                        className={es_propio ? 'md:p-6' : 'md:p-6 lg:col-span-2'}
                    >
                        <div className="my-6 flex flex-col gap-5">
                            {user.actividad_reciente.length > 0 ? (
                            user.actividad_reciente.map((actividad) =>{
                                
                                const esComentario = actividad.tipo === 'comentario';
                                
                                const accion = esComentario ?
                                    (es_propio ? 'Comentaste' : 'Comentó') :
                                    (es_propio ? 'Publicaste' : 'Publicó');
                                
                                return (
                                    <Link 
                                        key={actividad.id} 
                                        href={esComentario 
                                            // Con esto, al hacer clic en un comentario se abre la publicación y el navegador 
                                            // salta al elemento con id="comentario-<uuid>".
                                            ? route('foro.publicaciones.show', actividad.publicacion_id) + `#comentario-${actividad.id}`
                                            : route('foro.publicaciones.show', actividad.id)
                                        }
                                        >
                                        <div  className="flex gap-3">
                                            <div>
                                                <span className="text-sm font-bold">{accion}</span>
                                                <p className="my-1 text-xs text-neutral-foreground">
                                                    {actividad.texto.length > 150 
                                                        // slice para cortar en el índice 100 y añadir puntos suspensivos
                                                        ? actividad.texto.slice(0, 100) + '...'
                                                        : actividad.texto
                                                    }
                                                </p>
                                                <small className="text-xs text-neutral-foreground">
                                                    {publishedSince(actividad.created_at, untilNow)}
                                                </small>
                                            </div>
                                        </div>
                                    </Link>
                                );
                            })
                            ) : (
                                <div className="flex gap-3 text-sm">
                                    {es_propio ? 
                                    'Aún no tienes actividad.' : 
                                        'Aún no tiene actividad.'
                                    }
                                </div>
                            )}
                        </div>
                    </SectionCard>
                    

                    {es_propio && (
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
                    )}
                </div>
            </div>
        </AppLayout>
        
    );
}