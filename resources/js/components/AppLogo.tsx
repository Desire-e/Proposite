import { Link } from "@inertiajs/react";
import { GraduationCap } from "lucide-react"


export default function AppLogo({lightMode}: {lightMode: boolean}) {
    return (
            <Link
                href="/" 
                className="group inline-flex items-center gap-3 text-left" 
                aria-label="Ir al home"
            >
                <span 
                    className={`flex size-10 items-center justify-center rounded-xl 
                    ${lightMode ? 'bg-tertiary text-primary' : 'bg-secondary text-white'}`} 
                >
                    <GraduationCap aria-hidden="true" />
                </span>
                <span 
                    className={`font-heading text-2xl font-bold 
                    ${lightMode ? 'tracking-tight text-slate-900' : ''}`}
                >
                    proposite
                </span>
            </Link>
    );
}