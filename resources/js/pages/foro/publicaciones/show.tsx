
import { ArrowLeft, MessageCircle, Send,} from 'lucide-react'
import AppLayout from '@/layouts/AppLayout'
import UserAvatar from '@/components/ui/UserAvatar'
import { OptionsMenu } from '@/components/OptionsMenu';
import { Link, useForm, usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';
import { FormEventHandler } from 'react';
import { MeSirvioButton } from '@/components/MeSirvioButton';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/Textarea';

import { publishedSince } from '@/lib/dates';

import { useReport } from '@/hooks/use-report';
import { ReportModal } from '@/components/ReportModal';
import { DeleteModal } from '@/components/DeleteModal';
import { useDelete } from '@/hooks/use-delete';

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

    /** Eliminar publicación propia */
    const deletePublicacion = useDelete('foro.publicaciones.delete');

    /** Reportar publicación ajena */
    const reportPublicacion = useReport('foro.publicaciones.reportes.store');

    /** Eliminar comentario propio */
    const deleteComentario = useDelete('foro.comentarios.delete')

    /** Reportar comentario ajeno */
    const reportComentario = useReport('foro.comentarios.reportes.store');

    /** Saber si reportable/eliminable es propio o ajeno */
    // TODO copiar en index
    const { auth } = usePage<SharedData>().props;
    const esAutor = usuario_autor.id === auth.user.id;



    /**
     * Crear comentario
     */
    const { data, setData, post, processing, errors, clearErrors, reset} = 
        useForm<{contenido: string}>({contenido: ''});

    // TODO. Mensaje status   
    // const [notice, setNotice] = useState('')

    const storeComentario: FormEventHandler = (event) => {
        event.preventDefault();

        post(
            route('foro.publicaciones.comentarios.store', publicacion), 
            { 
                preserveScroll: true,
                onSuccess: () => {
                    reset('contenido');
                    //setNotice('Comentario publicado correctamente');
                },
            },
            
        );
    };


  return (
        <AppLayout headTitle="Publicación | Proposite">
            {/* Modal eliminar publicación */}
            <DeleteModal deletion={deletePublicacion} title="¿Eliminar esta publicación?" />

            {/* Modal reportar publicación */}
            <ReportModal report={reportPublicacion} title="Reportar publicación" />

            {/* Modal eliminar comentario */}
            <DeleteModal deletion={deleteComentario} title="¿Eliminar este comentario?" />

            {/* Modal reportar comentario */}
            <ReportModal report={reportComentario} title="Reportar comentario" />


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
                            <OptionsMenu items={esAutor ? 
                                [{ 
                                    label: 'Eliminar', 
                                    onSelect: () => deletePublicacion.open(publicacion.id) 
                                }]
                                : 
                                [{ 
                                    label: 'Reportar', 
                                    onSelect: () => reportPublicacion.open(publicacion.id) 
                                }]
                            } />
                        </div>
                    </div>
                    
                    <p className="mt-5 text-base leading-8 text-foreground">
                        {publicacion.contenido}
                    </p>

                    <div style={{visibility: 'hidden'}} id="comentarios"></div>

                    <div className="mt-8 flex flex-wrap items-center gap-3 border-t border-border pt-5">
                        <MeSirvioButton
                            publicacionId={publicacion.id}
                            activo={me_sirvio_activo}
                            total={me_sirvio}
                            variant='full'
                        />

                        <Link
                            href={`${route('foro.publicaciones.show', publicacion.id)}#comentarios`} 
                            className="inline-flex items-center gap-2 rounded-xl transition px-3 py-2 
                            text-sm font-semibold text-neutral-foreground hover:bg-neutral hover:text-primary"
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
                            <form className="min-w-0 flex-1" noValidate onSubmit={storeComentario}>
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
                        {comentarios?.map((comentario) => {
                            const esAutorComentario = comentario.usuario_id === auth.user.id;

                            return (
                                <article key={comentario.id} className="relative rounded-2xl border border-border bg-background p-5">
                                    <div className="flex items-start justify-between gap-4">
                                        
                                        <div className="flex gap-4">
                                            <UserAvatar userId={comentario.usuario_id} username={comentario.usuario.name} />

                                            <div className="min-w-0">
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

                                        {/* Eliminar / Reportar comentario */}
                                        <div className="relative z-10 shrink-0">
                                            <OptionsMenu
                                                items={ esAutorComentario ? 
                                                    [{
                                                        label: 'Eliminar',
                                                        onSelect: () => deleteComentario.open(comentario.id),
                                                    }]
                                                    : 
                                                    [{
                                                        label: 'Reportar',
                                                        onSelect: () => reportComentario.open(comentario.id),
                                                    }]
                                                }
                                            />
                                        </div>
                                    </div>
                                </article>
                            );
                        })}
                    </div>
                </section>
            </div>
        </AppLayout>
  )
}