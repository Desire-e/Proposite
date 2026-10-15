import { Head } from '@inertiajs/react';

import { AppHeader } from '@/components/AppHeader';
import { AppFooter } from '@/components/AppFooter';


export default function AppLayout(
    {children, headTitle, className=''}: 
    {children: React.ReactNode, headTitle: string, className?: string}
) {
    return (
        <div className='flex flex-col min-w-full min-h-screen'>
            
            {/* Gestionar las etiquetas <title> y <meta> del documento HTML */}
            <Head title={headTitle} />
            
            <AppHeader />
            
            <div className={`flex flex-col ${className}`}>
                {children}
            </div>

            <AppFooter />
        
        </div>
    );
}
