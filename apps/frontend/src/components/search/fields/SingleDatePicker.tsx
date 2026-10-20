/**
 * SingleDatePicker - انتخاب یک تاریخ (مخصوص برنامه سفر AI)
 * استفاده از React Portal برای جلوگیری از کلیپ شدن در مودال
 */
"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { Calendar as CalendarIcon } from "lucide-react";
import CalendarPanel, { CalRange } from "@/components/search/CalendarPanel";
import { parseJalaliDate, formatJalaliDate } from "@/lib/utils/jalali";

export function SingleDatePicker({
  value,
  onChange,
  label = "تاریخ",
  placeholder = "انتخاب کنید",
  disabled = false,
}: {
  value?: string;
  onChange: (v: string | undefined) => void;
  label?: string;
  placeholder?: string;
  disabled?: boolean;
}) {
  const [open, setOpen] = useState(false);
  const [session, setSession] = useState(0);
  const [mounted, setMounted] = useState(false);
  const [pos, setPos] = useState<{ top: number; left: number; width: number } | null>(null);

  const ref = useRef<HTMLDivElement>(null);
  const panelRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    if (!open) {
      setPos(null);
      return;
    }

    const update = () => {
      const container = ref.current;
      if (!container) return;
      const cRect = container.getBoundingClientRect();

      const isDesktop = window.innerWidth >= 768;
      const panelWidth = isDesktop ? 620 : 320;
      const panelHeight = isDesktop ? 460 : 440;
      const margin = 8;
      const vh = window.innerHeight;
      const vw = window.innerWidth;

      const openBelow = cRect.bottom + panelHeight + margin < vh;
      const top = openBelow
        ? cRect.bottom + margin
        : Math.max(margin, cRect.top - panelHeight - margin);

      const idealLeft = cRect.left + cRect.width / 2 - panelWidth / 2;
      const left = Math.max(margin, Math.min(idealLeft, vw - margin - panelWidth));

      setPos({ top, left, width: panelWidth });
    };

    const raf = requestAnimationFrame(update);
    window.addEventListener("resize", update);
    window.addEventListener("scroll", update, true);
    return () => {
      cancelAnimationFrame(raf);
      window.removeEventListener("resize", update);
      window.removeEventListener("scroll", update, true);
    };
  }, [open, session]);

  useEffect(() => {
    if (!open) return;
    const close = (e: MouseEvent) => {
      const target = e.target as Node;
      if (ref.current?.contains(target)) return;
      if (panelRef.current?.contains(target)) return;
      setOpen(false);
    };
    const esc = (e: KeyboardEvent) => {
      if (e.key === "Escape") setOpen(false);
    };
    document.addEventListener("mousedown", close);
    document.addEventListener("keydown", esc);
    return () => {
      document.removeEventListener("mousedown", close);
      document.removeEventListener("keydown", esc);
    };
  }, [open]);

  const openPopup = useCallback(() => {
    setSession((s) => s + 1);
    setOpen(true);
  }, []);

  const liveUpdate = useCallback(
    (r: CalRange) => {
      onChange(r.from ? formatJalaliDate(r.from) : undefined);
    },
    [onChange],
  );

  const confirm = useCallback(() => setOpen(false), []);

  return (
    <div ref={ref} className="relative w-full">
      <span className="text-xs font-semibold text-text-muted block mb-1.5">
        {label}
      </span>
      <button
        type="button"
        disabled={mounted ? disabled || undefined : undefined}
        onClick={openPopup}
        className={`ns-input w-full flex items-center justify-between gap-2 cursor-pointer text-start ${
          disabled ? "bg-bg-sec cursor-not-allowed" : ""
        }`}
      >
        <span
          className={value ? "text-text truncate" : "text-text-subtle truncate"}
        >
          {value || placeholder}
        </span>
        <CalendarIcon className="w-4 h-4 text-primary-500 shrink-0" />
      </button>

      {open &&
        pos &&
        mounted &&
        createPortal(
          <div
            ref={panelRef}
            style={{
              position: "fixed",
              top: pos.top,
              left: pos.left,
              width: pos.width,
              maxWidth: "calc(100vw - 16px)",
              zIndex: 9999,
            }}
            className="bg-white rounded-lg border border-border shadow-card-hover p-4"
            onMouseDown={(e) => e.stopPropagation()}
          >
            <CalendarPanel
              key={session}
              mode="single"
              initial={{
                from: parseJalaliDate(value) ?? null,
                to: null,
              }}
              fromName={label}
              toName=""
              onLiveChange={liveUpdate}
              onConfirm={confirm}
            />
          </div>,
          document.body,
        )}
    </div>
  );
}