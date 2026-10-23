import type { ReactNode } from 'react'
import { AlertTriangle, CheckCircle2, Info, X } from 'lucide-react'

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

export function FeedbackModal({
    open, title, description, tone = 'warning', 
    confirmLabel = 'Aceptar', cancelLabel = 'Cancelar', 
    processing = false, onConfirm, onClose, children,
}: FeedbackModalProps
) {

    if (!open) return null

    const close = () => { if (!processing) onClose() }

    const styles =
        tone === 'danger' ? 
            {
                icon: AlertTriangle,
                iconClass: 'bg-red-100 text-red-700',
                action: 'bg-red-600 hover:bg-red-700 disabled:opacity-50',
            }
            : tone === 'success' ? 
            {
                icon: CheckCircle2,
                iconClass: 'bg-tertiary text-primary',
                action: 'bg-primary hover:bg-tertiary hover:text-tertiary-foreground disabled:opacity-50',
            }
            : {
                icon: Info,
                iconClass: 'bg-tertiary text-tertiary-foreground',
                action: 'bg-primary hover:bg-tertiary hover:text-tertiary-foreground disabled:opacity-50',
            }

    const Icon = styles.icon

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-foreground/35 p-5"
            role="presentation"
            onMouseDown={(event) =>
                event.target === event.currentTarget && close()
            }
        >
            <section
                role="dialog"
                aria-modal="true"
                aria-labelledby="feedback-modal-title"
                className="w-full max-w-md rounded-3xl border border-border bg-background p-6 shadow-2xl"
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

                <h2 id="feedback-modal-title" className="mt-5 text-2xl font-bold text-foreground">
                    {title}
                </h2>

                <p className="mt-2 text-sm leading-6 text-neutral-foreground">
                    {description}
                </p>

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
            </section>
        </div>
    )
}
