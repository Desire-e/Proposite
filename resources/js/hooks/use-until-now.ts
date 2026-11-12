import { useEffect, useState } from 'react';


/**
 * Para marcar desde cuando una publicación/comentario se posteó
 *   
 * @param intervalMs recibe un intervalo en milisegundos (por defecto 30 segundos) 
 * @returns un número (el timestamp actual).
 */

export function useUntilNow(intervalMs = 30_000): number {

    // Actualizar tiempo
    // Valor inicial Date.now()
    const [now, setNow] = useState(() => Date.now());

    // Temporizador
    // setInterval(...) programa que cada intervalMs milisegundos 
    // se llame a setNow(Date.now()), actualizando el estado.
    useEffect(() => {
        // el identificador del temporizador.

        const id = setInterval(() => setNow(Date.now()), intervalMs);
        
        // es la función de limpieza: React la ejecuta cuando el 
        // componente se desmonta (o antes de volver a ejecutar el 
        // efecto si cambian las dependencias)
        return () => clearInterval(id);
    
    }, [intervalMs]);

    return now;
}