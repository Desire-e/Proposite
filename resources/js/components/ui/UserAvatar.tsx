type UserAvatarProps = {
    name: string;
    size?: 'sm' | 'md' | 'lg';
    className?: string;
};

const sizes = {
    sm: 'size-8 text-xs rounded-lg',
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
    name,
    size = 'md',
    className = '',
}: UserAvatarProps) {
    return (
        <div
            role="img"
            aria-label={`Avatar de ${name}`}
            className={`flex shrink-0 items-center justify-center
                bg-primary/10 text-primary font-heading font-semibold
                select-none ${sizes[size]} ${className}`}
        >
            {getInitials(name)}
        </div>
    );
}