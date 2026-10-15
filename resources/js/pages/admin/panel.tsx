import AppLayout from '@/layouts/AppLayout';
import PageLayout from '@/layouts/PageLayout';

export default function Panel() {
    return (
        <AppLayout headTitle="Panel de administración | Proposite">
            <PageLayout className="lg:w-[70%]">
                <h1 className="text-3xl font-heading font-medium">Panel de administración</h1>
                {/* TODO: reportes pendientes, usuarios, tests */}
            </PageLayout>
        </AppLayout>
    );
}