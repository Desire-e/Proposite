import { Head } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { Link } from '@inertiajs/react';

export type asideContentData = {
    brow: string;
    title: string;
    description: string;
    phrase: string;
}

export function AuthLayout(
    { children, headTitle, className = '', asideContent}: 
    {children: React.ReactNode; headTitle: string; className?: string; asideContent: asideContentData }
) {
    return (
        <main className={`grid min-h-screen lg:grid-cols-[0.85fr_1.15fr] ${className}`}>

            <Head title={headTitle} />

            <aside className="hidden bg-primary p-10 text-primary-foreground lg:flex lg:flex-col lg:justify-between">
                <Link
                    href={route('welcome')}
                    className="inline-flex w-fit items-center gap-2 
                    font-semibold text-primary-foreground/80 transition hover:text-primary-foreground"
                >
                    <ArrowLeft className="size-5" />
                    Volver al inicio
                </Link>

                <div>
                    <p className="text-xs font-bold uppercase tracking-[0.16em] text-secondary">
                        {asideContent.brow}
                    </p>

                    <h1 className="mt-5 max-w-md font-heading text-6xl font-bold leading-none">
                        {asideContent.title}
                    </h1>

                    <p className="mt-6 max-w-sm leading-7 text-indigo-100">
                        {asideContent.description}
                    </p>
                </div>

                <p className="text-sm italic text-indigo-200">
                    {asideContent.phrase}
                </p>
            </aside>

            {children}

        </main>
    );
}

