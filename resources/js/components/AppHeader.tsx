import { type NavItem, type SharedData } from '@/types';

import { useEffect, useState } from "react";

import { ArrowRight, Menu, X, LogOut } from "lucide-react"

import UserAvatar from './ui/UserAvatar';

import { Icon } from '@/components/icon';
import { Button } from '@/components/ui/button';
import { Link, usePage } from "@inertiajs/react";
import { cn } from '@/lib/utils';
import AppLogo from './AppLogo';



export const mainNavItems: NavItem[] = [
    { title: 'Cómo funciona', url: '#como-funciona', sectionId: 'como-funciona', },
    { title: 'Cómo empezar', url: '#como-empezar', sectionId: 'como-empezar', },
];
export const authMainNavItems: NavItem[] = [
    { title: 'Foro', url: '/foro', },
    { title: 'Temario oficial', url: '/temario', },
    { title: 'Tests', url: '/tests', },
];

export const adminMainNavItems: NavItem[] = [
    { title: 'Panel', url: '/admin/panel', },
    { title: 'Usuarios', url: '/admin/usuarios', },
    { title: 'Reportes', url: '/admin/panel#reportes-pendientes', },
];


export function AppHeader() {
    const page = usePage<SharedData>();
    const { auth } = page.props;

    const esAdmin = auth.user?.rol === "administrador";
    const generalItems = !auth.user ? mainNavItems : authMainNavItems;
    const adminItems = esAdmin ? adminMainNavItems : [];
    
    // Lista plana para el observer y para isItemActive
    const navItems = [...adminItems, ...generalItems];



    const currentPath = page.url.split(/[?#]/)[0]; // divide un objeto String en un array de subcadenas

    // Estado de id/ancla activo
    const [activeAnchor, setActiveAnchor] = useState<string | null>(null);

    // Observa nav items anclas activos/inactivos de la pagina actual
    useEffect(() => {

        const sections = navItems
            .filter((item) => item.sectionId)
            .map((item) => document.getElementById(item.sectionId!))
            .filter((section): section is HTMLElement => section !== null);

        setActiveAnchor(null);

        if (sections.length === 0) return;

        const visibleSections = new Set<HTMLElement>();

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    const section = entry.target as HTMLElement;

                    if (entry.isIntersecting) {
                        visibleSections.add(section);
                    } else {
                        visibleSections.delete(section);
                    }
                });

                const currentSection = [...visibleSections].sort(
                    (a, b) =>
                        Math.abs(a.getBoundingClientRect().top - 100) -
                        Math.abs(b.getBoundingClientRect().top - 100),
                )[0];

                setActiveAnchor(currentSection?.id ?? null);
            },
            {
                rootMargin: '-15% 0px -65% 0px',
                threshold: 0,
            },
        );

        sections.forEach((section) => observer.observe(section));

        return () => observer.disconnect();
    }, [currentPath, auth.user, esAdmin]);



    // Mobile navigation
    const [open, setOpen] = useState(false)
    const closeMenu = () => setOpen(false)

    useEffect(() => {
        if (!open) return;

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === "Escape") {
                closeMenu();
            }
        };

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";

        document.addEventListener("keydown", handleKeyDown);

        return () => {
            document.body.style.overflow = previousOverflow;
            document.removeEventListener("keydown", handleKeyDown);
        };
    }, [open]);



    function isItemActive(item: NavItem): boolean {
        if (item.sectionId) {
            return activeAnchor === item.sectionId;
        } else if (item.url.startsWith('#')) {
            return false;
        } else if (item.url === '/') {
            return currentPath === '/';
        } else {
            return (
                currentPath === item.url ||
                currentPath.startsWith(`${item.url}/`)
            );
        }
    }


    // Links del navegador
    function NavLinks({ items, mobile = false }: { items: NavItem[]; mobile?: boolean }) {
        return items.map((item) => {
            const isActive = isItemActive(item);
            const Tag = item.url.startsWith('#') ? 'a' : Link;

            return (
                <Tag
                    key={item.title}
                    href={item.url}
                    aria-current={isActive ? (item.sectionId ? 'location' : 'page') : undefined}
                    onClick={() => {
                        if (item.sectionId) setActiveAnchor(item.sectionId);
                        if (mobile) closeMenu();
                    }}
                    className={cn(
                        mobile
                            ? 'rounded-lg px-3 py-3 text-base transition hover:bg-tertiary hover:text-primary'
                            : 'inline-flex items-center rounded-full px-3 py-2 transition-colors',
                        isActive ? 'text-primary' : !mobile && 'text-neutral-foreground hover:text-primary',
                    )}
                >
                    {item.title}
                </Tag>
            );
        });
    }



    return (
        <>
        <header className="sticky top-0 z-40 border-b border-slate-200/80 bg-background/95 backdrop-blur">
            <div className="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-6 px-5 lg:px-10">

                <AppLogo lightMode={true} />

                {/* -- Desktop center navigation -- */}

                <nav 
                    className="hidden items-center gap-5 text-base lg:text-sm 
                    font-medium text-neutral-foreground lg:flex"
                    aria-label="Navegación principal"
                >
                    <NavLinks items={adminItems} />

                    {adminItems.length > 0 && (
                        <span aria-hidden="true" className="h-7 w-[2px] bg-border" />
                    )}

                    <NavLinks items={generalItems} />
                </nav>

                {/* -- Desktop right navigation -- */}

                {(!auth.user ? (
                    <div className="hidden items-center gap-3 lg:flex">
                        <Link 
                            href="/login"
                            className="rounded-lg px-4 py-2.5 text-base lg:text-sm font-semibold text-neutral-foreground 
                            transition duration-300 hover:bg-tertiary hover:text-primary"
                        >
                            Iniciar sesión
                        </Link>

                        <Link 
                            href="/register"
                            className="inline-flex items-center gap-2 rounded-lg bg-primary 
                            px-4 py-2.5 text-base lg:text-sm font-semibold text-white shadow-sm 
                            transition duration-300 hover:bg-[#4338CA]"
                        >
                            Crear cuenta <ArrowRight aria-hidden="true" size={16} />
                        </Link>
                    </div>
                ) : (
                    <div className="hidden items-center gap-7 lg:flex">
                        <UserAvatar size='md' userId={auth.user.id} username={auth.user.name} />

                        <Link className="flex items-center gap-2 cursor-pointer" method="post" href={route('logout')} as="button">
                            <Icon iconNode={LogOut} className="h-4 w-4" />
                        </Link>
                    </div>
                ))}

                {/* -- Mobile navigation -- */}

                <Button
                    type="button"
                    variant="ghost"
                    className="text-neutral-foreground transition hover:bg-tertiary lg:hidden text-base lg:text-sm"
                    onClick={() => setOpen((value) => !value)}
                    aria-expanded={open}
                    aria-controls="mobile-navigation"
                    aria-label="Abrir menú"
                >
                    <Menu aria-hidden="true" />
                </Button>

            </div>
        </header>

        {/* -- Mobile navigation -- */}

        {/* Overlay: oscurece el fondo e intercepta los clics */}
        <button
            type="button"
            aria-label="Cerrar menú"
            tabIndex={open ? 0 : -1}
            onClick={closeMenu}
            className={`
                lg:hidden fixed inset-0 z-50 bg-slate-950/40 text-base lg:text-sm
                transition-opacity duration-300 ease-in-out
                ${open ? "pointer-events-auto opacity-100" : "pointer-events-none opacity-0"}
            `}
            aria-hidden={!open}
        />
        
        {/* Panel lateral */}
        <nav
            id="mobile-navigation"
            aria-label="Navegación móvil"
            aria-hidden={!open}
            inert={!open}
            className={`
                lg:hidden fixed inset-y-0 right-0 z-50
                flex h-dvh w-[min(85vw,24rem)] flex-col
                overflow-y-auto bg-background shadow-2xl
                transition-transform duration-300 ease-in-out
                ${open ? "translate-x-0" : "translate-x-full"}
            `}
        >
            {/* Cabecera del panel */}
            <div className="flex min-h-20 items-top justify-between border-b border-border p-5">
                
                {(!auth.user ? (
                    <span className="font-heading text-2xl font-bold">
                        proposite
                    </span>
                ) : (
                    <div className='flex min-w-0 gap-3 items-center'>
                        <UserAvatar size='md' userId={auth.user.id} username={auth.user.name} /> 
                        <p className='font-semibold min-w-0 break-words text-lg'>{auth.user.name}</p>
                    </div>
                ))}

                <Button
                    type="button"
                    variant="ghost"
                    onClick={closeMenu}
                    className="rounded-lg p-2 text-neutral-foreground transition hover:bg-tertiary"
                    aria-label="Cerrar menú"
                >
                    <X aria-hidden="true" />
                </Button>
            </div>

            {/* Navegación */}

            <div className="flex flex-1 flex-col gap-2 overflow-y-auto px-5 py-5 text-sm font-medium text-neutral-foreground">
                <NavLinks items={adminItems} mobile />

                {adminItems.length > 0 && <hr className="my-2 border-border" />}

                <NavLinks items={generalItems} mobile />
            </div>

            {/* Acciones inferiores */}
            <div className="flex flex-col gap-3 border-t border-slate-200 p-5">
                {auth.user ? (<>
                    <Link 
                        className="flex items-center gap-2 cursor-pointer text-sm" 
                        method="post" 
                        href={route('logout')} 
                        as="button"
                    >
                        <Icon iconNode={LogOut} className="h-4 w-4" /> Cerrar sesión
                    </Link>
                </>) : (<>
                    <Link
                        href={route('login')}
                        onClick={() => { closeMenu(); }}
                        className="w-full rounded-lg px-4 py-3 text-base font-semibold text-primary 
                        transition hover:bg-tertiary"
                    >
                        Iniciar sesión
                    </Link>

                    <Link
                        href={route('register')}
                        onClick={() => { closeMenu(); }}
                        className="w-full rounded-lg bg-primary px-4 py-3 text-base font-semibold text-white 
                        transition hover:bg-[#4338CA]"
                    >
                        Crear cuenta
                    </Link>
                </>)}
            </div>
        </nav>
        </>
    )
}
