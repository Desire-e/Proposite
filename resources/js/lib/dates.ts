
/**
 * Saber desde hace cuanto fue publicado una publicacion/comentario
 */
export function publishedSince(dateStr: string): string {
    const mins = Math.floor((Date.now() - new Date(dateStr).getTime()) / 60_000);
    const hours = Math.floor(mins / 60);
    const days = Math.floor(hours / 24);

    if (days >= 1) return `Hace ${days} ${days === 1 ? 'día' : 'días'}`;
    if (hours >= 1) return `Hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
    if (mins >= 1) return `Hace ${mins} ${mins === 1 ? 'minuto' : 'minutos'}`;
    return 'Recién publicado';
}

/**
 * Formatea @dateStr completa, a mes y año en string 
 * 
 * TODO. HACER EN SERVIDOR
 */
export function formatMY(dateStr: string): string {

    const date = new Date(dateStr)
    
    // comprueba si es fecha válida, convirtiendo a ms desde 1970 y comprobando isNaN 
    if (Number.isNaN(date.getTime())) return '' 

    return new Intl.DateTimeFormat('es-ES', {
        month: 'long',
        year: 'numeric',
        timeZone: 'UTC',
    }).format(date) // Aplica las opciones de formato a la fecha y devuelve resultado en texto
}