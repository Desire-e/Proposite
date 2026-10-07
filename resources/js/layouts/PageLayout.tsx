export default function PageLayout({children}: {children: React.ReactNode}) {
    return (
        <main className='flex flex-col gap-30 w-[90%] mx-auto my-35'>
            {children}
        </main>
    );
}
