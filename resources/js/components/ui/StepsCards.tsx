export type StepCardData = {
    number: string;
    title: string;
    description: string;
}

export function StepsCards({ steps }: {steps: StepCardData[]} ) {
    return (
        <div className="mt-10 grid gap-4 md:grid-cols-4">
            {steps.map((step) => (
                <article
                    key={step.number}
                    className="rounded-2xl border border-border bg-white p-6"
                >
                    <span className="text-3xl font-bold text-secondary">
                        {step.number}
                    </span>

                    <h3 className="mt-5 font-bold text-foreground">
                        {step.title}
                    </h3>

                    <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                        {step.description}
                    </p>
                </article>
            ))}
        </div>
    );
}