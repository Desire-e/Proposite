export default function AuthLayout({children}: {children: React.ReactNode}) {
    return (
        <div className="flex flex-col w-full items-center py-17 bg-background">
            <div className='relative flex flex-col md:w-[80%] w-[85%] mx-auto md:gap-5 gap-5'>
        
                {children}
            
            </div>
        </div>
    );
}