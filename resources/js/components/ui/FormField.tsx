import { type ReactNode } from 'react';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

type FormFieldProps = { 
    id: string; 
    label?: string; 
    error?: string; 
    className?: string; 
    children: ReactNode 
};

export default function FormField({ id, label, error, className, children }: FormFieldProps) {
    return (
        <div className={cn('flex flex-col gap-2', className)}>

            {label && (
                <Label htmlFor={id} className="text-sm font-semibold">{label}</Label>
            )}
            {children}
            <InputError message={error} />
        </div>
    );
}