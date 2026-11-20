import { OptionsMenu } from '@/components/OptionsMenu';

type AccionesContenidoProps = {
    esAutor: boolean;
    onEliminar?: () => void;
    onReportar: () => void;
};

/**
 * Menú de opciones de un contenido (publicación, comentario, perfil):
 * el autor puede eliminar, el resto reportar
 */
export function AccionesContenido({ esAutor, onEliminar, onReportar }: AccionesContenidoProps) {
    const items = esAutor && onEliminar
        ? [{ label: 'Eliminar', onSelect: onEliminar }]
        : [{ label: 'Reportar', onSelect: onReportar }];

    return <OptionsMenu items={items} />;
}
