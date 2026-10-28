import { Link } from '@inertiajs/react';
import { ArrowRight,  } from "lucide-react";


export default function ProfileCard(
    { brow, title, link = null, className = '', children } : 
    {
        brow: string, 
        title: string, 
        link?: {label: string, route: string} | null,
        className?: string, 
        children: React.ReactElement
    } 
){
    return (
        <section className={`rounded-2xl border border-border bg-white p-5 md:p-6 ${className}`}>
            <div className="flex items-start justify-between gap-3">
                <div>
                    <span className="text-xs font-extrabold uppercase tracking-[0.13em] text-secondary">
                        {brow}
                    </span>
                    <h2 className="mb-0 mt-2 font-heading text-2xl font-bold tracking-[-0.04em]">
                        {title}
                    </h2>
                </div>
                
                {link && 
                    <Link
                        href={route(link.route)}
                        className="inline-flex shrink-0 items-center gap-1 text-xs font-bold text-primary"
                    >
                        {link.label}
                        <ArrowRight size={14} />
                    </Link>
                }
            </div>

            {children}
            
        </section>
    );
}