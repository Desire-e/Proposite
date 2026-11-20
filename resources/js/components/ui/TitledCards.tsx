import { LucideIcon } from "lucide-react";
import { Icon } from '@/components/icon';
import { SectionCard } from '@/components/ui/SectionCard';

export type TitledCardData = {
    icon?: LucideIcon | null;
    title: string;
    description: string;
}

export function TitledCards({ cards }: { cards: TitledCardData[]} ) {
    return (
        <div className="mt-12 grid gap-5 md:grid-cols-3">

            {cards.map((card) => (
            <SectionCard
                as="article"
                key={card.title}
                className='p-7 hover:bg-tertiary transition duration-250'
            >
                {card.icon && <Icon iconNode={card.icon} className="text-primary" />}

                <h3 className="mt-8 text-xl font-bold text-foreground">
                    {card.title}
                </h3>

                <p className="mt-3 leading-7 text-neutral-foreground">
                    {card.description}
                </p>
            </SectionCard>
            ),)}
        </div>
    );
}
