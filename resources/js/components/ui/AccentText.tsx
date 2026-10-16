export default function AccentText(
{children, underline=false}: 
{children: React.ReactNode, underline?: boolean}) {
    return (
        <span 
            className={`text-tertiary-foreground font-medium
            ${underline ? 'underline decoration-accent underline-offset-4' : ''}`}
        >
            {children}
        </span>
    );
}