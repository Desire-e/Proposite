import { Head } from '@inertiajs/react';

import { AppHeader } from '@/components/AppHeader';
import { AppFooter } from '@/components/AppFooter';

export default function AppLayout(
    { children, headTitle, className = '', }: 
    { children: React.ReactNode; headTitle: string; className?: string;}) 
{
    return (
        <div className={`min-h-screen bg-background text-slate-900 ${className}`}>
            <Head title={headTitle} />

            <AppHeader />

            <main>
                {children}
            </main>

            <AppFooter />
        </div>
    );
}