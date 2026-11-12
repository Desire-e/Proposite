import type { ReactNode } from 'react'
import * as DialogPrimitive from '@radix-ui/react-dialog'
import { AlertTriangle, CheckCircle2, Info, X } from 'lucide-react'
import { Dialog, DialogDescription, DialogOverlay, DialogPortal, DialogTitle } from '@/components/ui/dialog'

type FeedbackModalProps = {
    open: boolean
    title: string
    description: string
    tone?: 'danger' | 'success' | 'warning'
    confirmLabel?: string
    cancelLabel?: string
    onConfirm?: () => void
    onClose: () => void
    children?: ReactNode

    // operaciones procesando desencadenadas tras aceptar
    processing?: boolean
}

const tones = {
    danger: {
        icon: AlertTriangle,
        iconClass: 'bg-red-100 text-red-700',
        action: 'bg-red-600 hover:bg-red-700 disabled:opacity-50',
    },
    success: {
        icon: CheckCircle2,
        iconClass: 'bg-tertiary text-primary',
        action: 'bg-primary hover:bg-tertiary hover:text-tertiary-foreground disabled:opacity-50',
    },
    warning: {
        icon: Info,
        iconClass: 'bg-tertiary text-tertiary-foreground',
        action: 'bg-primary hover:bg-tertiary hover:text-tertiary-foreground disabled:opacity-50',
    },
}

/**
 * Modal de confirmación o aviso.
 *
 * Construido sobre el Dialog de Radix: se cierra con Escape o
 * pulsando fuera, y mantiene el foco dentro mientras está abierto.
 * No se puede cerrar mientras @param processing.
 */
export function FeedbackModal({
    open, title, description, tone = 'warning',
    confirmLabel = 'Aceptar', cancelLabel = 'Cancelar',
    processing = false, onConfirm, onClose, children,
}: FeedbackModalProps
) {

    const close = () => { if (!processing) onClose() }

    const styles = tones[tone]
    const Icon = styles.icon

    return (
        // Escape, clic fuera y botones de cerrar pasan por close()
        <Dialog open={open} onOpenChange={(abierto) => { if (!abierto) close() }}>
            <DialogPortal>
                <DialogOverlay className="bg-foreground/35" />

                <DialogPrimitive.Content
                    className="fixed left-1/2 top-1/2 z-50 w-[calc(100%-2.5rem)] max-w-md -translate-x-1/2 -translate-y-1/2
                    rounded-3xl border border-border bg-background p-6 shadow-2xl"
                >
                    <div className="flex items-start justify-between gap-4">
                        <div className={`flex size-11 items-center justify-center rounded-2xl ${styles.iconClass}`}>
                            <Icon className="size-5" />
                        </div>

                        <button
                            type="button"
                            onClick={close}
                            aria-label="Cerrar modal"
                            className="rounded-full p-2 text-neutral-foreground hover:text-foreground"
                        >
                            <X className="size-5" />
                        </button>
                    </div>

                    <DialogTitle className="mt-5 text-2xl font-bold leading-normal text-foreground">
                        {title}
                    </DialogTitle>

                    <DialogDescription className="mt-2 text-sm leading-6 text-neutral-foreground">
                        {description}
                    </DialogDescription>

                    {children}

                    <div className="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            disabled={processing}
                            onClick={close}
                            className="rounded-xl border border-border px-4 py-2.5 text-sm font-semibold
                            text-neutral-foreground hover:bg-neutral"
                        >
                            {cancelLabel}
                        </button>

                        {onConfirm && (
                            <button
                                type="button"
                                disabled={processing}
                                onClick={onConfirm}
                                className={`rounded-xl px-4 py-2.5 text-sm font-semibold text-primary-foreground
                                transition ${styles.action}`}
                            >
                                {confirmLabel}
                            </button>
                        )}
                    </div>
                </DialogPrimitive.Content>
            </DialogPortal>
        </Dialog>
    )
}
