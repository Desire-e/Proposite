import AuthLayout from "./AuthLayout";

export default function LoginLayout({children}: {children: React.ReactNode}) {
    return (
        <main 
        className='flex flex-col justify-center items-center 
        w-full min-h-screen 
        bg-muted-background'>
            <div className="flex lg:w-1/2 md:w-xl w-full">
                <AuthLayout>
                    {children}
                </AuthLayout>
            </div>
        </main>
    );
}