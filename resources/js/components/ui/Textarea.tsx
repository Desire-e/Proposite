import { cn } from '@/lib/utils';
import type { ChangeEventHandler } from 'react';

interface TextareaProps {
    id?: string;
    value?: string; 
    onChange?: ChangeEventHandler<HTMLTextAreaElement>;
    className?: string; 
    rows?: number;
    cols?: number; 
    placeholder?: string; 
    content?: string; 
    disabled?: boolean,
    errors?: boolean;
}

export default function Textarea(
    { id, value, onChange, className, rows = 4, cols, placeholder, disabled = false, errors = false,}: TextareaProps 
) {
    return (
        <textarea
            id={id}
            value={value ?? ''}
            onChange={onChange}
            className={cn(
                'flex w-full rounded-sm border-1 border-border bg-white px-3 py-2',
                'file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground',
                'placeholder:text-muted-foreground/80',
                'outline-none transition focus:border-primary focus:ring-4 focus:ring-secondary/20',
                'disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                className,
                errors ? 'border-error! placeholder:text-error! focus:ring-4 focus:ring-error/20' : '',
            )}
            rows={rows} 
            cols={cols}
            placeholder={placeholder}
            disabled={disabled}
        >
            {/* {content} */}
        </textarea>
    );
}