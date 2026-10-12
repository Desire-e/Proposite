export default function PageLayout({children}: {children: React.ReactNode}) {
    return (
        <main className='flex flex-col gap-50 w-[90%] mx-auto my-35'>
            {children}
        </main>
    );
}
