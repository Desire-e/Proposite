export default function AccentText(
{children, underline=false}: 
{children: React.ReactNode, underline?: boolean}) {
    return (
        <span 
            className={`text-accent 
            ${underline ? 'underline decoration-accent underline-offset-4' : ''}`}
        >
            {children}
        </span>
    );
}