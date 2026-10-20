/**
 * TripTypeToggle - انتخاب نوع سفر: یک‌طرفه / رفت و برگشت
 */
export function TripTypeToggle({
  value,
  onChange,
}: {
  value: "one" | "round";
  onChange: (v: "one" | "round") => void;
}) {
  return (
    <div className="inline-flex rounded-lg border border-border bg-bg-sec p-2 gap-1">
      {(
        [
          ["one", "یک‌طرفه"],
          ["round", "رفت و برگشت"],
        ] as const
      ).map(([v, l]) => (
        <button
          key={v}
          type="button"
          onClick={() => onChange(v)}
          className={`px-4 py-1.5 rounded-md text-sm font-semibold transition-all cursor-pointer ${
            value === v
              ? "bg-white text-primary-600 shadow-sm"
              : "text-text-muted hover:text-text"
          }`}
        >
          {l}
        </button>
      ))}
    </div>
  );
}