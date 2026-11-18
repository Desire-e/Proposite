import { Button } from "@/components/ui/button";

export function EliminarButton({onEliminar} :{onEliminar: () => void}) {

    return (
        <Button
            className='self-end font-semibold text-xs px-2 py-0! bg-error z-10 
            text-error-foreground border border-error! 
            cursor-pointer h-8 m-0'
            onClick={onEliminar}
        >
            Eliminar
        </Button>

    );
}
