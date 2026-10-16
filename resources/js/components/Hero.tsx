import { Link } from '@inertiajs/react';
import { ArrowRight, Sparkles, } from 'lucide-react';


export default function Hero({ usersNumber=0 }: { usersNumber?: number}) {

    const defaultAmount = 2400;
    const usersAmount = (usersNumber: number): string => {
        if(usersNumber < 2) 
            return defaultAmount.toLocaleString();
        else 
            return usersNumber.toLocaleString();
    }
    
    return (
        <section className="mx-auto grid w-full max-w-7xl items-center gap-14 
        px-5 py-20 lg:grid-cols-2 lg:px-10 lg:py-28">
            <div>
                <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-tertiary 
                px-3 py-2 text-xs font-bold uppercase tracking-[0.16em] text-primary">
                    <Sparkles size={14} /> La comunidad de opositores</div>
                    <h1 className="max-w-xl font-heading text-5xl font-bold leading-[0.98] tracking-tight text-foreground sm:text-7xl">
                        Opositar es difícil.
                        <br />
                        <em className="italic font-semibold text-secondary">Hacerlo solo, no.</em>
                    </h1>

                    <p className="mt-7 max-w-lg text-lg leading-8 text-neutral-foreground">
                        Un espacio académico y cercano para resolver dudas, compartir avances y avanzar acompañado hacia tu plaza.
                    </p>
                    
                    <div className="mt-9 flex flex-wrap items-center gap-4">
                        <Link
                            href={route('register')} 
                            className="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3.5 font-semibold 
                            text-primary-foreground shadow-lg shadow-primary/20 transition hover:-translate-y-0.5">
                            Únete gratis <ArrowRight size={17} />
                        </Link>
                        <Link
                            href={route('login')} 
                            className="inline-flex items-center gap-2 rounded-xl px-4 py-3.5 
                            font-semibold text-primary transition hover:bg-tertiary"
                        >
                            Ya tengo una cuenta <ArrowRight size={16} />
                        </Link>
                    </div>
                    <p className="mt-8 text-sm text-neutral-foreground">
                        <strong className="text-primary">
                            {usersAmount(usersNumber)}+
                        </strong> {' '} opositores ya forman parte de la comunidad
                    </p>
            </div>
                <div className="relative rounded-[2rem] bg-tertiary p-6 sm:p-10">
                <div className="absolute -right-4 -top-4 rounded-2xl bg-secondary px-4 py-3 text-sm font-bold text-secondary-foreground shadow-lg">
                    7 días de constancia
                </div>

                <div className="rounded-2xl border border-border bg-white p-6 shadow-xl shadow-primary/10">
                    <div className="mb-6 flex items-center gap-3 text-xs font-semibold text-neutral-foreground">
                        <span className="size-2 rounded-full bg-secondary" />
                        Comunidad · Ahora
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="grid size-11 place-items-center rounded-xl bg-primary font-bold text-primary-foreground">
                            LM
                        </div>

                        <div>
                            <strong className="block text-foreground">
                                Laura M.
                            </strong>

                            <span className="text-xs text-neutral-foreground">
                                Administrativo del Estado
                            </span>
                        </div>
                    </div>

                    <p className="mt-5 text-sm leading-6 text-neutral-foreground">
                        “Hoy he terminado el tema 21. Un paso más cerca.”
                    </p>

                    <div className="mt-5 flex items-center justify-between border-t border-border pt-4 text-xs text-neutral-foreground">
                        <span>12 personas dijeron “Me sirvió”</span>

                        <span className="font-bold text-primary">
                            Hace 2 h
                        </span>
                    </div>
                </div>
            </div>
        </section>
    );
}
