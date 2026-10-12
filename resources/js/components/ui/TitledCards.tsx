import { LucideIcon } from "lucide-react";
import { Icon } from '@/components/icon';

export interface TitledCardData {
    icon?: LucideIcon | null;
    title: string;
    description: string;
}

export function TitledCards({ cards }: {cards: TitledCardData[]} ) {
    return (
        <div className='flex flex-wrap justify-center text-center gap-10'>
            {cards.map((card) => (
                
                <div className='flex flex-col md:w-3xs gap-2 items-center text-accent'>
                        {card.icon && <Icon iconNode={card.icon} className="h-5 w-5" />}                        
                    <p className="text-sm">{card.title.toUpperCase()}</p>
                    <p className="text-sm text-foreground">{card.description}</p>
                </div>
        
            ))}
        </div>
    );
}
