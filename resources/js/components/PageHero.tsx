import { Eyebrow } from '@/components/ui/SectionHeading';

type PageHeroProps = {
    eyebrow: string;
    title: string;
    description: string;
    // contenido bajo la descripción
    children?: React.ReactNode;
    // contenido a la derecha (debajo en móvil)
    aside?: React.ReactNode;
};

/**
 * Cabecera de las páginas principales (foro, temario, tests)
 */
export function PageHero({ eyebrow, title, description, children, aside }: PageHeroProps) {
    return (
        <section className="overflow-hidden rounded-3xl border border-border bg-tertiary px-6 py-7 lg:px-10 lg:py-9">
            <div className="flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                <div className="max-w-2xl">
                    <Eyebrow className="mb-3 text-base">{eyebrow}</Eyebrow>

                    <h1 className="font-heading text-3xl font-bold text-primary lg:text-5xl">
                        {title}
                    </h1>

                    <p className="mt-3 leading-6 text-neutral-foreground">
                        {description}
                    </p>

                    {children}
                </div>

                {aside}
            </div>
        </section>
    );
}
