/**
 * DateRangePicker - انتخاب بازه تاریخی (رفت و برگشت)
 */
"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { Calendar as CalendarIcon } from "lucide-react";
import CalendarPanel, { CalRange } from "@/components/search/CalendarPanel";
import { parseJalaliDate, formatJalaliDate } from "@/lib/utils/jalali";

export interface DateRangeValue {
  from?: string; // YYYY/MM/DD شمسی
  to?: string;   // YYYY/MM/DD شمسی
}

export function DateRangePicker({
  value,
  onChange,
  allowRange = true,
  disabled = false,
  fromName = "ورود",
  toName = "خروج",
  hideReturnField = false,
}: {
  value: DateRangeValue;
  onChange: (v: DateRangeValue) => void;
  allowRange?: boolean;
  disabled?: boolean;
  fromName?: string;
  toName?: string;
  hideReturnField?: boolean;
}) {
  const [open, setOpen] = useState(false);
  const [session, setSession] = useState(0);
  const [shift, setShift] = useState(0);
  const ref = useRef<HTMLDivElement>(null);
  const panelRef = useRef<HTMLDivElement>(null);

  /* بستن با کلیک بیرون */
  useEffect(() => {
    if (!open) return;
    const close = (e: MouseEvent) => {
      if (!ref.current?.contains(e.target as Node)) setOpen(false);
    };
    document.addEventListener("mousedown", close);
    return () => document.removeEventListener("mousedown", close);
  }, [open]);

  /* وسط‌چین + نگه‌داشتن داخل viewport */
  useEffect(() => {
    if (!open) {
      setShift(0);
      return;
    }
    const measure = () => {
      const container = ref.current;
      const panel = panelRef.current;
      if (!container || !panel) return;

      const cRect = container.getBoundingClientRect();
      const width = panel.offsetWidth;
      const margin = 8;

      const idealLeft = cRect.left + cRect.width / 2 - width / 2;
      const clampedLeft = Math.max(
        margin,
        Math.min(idealLeft, window.innerWidth - margin - width),
      );
      setShift(clampedLeft - idealLeft);
    };

    const raf = requestAnimationFrame(measure);
    window.addEventListener("resize", measure);
    return () => {
      cancelAnimationFrame(raf);
      window.removeEventListener("resize", measure);
    };
  }, [open, session]);

  const openPopup = useCallback(() => {
    setSession((s) => s + 1);
    setOpen(true);
  }, []);

  const liveUpdate = useCallback(
    (r: CalRange) => {
      onChange({
        from: r.from ? formatJalaliDate(r.from) : undefined,
        to: r.to ? formatJalaliDate(r.to) : undefined,
      });
    },
    [onChange],
  );

  const confirm = useCallback(() => setOpen(false), []);

  // ✅ ساده‌سازی disabled
  const isDisabledFrom = disabled;
  const isDisabledTo = disabled || !allowRange;

  return (
    <div ref={ref} className="relative w-full">
      <div className="flex gap-2">
        {/* اینپوت تاریخ رفت */}
        <div className="flex-1 min-w-0">
          <span className="text-xs font-semibold text-text-muted block mb-1.5">
            {fromName}
          </span>
          <button
            type="button"
            disabled={isDisabledFrom}
            onClick={openPopup}
            className={`ns-input w-full flex items-center justify-between gap-2 cursor-pointer text-start ${
              isDisabledFrom ? "bg-bg-sec cursor-not-allowed" : ""
            }`}
          >
            <span
              className={
                value.from ? "text-text truncate" : "text-text-subtle truncate"
              }
            >
              {value.from || "انتخاب کنید"}
            </span>
            <CalendarIcon className="w-4 h-4 text-primary-500 shrink-0" />
          </button>
        </div>

        {/* اینپوت تاریخ برگشت */}
        {!hideReturnField && (
          <div className="flex-1 min-w-0">
            <span className="text-xs font-semibold text-text-muted block mb-1.5">
              {toName}
            </span>
            <button
              type="button"
              disabled={isDisabledTo}
              onClick={openPopup}
              className={`ns-input w-full flex items-center justify-between gap-2 cursor-pointer text-start ${
                isDisabledTo ? "bg-bg-sec cursor-not-allowed" : ""
              }`}
            >
              <span
                className={
                  value.to ? "text-text truncate" : "text-text-subtle truncate"
                }
              >
                {value.to || (allowRange ? "انتخاب کنید" : "—")}
              </span>
              <CalendarIcon className="w-4 h-4 text-primary-500 shrink-0" />
            </button>
          </div>
        )}
      </div>

      {/* پاپ‌آپ تقویم */}
      {open && (
        <div
          ref={panelRef}
          style={{
            left: "50%",
            transform: `translateX(calc(-50% + ${shift}px))`,
          }}
          className="absolute top-full mt-2 z-50 bg-white rounded-lg border border-border shadow-card-hover p-4 w-[320px] md:w-[620px]"
        >
          <CalendarPanel
            key={session}
            mode={allowRange ? "range" : "single"}
            initial={{
              from: parseJalaliDate(value.from) ?? null,
              to: parseJalaliDate(value.to) ?? null,
            }}
            fromName={fromName}
            toName={toName}
            onLiveChange={liveUpdate}
            onConfirm={confirm}
          />
        </div>
      )}
    </div>
  );
}