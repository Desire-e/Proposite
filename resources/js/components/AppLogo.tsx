export default function AppLogo({lightMode}: {lightMode: boolean}) {
    return (
        <div className="flex h-full items-center justify-center">
            <img 
            className="h-[80%] w-auto object-contain" 
            src={lightMode ? "/images/proposite-logo.svg" : "/images/dark-proposite-logo.svg"}            
            alt="Logo de Proposite"
            />
        </div>
    );
}
