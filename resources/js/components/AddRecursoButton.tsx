import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";


export function AddRecursoButton({ onClick }: { onClick: () => void }) {
    return (
        <div className="w-fit">
            <Button
                type='button'
                aria-label="Añadir recurso"
                className='font-semibold text-xs px-2 py-0! bg-primary z-10 
                text-primary-foreground border border-primary!
                cursor-pointer h-8 m-0 hover:bg-secondary hover:border-secondary!'
                onClick={onClick}
            >
                <Plus className="size-3.5" />
                Añadir recurso
            </Button>
        </div>
    );
}