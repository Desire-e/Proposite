import { cn } from '@/lib/utils';

/**
 * Antetítulo en mayúsculas que acompaña a los títulos de sección
 */
export function Eyebrow({ children, className = '' }: 
    { children: React.ReactNode, className?: string }) {
    return (
        <p className={cn('text-xs font-bold uppercase tracking-[0.16em] text-secondary', className)}>
            {children}
        </p>
    );
}


type SectionHeadingProps = {
    eyebrow: string;
    eyebrowIcon?: React.ReactNode;
    title: string;
    as?: 'h1' | 'h2';
    size?: 'md' | 'lg' | 'xl';
    titleClassName?: string;
    eyebrowClassName?: string;
    className?: string;
};

// Tamaño del título y separación con el antetítulo
const sizes = {
    md: 'mt-1 text-2xl',
    lg: 'mt-2 text-3xl',
    xl: 'mt-3 text-4xl lg:text-5xl',
};

/**
 * Antetítulo y título de una sección.
 *
 * El título hereda el color: para cambiarlo,
 * pasa el color en className (p. ej. 'text-primary').
 */
export function SectionHeading({
    eyebrow,
    eyebrowIcon = null,
    title,
    as: Tag = 'h2',
    size = 'md',
    eyebrowClassName = '',
    titleClassName = '',
    className = '',
}: SectionHeadingProps) {
    return (
        <div className={className}>
            {eyebrowIcon 
            ? (
                <div className={`flex gap-2 items-center ${eyebrowClassName}`}>
                        {eyebrowIcon}
                    
                    <Eyebrow className={eyebrowClassName}>
                        {eyebrow}
                    </Eyebrow>
                </div>
            ) : (
                <Eyebrow className={eyebrowClassName}>
                    {eyebrow}
                </Eyebrow>
            )}

            <Tag className={cn('font-heading font-bold', sizes[size], titleClassName)}>
                {title}
            </Tag>
        </div>
    );
}
