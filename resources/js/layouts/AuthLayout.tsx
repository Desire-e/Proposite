export default function AuthLayout({children, className=''}: {children: React.ReactNode, className?: string}) {
    return (
        <div className={`relative flex flex-col lg:w-[50%] w-[100%] 
        mx-auto lg:p-15 py-15 px-5 gap-5 bg-background ${className}`}>
            {children}
        </div>
    );
}