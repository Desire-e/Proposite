

import { ArrowUpRight } from "lucide-react"
import { Link } from '@inertiajs/react';
import AppLogo from "./AppLogo";

export function AppFooter() {
  return (
    <footer className="border-t border-tertiary-foreground/40 bg-tertiary-foreground text-white">
        <div className="mx-auto grid max-w-7xl gap-10 px-5 py-12 lg:grid-cols-[1.4fr_1fr_1fr] lg:px-10">
            <div>
                <AppLogo lightMode={false} />
            
                <p className="mt-4 max-w-xs text-sm leading-6 text-tertiary">
                    La comunidad que convierte el esfuerzo de opositar en un camino compartido.
                </p>

                <Link
                    className="mt-5 inline-block text-sm text-secondary transition hover:text-white" 
                    href="mailto:hola@proposite.es"
                >
                    hola@proposite.es
                </Link>
            </div>

            <div>
                <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-secondary">
                    Explora
                </h2>
                <div className="mt-4 flex flex-col items-start gap-3 text-sm text-tertiary">
                    <Link className="inline-flex items-center gap-1 hover:text-white" href="#como-funciona">
                        Cómo funciona <ArrowUpRight size={14} />
                    </Link>
                    <Link className="inline-flex items-center gap-1 hover:text-white" href="#comunidad">
                        La comunidad <ArrowUpRight size={14} />
                    </Link>
                    <Link className="inline-flex items-center gap-1 hover:text-white" href="#beneficios">
                        Beneficios <ArrowUpRight size={14} />
                    </Link>
                </div>
            </div>

            <div>
                <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-secondary">
                    Proposite
                </h2>
                
                <div className="mt-4 flex flex-col gap-3 text-sm text-[color:var(--tertiary)]">
                    <Link className="hover:text-white" href="#privacidad">Privacidad</Link>
                    <Link className="hover:text-white" href="#terminos">Términos y condiciones</Link>
                    <span>Hecho para opositores, por opositores.</span>
                </div>
            </div>
        </div>
        
        <div className="mx-auto flex max-w-7xl flex-col gap-2 border-t border-secondary/20 
        px-5 py-5 text-xs text-secondary sm:flex-row sm:items-center sm:justify-between lg:px-10">
            <span>&copy; 2026 Proposite</span>
            <span>Tu plaza empieza aquí.</span>
        </div>
    </footer>
  )
}