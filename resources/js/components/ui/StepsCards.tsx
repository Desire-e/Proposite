export interface StepCardData {
    number: number;
    title: string;
    description: string;
}

export function StepsCards({ steps }: {steps: StepCardData[]} ) {
    return (
        <div className='flex flex-wrap justify-center gap-10'>
            {steps.map((step) => (

                <div 
                    key={`${step.number}-${step.title}`} 
                    className='flex h-auto w-2xs p-6 gap-5
                    border-2 border-black/20 border-[1px] rounded-lg'
                >
                    <div className='flex flex-col shrink-0 
                    w-10 h-10 text-center bg-soft-accent rounded-full 
                    text-accent text-xl/10 font-semibold font-heading'>
                        {step.number}
                    </div>
                    <div>
                        <p className='font-semibold '>{step.title}</p>
                        <p className='text-sm text-soft-foreground'>{step.description}</p>
                    </div>
                </div>
        
            ))}
        </div>
    );
}
