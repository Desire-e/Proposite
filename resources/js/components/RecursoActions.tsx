import { Button } from "@/components/ui/button";
import { Trash2, Pencil } from "lucide-react";


export function RecursoActions(
    { onDelete, onEdit }: { 
    onDelete: () => void, onEdit: () => void
}) {
    return (
        <div className="flex flex-wrap gap-1">
            <div className="w-fit">
                {/* Eliminar */}
                <Button
                    type="button" 
                    aria-label="Eliminar recurso"
                    className='font-semibold text-xs px-1 py-0! bg-transparent 
                    z-10 text-error-foreground cursor-pointer h-5 m-0'
                    onClick={onDelete}
                >
                    <Trash2 className="size-3.5" />
                </Button>                
            </div>
            <div className="w-fit">
                {/* Editar */}
                <Button
                    type="button" 
                    aria-label="Editar recurso"
                    className='font-semibold text-xs px-1 py-0! bg-transparent z-10 
                    text-secondary cursor-pointer h-5 m-0'
                    onClick={onEdit}
                >
                    <Pencil className="size-3.5" />
                </Button>
            </div>
        </div>
    );
}