import { usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';

type UserAvatarProps = {
    username?: string;
    size?: 'sm' | 'md' | 'lg';
    className?: string;
};

const sizes = {
    sm: 'size-10 text-xs rounded-[11px]',
    md: 'size-12 text-base rounded-xl',
    lg: 'size-20 text-2xl rounded-2xl',
};

function getInitials(name: string): string {

    const words = name.trim().split(/\s+/).filter(Boolean);

    if (words.length === 0) {
        return '?';
    }

    const firstInitial = words[0][0];

    if (words.length === 1) {
        return firstInitial.toLocaleUpperCase('es');
    }

    const lastInitial = words[words.length - 1][0];

    return `${firstInitial}${lastInitial}`.toLocaleUpperCase('es');
}

export default function UserAvatar({
    username = '',
    size = 'md',
    className = '',
}: UserAvatarProps) {

    const { auth } = usePage<SharedData>().props;
    const name = username.trim() !== '' ? 
        username.trim() : 
        auth.user?.name ?? ''; 


    return (
        // <div
        //     role="img"
        //     aria-label={`Avatar de ${name}`}
        //     className={`flex shrink-0 items-center justify-center
        //         bg-primary/10 text-primary font-heading font-semibold
        //         select-none ${sizes[size]} ${className}`}
        // >
        <div 
            role="img"
            aria-label={`Avatar de ${name}`}
            className={`grid  shrink-0 place-items-center 
                font-heading font-bold text-primary bg-primary/10 
                border-1 border-primary! 
            ${sizes[size]} ${className}`}
        >
            {getInitials(name)}
        </div>
    );
}