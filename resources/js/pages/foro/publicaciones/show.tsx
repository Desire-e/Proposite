
import { ArrowLeft, Heart, MessageCircle, MoreHorizontal, Send,} from 'lucide-react'
import AppLayout from '@/layouts/AppLayout'
import UserAvatar from '@/components/ui/UserAvatar'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, } from '@/components/ui/dropdown-menu';
import { Link, router, useForm, usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';
import { FeedbackModal } from '@/components/ui/FeedbackModal';
import { FormEventHandler, useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/Textarea';


type Usuario = {
    id: string;
    name: string;
}

type Publicacion = {
    id: string;
    oposicion_id: string;
    contenido: string;
    created_at: string;
}


type Comentario = {
    id: string;
    usuario_id: string;
    publicacion_id: string;
    contenido: string;
    created_at: string;
    usuario: Usuario;
}

type PublicacionProps = {
    usuario_autor: Usuario;
    publicacion: Publicacion;
    me_sirvio: number;
    me_sirvio_activo: boolean;
    comentarios?: Comentario[] | [];
}

export default function Publicacion({
    usuario_autor, publicacion, me_sirvio, me_sirvio_activo, comentarios
}: PublicacionProps) {


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
     * Eliminar publicación propia
     */

    const { auth } = usePage<SharedData>().props;
    const esAutor = usuario_autor.id === auth.user.id;

    // Si hay id, el modal está abierto
    const [postToDelete, setPostToDelete] = useState<string | null>(null);
    const deleteForm = useForm({});

    const deletePublicacion = () => {
        if (!postToDelete) return;

        deleteForm.delete(
            route('foro.publicaciones.delete', postToDelete), 
            {
                preserveScroll: true,
                only: ['publicaciones'],
                onFinish: () => setPostToDelete(null),
            }
        );
    };

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
            route('foro.publicaciones.reportes.insert', postToReport), 
            {
                preserveScroll: true,
                onSuccess: () => closeReport(), 
            }
        );
    };

    /**
     * Crear comentario
     */

    const { data, setData, post, processing, errors, clearErrors, reset} = 
        useForm<{contenido: string}>({contenido: ''});

    // TODO. Mensaje status   
    // const [notice, setNotice] = useState('')

    const insertComentario: FormEventHandler = (event) => {
        event.preventDefault();

        post(
            route('foro.publicaciones.comentarios.insert', publicacion), 
            { 
                preserveScroll: true,
                onSuccess: () => {
                    reset('contenido');
                    //setNotice('Comentario publicado correctamente');
                },
            },
            
        );
    };

    /**
     * Toggle me sirvió optimista (automático)
     */

    const toggleMeSirvio = (publicacion: Publicacion) => {
        router.post(
            route('foro.publicaciones.me-sirvio.toggle', publicacion.id), 
            {}, 
            { 
                preserveScroll: true, 
                // Partial reload: Opción de visita de Inertia, que pide 
                // al servidor solo algunas props de la página.   
                // only: ['comentarios'] 
            }
        );
    }



  return (
        <AppLayout headTitle="Publicación | Proposite">
            
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

            <div className="mx-auto max-w-4xl px-6 py-8 lg:px-8 lg:py-10">
                <Link
                    href={route('foro.index')}
                    className="inline-flex items-center gap-2 text-sm font-semibold text-neutral-foreground 
                    transition hover:text-primary"
                >
                    <ArrowLeft className="size-4" />
                    Volver al foro
                </Link>


                <article className="mt-4 rounded-3xl border border-border bg-background p-6 shadow-sm sm:p-9">
                    <div className="flex items-start justify-between gap-4">
                        <div className="flex items-center gap-3">
                        
                            <UserAvatar 
                                username={usuario_autor.name} 
                                userId={usuario_autor.id} 
                            />

                            <div>
                                <h1 className="text-base font-bold">{usuario_autor.name}</h1>
                                
                                <p className="mt-1 text-xs text-neutral-foreground">
                                    {publishedSince(publicacion.created_at)}
                                </p>
                            </div>
                        </div>

                        {/* Eliminar / Reportar publicación */}
                        <div className='relative z-10'>
                            <DropdownMenu>
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
                    
                    <p className="mt-5 text-base leading-8 text-foreground">
                        {publicacion.contenido}
                    </p>

                    <div style={{display:'hidden'}} id="comentarios"></div>

                    <div className="mt-8 flex flex-wrap items-center gap-3 border-t border-border pt-5">
                        {/* TODO. Toggle mesirvio */}                    
                        <button
                            type="button"
                            onClick={() => toggleMeSirvio(publicacion)}
                            aria-pressed={me_sirvio_activo}
                            className={`inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition ${
                                me_sirvio_activo ? 'bg-tertiary text-primary' : 
                                'text-neutral-foreground hover:bg-neutral hover:text-primary'
                            }`}
                        >
                            <Heart className={`size-4 ${me_sirvio_activo ? 'fill-current' : ''}`} />
                            {me_sirvio} Me sirvió
                        </button>

                        <Link
                            href={`${route('foro.publicaciones.show', publicacion.id)}#comentarios`} 
                            className="inline-flex items-center gap-2 px-3 py-2 text-sm text-neutral-foreground font-semibold
                            transition text-neutral-foreground hover:bg-neutral hover:text-primary"
                        >
                            <MessageCircle className="size-4" />
                            {comentarios?.length} Comentarios
                        </Link>
                    </div>
                </article>

                <section className="mt-10">
                    <div className="flex items-end justify-between gap-4">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                                Hilo de conversación
                            </p>

                            <h2 className="mt-2 font-heading text-3xl font-bold">
                                Comentarios
                            </h2>
                        </div>
                    </div>

                    <div className="mt-6 rounded-2xl border border-border bg-tertiary p-4">
                        <div className="flex gap-3">
                            <UserAvatar 
                                userId={auth.user.id} 
                                username={auth.user.name} 
                            />
                            {/* TODO. form insertar comentario */}
                            <form className="min-w-0 flex-1" noValidate onSubmit={insertComentario}>
                                <Label htmlFor="contenido" className="sr-only">
                                    Escribe un comentario
                                </Label>
                                <Textarea
                                    id="contenido"
                                    value={data.contenido}
                                    onChange={(e) => {
                                        setData('contenido', e.target.value);
                                        clearErrors('contenido');
                                    }}

                                    className="resize-none"
                                    // className="min-h-20 w-full resize-none rounded-xl border border-border 
                                    // bg-background px-4 py-3 text-sm outline-none placeholder:text-neutral-foreground 
                                    // focus:border-primary focus:ring-2 focus:ring-ring/30"
                                    placeholder="Responde a la comunidad..."
                                    disabled={processing}
                                    errors={errors.contenido ? true : false}
                                    rows={4} 
                                />

                                <InputError message={errors.contenido} />

                                <div className="mt-3 flex items-center justify-end gap-3">
                                    <Button
                                        type="submit"
                                        disabled={processing || !data.contenido.trim()}
                                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-primary 
                                        px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
                                    >
                                        <Send className="size-4" />
                                        Responder
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* TODO. Message status - accesibilidad */}
                    {/* {notice && (
                    <p role="status" className="mt-3 text-sm font-semibold text-primary">
                        {notice}
                    </p>
                    )} */}

                    <div className="mt-6 space-y-4">
                        {comentarios?.map((comentario) => (
                        <article 
                            key={comentario.id} 
                            className="rounded-2xl border border-border bg-background p-5">
                            <div className="flex gap-3">
                                <UserAvatar userId={comentario.usuario_id} username={comentario.usuario.name} />

                                <div>
                                    <div className="flex flex-wrap items-baseline gap-x-2">
                                        <h3 className="text-sm font-bold">
                                            {comentario.usuario.name}
                                        </h3>

                                        <span className="text-xs text-neutral-foreground">
                                            {publishedSince(comentario.created_at)}
                                        </span>
                                    </div>

                                    <p className="mt-3 text-sm leading-7 text-neutral-foreground">
                                        {comentario.contenido}
                                    </p>
                                </div>
                            </div>
                        </article>
                        ))}
                    </div>
                </section>
            </div>
        </AppLayout>
  )
}