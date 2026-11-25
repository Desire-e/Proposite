import { ShieldCheck, UserRound, UserRoundCheck, UserRoundX } from 'lucide-react'
import AppLayout from '@/layouts/AppLayout';
import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';


type Opositor = {
    id: string;
    name: string;
    email: string;
    created_at: string;
    baneado: { 
        deleted_at: string, 
        deleted_by: string 
    } | null;
}

export default function OpositoresList(
    { oposicion, opositores = [], administradores = [] }: 
    { oposicion: string, opositores?: Opositor[], administradores: string[] }
) {

    const baneadosCount = opositores.filter((op) => op.baneado !== null).length;
    const noBaneadosCount = opositores.length - baneadosCount;
    const usuariosCount = opositores.length;

    const updateBan = ({usuarioId, baneado}: {usuarioId: string, baneado: boolean}): void => {
        
        if (baneado) {
            router.patch(
                route('admin.usuarios.recover', { usuario: usuarioId }),
                { },
                { preserveScroll: true }
            );

            return;
        }

        router.delete(
            route('admin.usuarios.ban', { usuario: usuarioId }),
            { preserveScroll: true }
        );

    }


    return (
        <AppLayout headTitle='Usuarios | Proposite'>
            <div className="mx-auto w-full max-w-6xl px-4 py-6">
                
                <div className='flex flex-col mb-4'>
                    <p className="mb-2 flex items-center gap-2 text-xs font-bold 
                    uppercase tracking-[0.16em] text-secondary">
                        <ShieldCheck className="size-4" />
                        Usuarios · {oposicion}
                    </p>

                    <h1 className="font-heading text-4xl font-bold text-primary lg:text-5xl">
                        Gestión de usuarios
                    </h1>
                </div>


                <section>
                    <div className="flex flex-col border-b border-border p-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 className="text-2xl font-bold font-heading">Cuentas de la comunidad</h2>

                            <p className="mt-1 text-xs text-neutral-foreground">
                                {noBaneadosCount} de {usuariosCount} cuentas de usuario en activo
                            </p>
                        </div>

                        {/* <div className="border border-border bg-white flex flex-col gap-3 sm:flex-row">
                            <label className="flex h-11 items-center gap-2 rounded-xl border border-border px-3">
                                <Search aria-hidden="true" className="size-4 text-neutral-foreground" />

                                <span className="sr-only">Buscar usuarios por nombre o email</span>

                                <input
                                    // value={search}
                                    // onChange={(event) => setSearch(event.target.value)}
                                    placeholder="Buscar nombre o email"
                                    className="w-full bg-transparent text-sm outline-none placeholder:text-neutral-foreground sm:w-56"
                                />
                            </label>

                            <label className="sr-only" htmlFor="user-status">
                                Filtrar por estado
                            </label>

                            <select
                                id="user-status"
                                // value={showBanned}
                                // onChange={(event) => setShowBanned(event.target.value as typeof showBanned)}
                                className="h-11 rounded-xl border border-border bg-background px-3 text-sm font-medium"
                            >
                                <option value="all">Todos los estados</option>
                                <option value="active">Usuarios activos</option>
                                <option value="banned">Usuarios baneados</option>
                            </select>
                        </div> */}
                    </div>


                    <div className="divide-y divide-border">
                        {administradores.map((user, i) => (
                            <article key={`admin-${i}`} className="flex flex-col bg-tertiary gap-4 p-5 sm:flex-row sm:items-center">
                                <div className="flex min-w-0 flex-1 items-center gap-3">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-full bg-secondary text-secondary-foreground">
                                        <UserRound className="size-5" />
                                    </span>

                                    <div className="flex gap-3 flex-wrap">
                                        <h3 className="font-semibold">
                                            {user} 
                                        </h3>
                                        <div className='flex items-center'>
                                            <span className="rounded-full bg-white px-3 py-1 text-xs font-semibold text-tertiary-foreground">
                                                Administrador
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        ))}

                        {opositores.map((user) => (
                            <article key={user.id} className="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
                                <div className="flex min-w-0 flex-1 items-center gap-3">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-full bg-tertiary text-primary">
                                        <UserRound className="size-5" />
                                    </span>

                                    <div className="min-w-0">
                                        <div className='flex gap-2 flex-wrap mb-2'>
                                            <h3 className="font-semibold">
                                                {user.name}
                                            </h3>
                                            <div className='flex items-center'>
                                                <span className="rounded-full bg-tertiary px-3 py-1 text-xs font-semibold text-tertiary-foreground">
                                                    Miembro
                                                </span>

                                                <span className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold 
                                                ${user.baneado ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'}`}>
                                                    {user.baneado 
                                                        ? (<><UserRoundX className="size-3.5" />Baneado</>) 
                                                        : (<><UserRoundCheck className="size-3.5" />Activo</>)
                                                    }
                                                </span>
                                            </div>
                                        </div>
                                            
                                        <p className="truncate text-sm text-neutral-foreground">
                                            {user.email}
                                        </p>

                                        <p className="mt-1 text-xs text-neutral-foreground">
                                            {/* TODO. Usar utils */}
                                            Se unió el {' '}
                                            {new Intl.DateTimeFormat('es-ES', 
                                                { dateStyle: 'medium' }).format(new Date(user.created_at)
                                            )}
                                        </p>
                                    </div>
                                </div>


                                <div className="flex flex-wrap items-center gap-3 sm:justify-end">
                                    <Link
                                        href={route('opositores.show', user.id)}
                                        className="flex items-center min-h-10 rounded-lg border border-border 
                                        px-4 p-0 text-sm font-semibold transition hover:border-primary 
                                        hover:border-tertiary-foreground! bg-tertiary text-tertiary-foreground 
                                        disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        Ver perfil
                                    </Link>

                                    <button
                                        type="button"
                                        onClick={() =>{
                                            updateBan({usuarioId: user.id, baneado: user.baneado !== null});
                                        }}
                                        className="min-h-10 rounded-lg border border-error-foreground! px-4 p-0 text-sm font-semibold 
                                        transition hover:border-primary hover:text-error hover:bg-error-foreground 
                                        bg-error text-error-foreground disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {user.baneado ? 'Revocar baneo' : 'Banear'}
                                    </button>
                                </div>
                            </article>
                        ))}

                        {!opositores.length && (
                            <p className="px-5 py-12 text-center text-sm text-neutral-foreground">
                                No hay usuarios registrados aún en esta oposición
                            </p>
                        )}
                    </div>
                </section>
            </div>
        </AppLayout>
    )
}