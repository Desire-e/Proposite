/**
 * Layout del contenido de la página
 */

export default function PageLayout({children, className='' }: {children: React.ReactNode, className?: string}) {
    return (
        <main className={`flex flex-col w-[90%] mx-auto my-10 gap-8 
        ${className}`}>
        {/* border-3 border-green-600 ${className}`}> */}
            {children}
        </main>
    );
}
