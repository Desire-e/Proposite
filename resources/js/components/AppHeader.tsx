// import { Breadcrumbs } from '@/components/breadcrumbs';
// import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
// import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
// import { UserMenuContent } from '@/components/user-menu-content';
// import { NavigationMenu, NavigationMenuItem, NavigationMenuList, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu';
// import { useInitials } from '@/hooks/use-initials';
// import AppLogoIcon from './app-logo-icon';
// import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

import { /*type BreadcrumbItem,*/ type NavItem, type SharedData } from '@/types';

import {
    UserRound, UserRoundGroup, Bolt, ChartSpline,BookOpen, LogOut,
    Menu, House, LogIn, UserRoundPlus, Sparkles
} from 'lucide-react';

import { Icon } from '@/components/icon';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { cn } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import AppLogo from './AppLogo';

/*************************
 * Elementos de navegación 
 *************************/

const mainNavItems: NavItem[] = [
    { title: 'Inicio', url: '/', icon: House, },
    { title: 'Cómo empezar', url: '#how-to-start', icon: Sparkles, },
];

const rightNavItems: NavItem[] = [
    { title: 'Registro', url: '/register', icon: UserRoundPlus, },
    { title: 'Iniciar sesión', url: '/login', icon: LogIn, },
];

// TODO. Implementar condición - si usuario está autenticado
const authMainNavItems: NavItem[] = [
    { title: 'Inicio', url: '/', icon: House, },
    { title: 'Foro', url: '/foro', icon: UserRoundGroup, },
    { title: 'Configuración', url: '/configuración', icon: Bolt, },
    { title: 'Estadísticas', url: '/estadisticas', icon: ChartSpline, },
    { title: 'Mi temario', url: '/mi-temario', icon: BookOpen, },
];

const authRightNavItems: NavItem[] = [
    { title: 'Perfil', url: '/perfil', icon: UserRound, },
    { title: 'Cerrar sesión', url: '/logout', icon: LogOut, },
];


// Las clases de Tailwind que se aplicarán al elemento de navegación activo
const activeItemStyles = 'bg-selected text-primary';

export function AppHeader() {
    /**
     * TODO.
     * 
     * Acceso a los datos de Inertia.
     * usePage() permite acceder a los datos de la página actual.
     * page.props contiene propiedades enviadas por Laravel vía Inertia. Con desestructuración se extrae auth.
     * useInitials() función que permite calcular las iniciales de un nombre. - alternativa visual al avatar
     */

    const page = usePage<SharedData>();
    const { auth } = page.props;
    // const getInitials = useInitials();
    return (
        <header className="border-b-[2px] border-black/10">
            <div className="mx-auto flex h-[10vh] items-center justify-between px-4 md:max-w-7xl">
                
                <Link href="/" prefetch className="flex h-full items-center space-x-2">
                    <AppLogo lightMode={true} />
                </Link>

                

                {/* MOBILE MENU */}
                
                <div className="lg:hidden">
                    {/* -- Panel deslizante lateral -- */}

                    <Sheet>
                        {/* -- Botón hamburguer -- */}
                    
                        {/* SheetTrigger asChild quita el botón por defecto, renderiza en su 
                        lugar el <Button> pasado como hijo directamente como disparador */}
                        <SheetTrigger asChild>
                            <Button variant="ghost" size="icon" className="mr-2 h-[34px] w-[34px]">
                                <Menu className="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        
                        {/* -- Contenido del panel -- */}
                    
                        <SheetContent 
                            side="right" 
                            className="flex h-full w-64 md:w-100 flex-col items-stretch justify-between bg-background"
                        >
                            
                            {/* Título para accesibilidad oculto */}
                            <SheetTitle className="sr-only">Menu de Navegación</SheetTitle>
                            
                            <SheetHeader className="flex justify-start text-left">
                                <Link href="/" prefetch>
                                    <img 
                                    src='/images/proposite-logo-icon.svg' 
                                    className="w-8 object-contain" 
                                    alt="Logo de Proposite" 
                                    />
                                </Link>
                            </SheetHeader>
                            
                            {/* -- Navegación -- */}
                            <nav className="mt-6 flex h-full flex-1 flex-col">
                                <div className="flex h-full flex-col justify-between text-sm">

                                    <div className="flex flex-col">
                                        {(auth.user ? 
                                        [...authMainNavItems, ...authRightNavItems] : // Autenticado 
                                        [...mainNavItems, ...rightNavItems])        // No autenticado
                                        .map((item) => (
                                            <Link 
                                                key={item.title} 
                                                href={item.url} 
                                                className={cn(
                                                'flex items-center p-2 m-2 space-x-2 font-medium rounded-xl',
                                                'text-sm text-soft-foreground transition-colors',
                                                'hover:bg-hover hover:text-black',
                                                'focus:bg-hover focus:text-black focus:outline-hidden',
                                                page.url === item.url && activeItemStyles
                                            )}>
                                                {item.icon && <Icon iconNode={item.icon} className="h-5 w-5" />}
                                                <span>{item.title}</span>
                                            </Link>
                                        ))}
                                    </div>

                                </div>
                            </nav>

                        </SheetContent>

                    </Sheet>
                    
                </div>


                
                {/* DESKTOP CENTER NAVIGATION */}

                <nav className="hidden h-full min-w-0 flex-1 items-center justify-center lg:flex">
                    <div className="relative flex h-full min-w-0 flex-1 items-center justify-center">
                        
                        <div className="flex h-full items-stretch space-x-2 
                        group flex-1 list-none items-center justify-center">
                            {(auth.user ? 
                            authMainNavItems : 
                            mainNavItems)
                            .map((item) => (

                            <div key={item.title} className="relative flex h-full items-center">
                                <Link
                                href={item.url}
                                className={cn(
                                    'h-9 inline-flex items-center justify-center rounded-full px-3',
                                    'text-soft-foreground text-sm font-medium transition-colors',
                                    'hover:bg-hover hover:text-black',
                                    'focus:bg-hover focus:text-black',
                                    page.url === item.url && activeItemStyles
                                )}
                                >
                                    {item.icon && <Icon iconNode={item.icon} className="mr-2 h-4 w-4" />}
                                    {item.title}
                                </Link>
                            </div>

                            ))}
                        </div>
                    </div>
                </nav>

                    

                {/* DESKTOP RIGHT NAVIGATION */}
                <div className="hidden lg:flex items-center space-x-2 shrink-0 ml-auto">
                    <div className="flex items-center">

                        <div className="flex gap-6">
                            {(auth.user ? 
                            authRightNavItems : 
                            rightNavItems)
                            .map((item) => (

                            <Link href={item.url} key={item.title} className="flex items-center gap-2">
                                {item.icon && <Icon iconNode={item.icon} className="h-5 w-5" /> }
                                { !auth.user && item.title}
                            </Link>

                            ))}
                        </div>
                        
                    </div>

                    
                    {/* TODO. Mostrar foto de perfil ... */}
                    {/* 
                    {auth.user && (

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="size-10 rounded-full p-1">
                                <Avatar className="size-8 overflow-hidden rounded-full">
                                    <AvatarImage
                                        src={auth.user.avatar}
                                        alt={auth.user.name}
                                    />
                                    <AvatarFallback className="rounded-lg">
                                        {getInitials(auth.user.name)}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>

                        <DropdownMenuContent className="w-56" align="end">
                            <UserMenuContent user={auth.user} />
                        </DropdownMenuContent>
                    </DropdownMenu>
                    ) */}
                        
                </div>

            </div>
        </header>
    );
}
