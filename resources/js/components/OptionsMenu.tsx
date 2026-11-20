import { MoreHorizontal } from "lucide-react";
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, } from '@/components/ui/dropdown-menu';

type Item = { 
    label: string; 
    onSelect: () => void;
};

export function OptionsMenu({ items }: { items: Item[] }) {
    return (
        <DropdownMenu>
            
            <DropdownMenuTrigger asChild>
                <button 
                    type="button" 
                    aria-label="Más opciones" 
                    className="text-neutral-foreground hover:text-primary"
                >
                    <MoreHorizontal className="size-5" />
                </button>
            </DropdownMenuTrigger>

            <DropdownMenuContent className="bg-white border rounded-sm border-muted-foreground/30" align="end">
                
                {items.map((item) => (
                    <DropdownMenuItem 
                        key={item.label} 
                        onSelect={item.onSelect}
                        className="cursor-pointer px-3 py-2 text-neutral-foreground focus:bg-neutral focus:text-primary"
                    >
                        {item.label}
                    </DropdownMenuItem>
                ))}

            </DropdownMenuContent>
        </DropdownMenu>
    );
}