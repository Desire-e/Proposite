import * as React from 'react';

import { cn } from '@/lib/utils';

type InputProps = React.ComponentProps<'input'> & {
    errors?: string | boolean
}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
    ({ className, type, errors, ...props }, ref) => {
    return (
        <input
            type={type}
            className={cn(

                'flex w-full rounded-sm border-1 border-border bg-white px-3 py-2',
                'file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground',
                'placeholder:text-muted-foreground/80',
                'outline-none transition focus:border-primary focus:ring-4 focus:ring-secondary/20',
                // 'focus:outline-hidden focus:border-muted-foreground',
                'disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                className,
                errors ? 'border-error-foreground! placeholder:text-error-foreground! focus:ring-4 focus:ring-error-foreground/20' : '',
            )}
            ref={ref}
            {...props}
        />
    );
});

Input.displayName = 'Input';

export { Input };
