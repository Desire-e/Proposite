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
                
                <div className='flex flex-col md:w-3xs gap-2 items-center'>
                        {card.icon && <Icon iconNode={card.icon} className="h-5 w-5 text-primary" />}                        
                    <p className="text-sm text-primary">{card.title.toUpperCase()}</p>
                    <p className="text-sm ">{card.description}</p>
                </div>
        
            ))}
        </div>
    );
}
