export function IconButton({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <button 
      type="button" 
      aria-label={label} 
      className="inline-flex size-10 items-center justify-center rounded-full 
      border border-slate-200 bg-white text-slate-600 transition 
      hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
    >
      {children}
    </button>

  ); 
}
