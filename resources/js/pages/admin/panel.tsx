
import { 
    ArrowRight, BookOpen, FileQuestion,
    Flag, Link2, Plus, ShieldCheck, Users, BookOpenCheck,
} from 'lucide-react'

import AppLayout from '@/layouts/AppLayout';

import { ComunidadDropdown, type ComunidadOption } from '@/components/admin/ComunidadDropdown';
import { type FormEventHandler } from 'react';
import { useForm } from '@inertiajs/react';
import { Oposicion } from '@/types/models';

import AdminPanelCard from '@/components/admin/AdminPanelCard';
import { Reporte } from '@/types/models';
import ReportePendienteCard from '@/components/admin/ReportePendienteCard';


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


    return (
        <AppLayout headTitle={`Administración de ${oposicion_moderada.nombre} | Proposite`}>
            <div className="mx-auto w-full max-w-6xl px-4 py-6">

                <div className='flex flex-col mb-8 '>
                    <p className="mb-2 flex items-center gap-2 text-xs font-bold 
                    uppercase tracking-[0.16em] text-secondary">
                        <ShieldCheck className="size-4" />
                        Vista general · {oposicion_moderada.nombre}
                    </p>

                    <h1 className="font-heading text-4xl font-bold text-primary lg:text-5xl">
                        Panel de administración
                    </h1>
                </div>

                <form onSubmit={submit} className="relative flex gap-3 mb-5">
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
                        // link={{
                        //     label:'Ver usuarios', 
                        //     // TODO. route(...)
                        //     href:'#ejemplo'
                        // }} 
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
                            // TODO. route(...)
                            href:route('tests.index')
                        }} 
                    />
                </section>

                <div id='reportes-pendientes' className="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_350px]">
                    <section>
                        <div className="flex flex-col gap-4 border-b border-border pb-5 pl-0
                            sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p className="text-xs font-bold uppercase tracking-[0.14em] text-secondary">
                                    Cola de moderación
                                </p>
                                <h2 className="mt-1 font-heading text-3xl font-bold">
                                    Reportes pendientes
                                </h2>
                            </div>

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
                                        // TODO.
                                        // onOmitir={omitirReporte}
                                        // onEliminar={eliminarReporte}
                                        // onBanear={banearUsuario}
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
                    </section>

                    <aside className="space-y-6">
                        <section className="rounded-2xl border border-border bg-tertiary p-5">
                            <div className="mb-4 flex items-start justify-between">
                                <div>
                                <p className="text-xs font-bold uppercase tracking-[0.14em] text-secondary">
                                    Herramientas
                                </p>

                                <h2 className="mt-1 font-heading text-2xl font-bold text-primary">
                                    Crear contenido
                                </h2>
                                </div>

                                <Plus className="size-5 text-primary" />
                            </div>

                            <p className="text-sm leading-6 text-neutral-foreground">
                                Añade material oficial para que cada comunidad tenga recursos
                                fiables y actualizados.
                            </p>

                            <div className="mt-5 flex flex-col gap-2">
                                <button
                                type="button"
                                className="flex items-center gap-3 rounded-xl bg-background px-4 py-3 text-left text-sm font-semibold text-foreground hover:bg-secondary hover:text-secondary-foreground"
                                >
                                <FileQuestion className="size-4 text-primary" />
                                Crear test
                                <span className="ml-auto">→</span>
                                </button>

                                <button
                                type="button"
                                className="flex items-center gap-3 rounded-xl bg-background px-4 py-3 text-left text-sm font-semibold text-foreground hover:bg-secondary hover:text-secondary-foreground"
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
                </div>
            </div>

        </AppLayout>
    )
}