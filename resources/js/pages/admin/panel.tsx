
import { 
    ArrowRight, BookOpen, FileQuestion,
    Flag, Link2, ShieldCheck, Users, BookOpenCheck,
} from 'lucide-react'

import AppLayout from '@/layouts/AppLayout';

import { ComunidadDropdown, type ComunidadOption } from '@/components/admin/ComunidadDropdown';
import { type FormEventHandler } from 'react';
import { useForm, router, Link } from '@inertiajs/react';
import { type Oposicion } from '@/types/models';

import AdminPanelCard from '@/components/admin/AdminPanelCard';
import { type Reporte } from '@/types/models';
import ReportePendienteCard from '@/components/admin/ReportePendienteCard';
import { SectionHeading } from '@/components/ui/SectionHeading';


type AdminPanelProps = {
    oposicion_moderada: Oposicion; // id, nombre
    reportes_pendientes: Reporte[];
    reportes_count: number;
    recursos_count: number;
    tests_count: number;
    usuarios_count: number;

    // id (oposicion), nombre (oposicion), usuarios_count, reportes_count 
    comunidades: ComunidadOption[]; 
}


export default function Panel(
    {oposicion_moderada, reportes_pendientes, reportes_count, 
    recursos_count, tests_count, usuarios_count, comunidades}
    : AdminPanelProps
) {

    // TODO. Buscador + eager load

    // const [search, setSearch] = useState('')

    // const visibleReports = reports.filter(
    //     (report) =>
    //     !resolved.includes(report.id) &&
    //     (selectedCommunity === oppositionOptions[0] ||
    //         report.community === selectedCommunity) &&
    //     `${report.title} ${report.detail} ${report.reason}`
    //         .toLowerCase()
    //         .includes(search.toLowerCase())
    // )


    /** Cambiar de comunidad */
    const { data, setData, post, processing, errors, clearErrors,} =
        useForm<{oposicion_id: string}>({
            oposicion_id: '',
        });

    // post de este formulario: envía data (oposicion_id) en el cuerpo de la petición
    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(route('admin.select'));
    };

    const selectedOposicion = comunidades.find((c) => (c.id === data.oposicion_id));


    /** Acciones sobre reportes */

    // Omitir
    const omitirReporte = (reporte: Reporte) => {
        router.post(
            route('admin.reportes.omit', { reporte: reporte.id }),
            { },
            { preserveScroll: true }
        );
    };  

    // Eliminar contenido
    const deleteContent = (reporte: Reporte) => {
        router.delete(
            route('admin.reportes.delete', { reporte: reporte.id }),
            { preserveScroll: true }
        );
    }; 

    // Banear usuario
    const banUsuario = (reporte: Reporte) => {
        router.delete(
            route('admin.reportes.ban', { reporte: reporte.id }),
            { preserveScroll: true }
        );
    };

    return (
        <AppLayout headTitle={`Administración de ${oposicion_moderada.nombre} | Proposite`}>
            <div className="mx-auto w-full max-w-6xl px-4 py-6 flex flex-col gap-10">

                <SectionHeading 
                    as='h1' 
                    eyebrow={`Vista general · ${oposicion_moderada.nombre}`} 
                    title='Panel de administración' 
                    size='xl'
                    titleClassName='text-primary'
                    eyebrowIcon={
                        <ShieldCheck className="md:size-4 size-6 text-secondary" />
                    }
                />
                
                <form onSubmit={submit} className="relative flex gap-3">
                    <ComunidadDropdown
                        comunidades={comunidades}
                        oposicion_id={data.oposicion_id}
                        onChange={(id) => {
                            setData('oposicion_id', id);
                            clearErrors('oposicion_id');
                        }}
                        error={errors.oposicion_id}
                        disabled={processing}
                        variant='compact'
                    />
                    
                    <button
                        type="submit"
                        disabled={!selectedOposicion}
                        className="inline-flex items-center justify-center gap-2 rounded-sm bg-primary 
                        p-3 text-sm font-bold text-primary-foreground transition hover:bg-tertiary 
                        hover:text-tertiary-foreground disabled:cursor-not-allowed disabled:opacity-45"
                    >
                        <ArrowRight className="size-4" />
                    </button> 
                </form>

                <section className="flex flex-wrap justify-between gap-3">
                    <AdminPanelCard Icon={Flag} title='Reportes pendientes' count={reportes_count} 
                        link={{
                            label:'Requieren revisión', 
                            href:'#reportes-pendientes'
                        }} 
                    />
                    <AdminPanelCard Icon={Users} title='Usuarios' count={usuarios_count} 
                        link={{
                            label:'Ver usuarios', 
                            href:route('admin.usuarios.index')
                        }} 
                    />

                    <AdminPanelCard Icon={BookOpenCheck}  title='Recursos' count={recursos_count} 
                        link={{
                            label:'Recursos publicados', 
                            href:route('temario.index')
                        }} 
                    />

                    <AdminPanelCard Icon={BookOpen} title='Tests' count={tests_count} 
                        link={{
                            label:'Tests publicados', 
                            href:route('tests.index')
                        }} 
                    />
                </section>

                <section id='reportes-pendientes' className="flex gap-7 lg:flex-row flex-col">
                    <div className='lg:min-w-[67%]'>
                        <div className="flex flex-col border-b border-border pb-5
                        sm:flex-row sm:items-center sm:justify-between">
                            <SectionHeading 
                                as='h2' 
                                eyebrow='Cola de moderación' 
                                title='Reportes pendientes' 
                                size='md'
                            />

                            {/* <label className="flex items-center gap-2 rounded-xl border border-border px-3 py-2">
                                <Search className="size-4 text-neutral-foreground" />

                                <span className="sr-only">Buscar reportes</span>

                                <input
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                                className="w-full min-w-0 bg-transparent text-sm outline-none placeholder:text-neutral-foreground"
                                placeholder="Buscar reporte"
                                />
                            </label> */}
                        </div>

                        <div className="flex flex-col gap-5 divide-y divide-border mt-10">
                            {/* {visibleReports.map((report) => ( */}
                            {reportes_pendientes.length > 0 ? (
                                reportes_pendientes.map((reporte) => (
                                    <ReportePendienteCard 
                                        key={reporte.id}
                                        reporte={reporte}
                                        onOmitir={omitirReporte}
                                        onEliminar={deleteContent}
                                        onBanear={banUsuario}
                                    />
                                ))
                            ) : (
                                <p className='text-sm text-neutral-foreground'>
                                    Sin reportes pendientes en esta comunidad
                                </p>
                            )}

                            {/* {visibleReports.length === 0 && (
                                <div className="p-10 text-center text-sm text-neutral-foreground">
                                No hay reportes que coincidan con los filtros.
                                </div>
                            )} */}
                        </div>
                    </div>

                    <aside className="w-full lg:w-auto h-fit">
                        <section className="flex flex-col gap-2 rounded-xl border border-tertiary-foreground/15! 
                        bg-tertiary p-5">
                            <SectionHeading 
                                as='h3'
                                eyebrow='Herramientas' 
                                title='Crear contenido' 
                                titleClassName='text-primary' 
                                size='md'
                            />

                            <p className="text-sm text-tertiary-foreground">
                                Añade material oficial para que cada comunidad tenga recursos
                                fiables y actualizados.
                            </p>

                            <div className="flex flex-col gap-2 mt-5">
                                {/* TODO. */}
                                <Link
                                    href={route('admin.tests.create')}
                                    className="flex items-center gap-3 rounded-xl bg-white px-4 
                                    py-3 text-left text-sm font-semibold text-foreground transition 
                                    hover:bg-secondary hover:text-secondary-foreground"
                                >
                                    <FileQuestion className="size-4 text-primary" />
                                    Crear test
                                    <span className="ml-auto">→</span>
                                </Link>

                                {/* TODO. */}
                                <button
                                    type="button"
                                    className="flex items-center gap-3 rounded-xl bg-white px-4 
                                    py-3 text-left text-sm font-semibold text-foreground transition
                                    hover:bg-secondary hover:text-secondary-foreground"
                                >
                                    <Link2 className="size-4 text-primary" />
                                    Añadir recurso legal
                                    <span className="ml-auto">→</span>
                                </button>
                            </div>
                        </section>

                        {/* <section className="rounded-2xl border border-border bg-background p-5">
                            <div className="flex items-center gap-3">
                                <div className="flex size-9 items-center justify-center rounded-lg bg-tertiary text-primary">
                                    <Activity className="size-4" />
                                </div>

                                <div>
                                    <h2 className="text-sm font-bold">
                                        Actividad reciente
                                    </h2>

                                    <p className="text-xs text-neutral-foreground">
                                        Últimas acciones del equipo
                                    </p>
                                </div>
                            </div>

                            <ul className="mt-5 space-y-4 text-xs">
                                <li className="flex gap-3">
                                    <span className="mt-1 size-2 shrink-0 rounded-full bg-secondary" />

                                    <span className="text-neutral-foreground">
                                        <strong className="text-foreground">Marta S.</strong>{' '}
                                        resolvió un reporte

                                        <span className="mt-1 block text-[10px]">
                                        Hace 8 min
                                        </span>
                                    </span>
                                </li>

                                <li className="flex gap-3">
                                    <span className="mt-1 size-2 shrink-0 rounded-full bg-primary" />

                                    <span className="text-neutral-foreground">
                                        <strong className="text-foreground">David R.</strong>{' '}
                                        publicó un recurso legal

                                        <span className="mt-1 block text-[10px]">
                                        Hace 42 min
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </section> */}
                    </aside>
                </section>
            </div>

        </AppLayout>
    )
}