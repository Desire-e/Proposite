/**
 * Barra de progreso horizontal. @param porcentaje de 0 a 100
 */

const colors = {
    primary: {bg: 'bg-white', fg: 'bg-primary'},
    secondary: {bg: 'bg-tertiary', fg: 'bg-secondary'}
}

export function ProgressBar(
    { porcentaje, className='', color='secondary' }: 
    { porcentaje: number, className?: string, color?: 'primary' | 'secondary' }
) {
    return (
        <div
            role="progressbar"
            aria-valuenow={porcentaje}
            aria-valuemin={0}
            aria-valuemax={100}
            className={`h-2 overflow-hidden rounded-full ${className} ${colors[color]['bg']}`}
        >
            <span
                className={`block h-full rounded-full transition-all ${colors[color]['fg']}`}
                style={{ width: `${porcentaje}%` }}
            />
        </div>
    );
}
