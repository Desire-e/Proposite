import {  
    ArrowRight, Bell, Heart, MessageCircle, MoreHorizontal, Send,  Sparkles, UserRound,
    // ShieldCheck, ChevronDown, Search,
} from 'lucide-react';
import { Link, router, useForm, usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';

import UserAvatar from '@/components/ui/UserAvatar';
import AppLayout from '@/layouts/AppLayout';
import { FeedbackModal } from '@/components/ui/FeedbackModal';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
} from '@/components/ui/dropdown-menu';

import { FormEventHandler, useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/Textarea';



type Oposicion = {
    nombre: string;
    descripcion: string;
}

type Usuario = {
    id: string;
    name: string;
}

type Publicacion = {
    id: string;
    contenido: string;
    created_at: string;
    
    usuario: Usuario;
    me_sirvio_count: number;
    comentarios_count: number;
    me_sirvio_activo: boolean;
}


type ForoProps = {
    oposicion: Oposicion;
    publicaciones?: Publicacion[] | [];
    miembros: number;
}


export default function Foro(
    { oposicion, publicaciones = [], miembros = 0 }: ForoProps
) {

    /**
     * Buscador y filtros
     */

    // const [query, setQuery] = useState('')
    // const [activeFilter, setActiveFilter] = useState('Más recientes')

    // const visiblePosts = useMemo(
    //     () =>
    //         posts.filter((post) =>
    //             `${post.name} ${post.content} ${post.tag ?? ''}`
    //                 .toLowerCase()
    //                 .includes(query.toLowerCase()),
    //         ),
    //     [posts, query],
    // )


    /**
     * Calcular tiempo desde que fue publicada una publicación
     */

    function publishedSince(postDateStr: string) {
        const currentDate = new Date();
        const postDate = new Date(postDateStr);

        const diffMs = currentDate.getTime() - postDate.getTime();

        const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diffMs / (1000 * 60 * 60)) % 24);
        const mins = Math.floor((diffMs / (1000 * 60)) % 60);
        const secs = Math.floor((diffMs / 1000) % 60);
        
        const since = days >= 1 ? `Hace ${days} días` : 
            hours >= 1 ? `Hace ${hours} horas` : 
                mins >= 1 ?  `Hace ${mins} minutos` : 
                    secs >= 1 ? `Hace ${secs} segundos` : 'Recién publicado';

        return since;
    }

    /**
     * Crear publicación
     */

    const { data, setData, post, processing, errors, clearErrors, reset, isDirty} = 
        useForm<{contenido: string}>({contenido: '', });

    const insertPublicacion: FormEventHandler = (event) => {
        event.preventDefault();

        post(
            route('foro.publicacion.insertPublicacion'), 
            { onSuccess: () => reset('contenido'), }
        );
    };

    /**
     * Eliminar publicación propia
     */

    // Si hay id, el modal está abierto
    const [postToDelete, setPostToDelete] = useState<string | null>(null);
    const deleteForm = useForm({});

    const deletePublicacion = () => {
        if (!postToDelete) return;

        deleteForm.delete(
            route('foro.publicacion.eliminar', postToDelete), 
            {
                preserveScroll: true,
                only: ['publicaciones'],
                onFinish: () => setPostToDelete(null),
            }
        );
    };

    const { auth } = usePage<SharedData>().props;


    /**
     * Reportar publicación ajena
     */

    const [postToReport, setPostToReport] = useState<string | null>(null);
    const reportForm = useForm({ motivo: '' });

    const closeReport = () => { 
        setPostToReport(null);
        reportForm.reset();
        reportForm.clearErrors();
    };

    const reportPublicacion = () => {
        if (!postToReport) return;

        reportForm.post(
            route('foro.publicacion.reportar', postToReport), 
            {
                preserveScroll: true,
                onSuccess: closeReport,
            }
        );
    };


    /**
     * Toggle me sirvió optimista (automático)
     */

    const toggleMeSirvio = (publicacion: Publicacion) => {
        router.post(
            route('foro.publicacion.meSirvio', publicacion.id), 
            {}, 
            { 
                preserveScroll: true, 
                // Partial reload: Opción de visita de Inertia, que pide 
                // al servidor solo algunas props de la página.   
                only: ['publicaciones'] 
            }
        );
    }

    return (
        <AppLayout headTitle={`Foro de ${oposicion.nombre} | Proposite`}>
            
            {/* Modal eliminar publicación */}
            <FeedbackModal
                // abierto si hay id publicación en el estado
                open={postToDelete !== null}
                tone="danger"
                title="¿Eliminar esta publicación?"
                description="La publicación y sus comentarios se eliminarán de forma permanente."
                confirmLabel="Eliminar"                
                processing={deleteForm.processing}
                onConfirm={deletePublicacion}
                onClose={() => setPostToDelete(null)}
            />

            {/* Modal reportar publicación */}
            <FeedbackModal
                open={postToReport !== null}
                tone="warning"
                title="Reportar publicación"
                description="Cuéntanos qué ocurre. Un moderador la revisará."
                confirmLabel="Enviar reporte"
                processing={reportForm.processing}
                onConfirm={reportPublicacion}
                onClose={closeReport}
            >
                <div className="mt-4">
                    <Label htmlFor="motivo" className="sr-only">Motivo del reporte</Label>
                    <Textarea
                        id="motivo"
                        rows={4}
                        value={reportForm.data.motivo}
                        onChange={(e) => {
                            reportForm.setData('motivo', e.target.value);
                            reportForm.clearErrors('motivo');
                        }}
                        errors={!!reportForm.errors.motivo}
                        placeholder="Describe el motivo del reporte"
                        disabled={reportForm.processing}
                    />
                    <InputError message={reportForm.errors.motivo} />
                </div>                    
            </FeedbackModal>
            
            <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8 lg:py-10">
            
                {/* Hero */}
                <section className="overflow-hidden rounded-3xl border border-border bg-tertiary px-6 py-7 lg:px-10 lg:py-9">
                    <div className="flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                        <div>
                            <div className="mb-3 inline-flex items-center gap-2 text-xs font-bold uppercase 
                            tracking-[0.16em] text-secondary">
                                <Sparkles className="size-4" />
                                Tu comunidad
                            </div>
                            
                            <h1 className="font-serif text-3xl font-bold tracking-tight text-primary lg:text-5xl">
                                Foro de {oposicion.nombre}
                            </h1>

                            <p className="mt-3 max-w-2xl text-sm leading-6 text-neutral-foreground">
                                Un espacio para resolver dudas, compartir recursos y avanzar acompañado con personas que preparan la misma oposición.
                            </p>

                            <div className="mt-5 flex flex-wrap gap-3">

                                <span className="rounded-full bg-background px-3 py-1.5 text-xs font-semibold 
                                text-tertiary-foreground">
                                    {miembros.toLocaleString('es-ES')} opositores
                                </span>
                            </div>
                        </div>
                    </div>
                </section>


                {/* Contenido principal */}
                <div className="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <section>

                        {/* Crear publicación */}
                        <div className="rounded-2xl border border-border bg-white p-5">
                            <div className="flex md:flex-row flex-col items-start gap-3">
                                <UserAvatar size='sm' />

                                <form className="min-w-0 flex-1" noValidate onSubmit={insertPublicacion}>
                                    <Label htmlFor="contenido" className="sr-only">
                                        Crear una publicación
                                    </Label>

                                    <Textarea
                                        id="contenido"
                                        value={data.contenido}
                                        onChange={(e) => {
                                            setData('contenido', e.target.value);
                                            clearErrors('contenido');
                                        }}
                                        errors={errors.contenido ? true : false}
                                        rows={6} 
                                        placeholder="¿Qué quieres compartir con tu comunidad?"
                                        disabled={processing}
                                        className="resize-none"
                                        // className="min-h-24 w-full resize-none rounded-xl border border-border bg-neutral px-4 py-3 text-sm outline-none placeholder:text-neutral-foreground focus:border-primary focus:ring-2 focus:ring-ring/30"
                                    />
                                    <InputError message={errors.contenido} />

                                    <div className="mt-3 flex items-center justify-between gap-3">
                                        <Button
                                            type="submit"
                                            disabled={processing || !isDirty}
                                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-primary 
                                            px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
                                        >
                                            <Send className="size-4" />
                                            Publicar
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {/* Conversaciones */}
                        <div className="mt-7 flex flex-col gap-4">
                            <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                <div>
                                    <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                                        Conversaciones
                                    </p>

                                    <h2 className="mt-1 font-serif text-2xl font-bold">
                                        Lo que está pasando
                                    </h2>
                                </div>

                                {/* TODO. Buscador y filtros */}
                                
                                {/* <div className="flex items-center gap-2">
                                    <label className="flex items-center gap-2 rounded-xl border border-border bg-background px-3 py-2">
                                        <Search className="size-4 text-neutral-foreground" />

                                        <span className="sr-only">
                                            Buscar publicaciones
                                        </span>

                                        <input
                                            value={query}
                                            onChange={(event) =>
                                                setQuery(event.target.value)
                                            }
                                            className="w-32 bg-transparent text-xs outline-none placeholder:text-neutral-foreground"
                                            placeholder="Buscar"
                                        />
                                    </label>

                                    <button
                                        type="button"
                                        onClick={() =>
                                            setActiveFilter(
                                                activeFilter === 'Más recientes'
                                                    ? 'Más populares'
                                                    : 'Más recientes',
                                            )
                                        }
                                        className="inline-flex items-center gap-1 rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold text-neutral-foreground"
                                    >
                                        {activeFilter}
                                        <ChevronDown className="size-3" />
                                    </button>
                                </div> */}
                            </div>

                            {/* Listado de publicaciones */}
                            {publicaciones?.map((publicacion)=>{
                                const esAutor = publicacion.usuario.id === auth.user.id;
                                
                                return (
                                <article key={publicacion.id} className="rounded-2xl border border-border bg-white p-5">
                                    <div className="flex md:flex-row flex-col items-start gap-3">

                                        <UserAvatar username={publicacion.usuario.name}/>

                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-start justify-between gap-3">
                                                <div>
                                                    <p className="mt-0.5 text-xs text-neutral-foreground">
                                                        {publishedSince(publicacion.created_at)}
                                                    </p>
                                                </div>

                                                {/* Eliminar publicación */}
                                                <div>
                                                    <DropdownMenu 
                                                    >
                                                        <DropdownMenuTrigger asChild>
                                                            <button 
                                                            type="button" 
                                                            aria-label="Más opciones" 
                                                            className="text-neutral-foreground hover:text-primary">
                                                                <MoreHorizontal className="size-5" />
                                                            </button>
                                                        </DropdownMenuTrigger>

                                                        <DropdownMenuContent 
                                                            className="bg-white border-1 rounded-sm border-muted-foreground/30"
                                                            align="end"
                                                        >
                                                            {esAutor ? (

                                                            <DropdownMenuItem
                                                                onSelect={() => setPostToDelete(publicacion.id)}
                                                                className="cursor-pointer px-3 py-2 text-neutral-foreground 
                                                                focus:bg-neutral focus:text-primary"
                                                            >
                                                                Eliminar
                                                            </DropdownMenuItem>

                                                            ) : (

                                                            <DropdownMenuItem
                                                                onSelect={() => setPostToReport(publicacion.id)}
                                                                className="cursor-pointer px-3 py-2 text-neutral-foreground 
                                                                focus:bg-neutral focus:text-primary"
                                                            >
                                                                Reportar
                                                            </DropdownMenuItem>

                                                            )}
                                                        </DropdownMenuContent>
                                                    </DropdownMenu>
                                                </div>
                                            </div>
                                            
                                            {/* TODO. Poner link a la publicacion */}
                                            <p className="mt-4 text-sm leading-7 text-foreground">
                                                {publicacion.contenido}
                                            </p>

                                            {/* {post.tag && (
                                                <span className="mt-4 inline-flex rounded-full bg-tertiary px-2.5 py-1 text-[11px] font-bold text-tertiary-foreground">
                                                    #{post.tag}
                                                </span>
                                            )} */}

                                            {/* Acciones de la publicación */} 
                                            <div className="mt-5 flex items-center gap-5 border-t border-border pt-4 text-xs text-neutral-foreground">
                                                <button
                                                    type="button"
                                                    onClick={() => toggleMeSirvio(publicacion)}
                                                    aria-pressed={publicacion.me_sirvio_activo}
                                                    className={`inline-flex items-center gap-1.5 transition ${
                                                        publicacion.me_sirvio_activo ? 'font-bold text-primary' : 'hover:text-primary'
                                                    }`}
                                                >
                                                    <span>{publicacion.me_sirvio_count}</span>
                                                    <Heart className="size-4" fill={publicacion.me_sirvio_activo ? 'currentColor' : 'none'} />
                                                    Me sirvió
                                                </button>
                                                
                                                {/* TODO. Ruta a /publicacion/{id}#comentarios */} 

                                                <button type="button" className="inline-flex items-center gap-1.5 hover:text-primary">
                                                    {publicacion.comentarios_count} 
                                                    <MessageCircle className="size-4" />
                                                    Comentarios
                                                </button> 
                                                {/* <button type="button" className="inline-flex items-center gap-1.5 hover:text-primary">
                                                    <MessageCircle className="size-4" />
                                                    {post.comments} comentarios
                                                </button> */}
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                );
                            }
                                
                        
                            )}
                        </div>
                    </section>

                    {/* Barra lateral */}
                    <aside className="space-y-4">
                        {/* Normas de la comunidad */}
                        <section className="rounded-2xl border border-border bg-white p-5">
                            <div className="mb-4 flex size-10 items-center justify-center rounded-xl bg-tertiary text-primary">
                                <UserRound className="size-5" />
                            </div>

                            <h2 className="font-serif text-xl font-bold">
                                Normas de la comunidad
                            </h2>

                            <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                                Un lugar seguro para preguntar, compartir y apoyarnos durante la preparación.
                            </p>

                            <ul className="mt-4 mx-3 space-y-3 text-xs leading-5 list-disc text-neutral-foreground">
                                <li>Respeta los ritmos y experiencias de los demás.</li>
                                <li>Comparte información útil y contrastada.</li>
                                <li>No publiques datos personales.</li>
                            </ul>
                        </section>

                        {/* Acceso al temario */}
                        <section className="rounded-2xl border border-border bg-tertiary p-5">
                            <Bell className="size-5 text-primary" />

                            <h2 className="mt-4 font-serif text-xl font-bold text-primary">
                                ¿Necesitas ayuda?
                            </h2>

                            <p className="mt-2 text-sm leading-6 text-tertiary-foreground">
                                Explora el temario oficial y participa en conversaciones sobre tus temas.
                            </p>

                            <Link
                                href={route('temario.index')}
                                className="mt-4 inline-flex items-center gap-2 text-xs font-bold text-primary 
                                hover:text-tertiary-foreground"
                            >
                                Ir al temario
                                <ArrowRight className="size-4" />
                            </Link>
                        </section>
                    </aside>
                </div>

            </div>

        </AppLayout>

    )
}
