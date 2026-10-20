/**
 * Field Wrapper - کامپوننت پایه برای فیلدهای فرم با لیبل
 */
export function Field({
  label,
  children,
  className = "",
}: {
  label: string;
  children: React.ReactNode;
  className?: string;
}) {
  return (
    <div className={`flex flex-col gap-1.5 ${className}`}>
      <span className="text-xs font-semibold text-text-muted">{label}</span>
      {children}
    </div>
  );
}