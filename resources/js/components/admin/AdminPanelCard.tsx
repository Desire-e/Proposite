
import { Link } from "@inertiajs/react";
import { ArrowRight, LucideIcon } from "lucide-react";
// import { Icon } from '@/components/icon';

type AdminPanelCardProps = {
    Icon: LucideIcon;
    title: string;
    count?: number;
    link?: {
        label: string;
        href: string;
    } | null;
}

export default function AdminPanelCard({Icon, title, count=0, link = null}: AdminPanelCardProps){
    return(
        <article className="flex flex-col flex-wrap w-full md:w-[48%] lg:w-[23%] p-5 
        rounded-xl bg-white border border-border gap-2">
            
            <div className="flex lg:flex-col gap-2 justify-between">
                <div className='flex items-center gap-2'>
                    <div className="flex items-center justify-center">
                        <Icon className="size-5" strokeWidth={2.5} />
                    </div>

                    <p className="text-sm font-semibold">
                        {title}
                    </p>
                </div>

                <strong className="flex block text-2xl font-bold">
                    {count}
                </strong>
            </div>

            {link && (
                <Link 
                    href={link.href}
                    className="inline-flex items-center gap-2 mt-1 text-xs font-medium 
                    text-tertiary-foreground"
                >
                    {link.label} <ArrowRight className="size-3"/>
                </Link>
            )}
        </article>
    );
}