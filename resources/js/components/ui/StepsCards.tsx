import { SectionCard } from '@/components/ui/SectionCard';

export type StepCardData = {
    number: string;
    title: string;
    description: string;
}

export function StepsCards({ steps }: {steps: StepCardData[]} ) {
    return (
        <div className="mt-10 grid gap-4 md:grid-cols-4">
            {steps.map((step) => (
                <SectionCard
                    as="article"
                    key={step.number}
                    className="p-6"
                >
                    <span className="text-3xl font-bold text-secondary">
                        {step.number}
                    </span>

                    <h3 className="mt-5 text-lg font-bold text-foreground">
                        {step.title}
                    </h3>

                    <p className="mt-2 leading-6 text-neutral-foreground">
                        {step.description}
                    </p>
                </SectionCard>
            ))}
        </div>
    );
}