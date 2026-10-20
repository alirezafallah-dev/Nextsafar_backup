/**
 * PassengerPicker - انتخاب تعداد بزرگسال و کودک
 */
"use client";

import { useEffect, useRef, useState } from "react";
import { ChevronDown, Minus, Plus, Users } from "lucide-react";

export function PassengerPicker({
  adults,
  children: kids,
  onChange,
}: {
  adults: number;
  children: number;
  onChange: (a: number, c: number) => void;
}) {
  const [open, setOpen] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!open) return;
    const close = (e: MouseEvent) => {
      if (!ref.current?.contains(e.target as Node)) setOpen(false);
    };
    document.addEventListener("mousedown", close);
    return () => document.removeEventListener("mousedown", close);
  }, [open]);

  const label =
    kids > 0
      ? `${adults.toLocaleString("fa-IR")} بزرگسال، ${kids.toLocaleString("fa-IR")} کودک`
      : `${adults.toLocaleString("fa-IR")} مسافر`;

  const Counter = ({
    title,
    sub,
    value,
    min,
    max,
    set,
  }: {
    title: string;
    sub: string;
    value: number;
    min: number;
    max: number;
    set: (v: number) => void;
  }) => (
    <div className="flex items-center justify-between py-2">
      <div>
        <div className="text-sm font-semibold">{title}</div>
        <div className="text-xs text-text-muted">{sub}</div>
      </div>
      <div className="flex items-center gap-3">
        <button
          type="button"
          disabled={value <= min}
          onClick={() => set(value - 1)}
          className="w-8 h-8 rounded-md border border-border flex items-center justify-center disabled:opacity-40 hover:border-primary-500 hover:text-primary-600 cursor-pointer"
        >
          <Minus className="w-4 h-4" />
        </button>
        <span className="w-5 text-center font-bold">
          {value.toLocaleString("fa-IR")}
        </span>
        <button
          type="button"
          disabled={value >= max}
          onClick={() => set(value + 1)}
          className="w-8 h-8 rounded-md border border-border flex items-center justify-center disabled:opacity-40 hover:border-primary-500 hover:text-primary-600 cursor-pointer"
        >
          <Plus className="w-4 h-4" />
        </button>
      </div>
    </div>
  );

  return (
    <div ref={ref} className="relative w-full">
      <button
        type="button"
        onClick={() => setOpen((o) => !o)}
        className="ns-input w-full flex items-center justify-between gap-2 cursor-pointer"
        aria-expanded={open}
        aria-haspopup="dialog"
        aria-label={`انتخاب تعداد مسافران: ${label}`}
      >
        <span className="flex items-center gap-2">
          <Users className="w-4 h-4 text-primary-500" />
          {label}
        </span>
        <ChevronDown
          className={`w-4 h-4 text-text-muted transition-transform ${
            open ? "rotate-180" : ""
          }`}
        />
      </button>
      {open && (
        <div 
          className="absolute top-full end-0 mt-2 w-64 bg-white rounded-lg border border-border shadow-card-hover p-4 z-40"
          role="dialog"
          aria-label="انتخاب تعداد مسافران"
        >
          <Counter
            title="بزرگسال"
            sub="۱ سال به بالا"
            value={adults}
            min={1}
            max={9}
            set={(v) => onChange(v, kids)}
          />
          <div className="h-px bg-divider my-1" />
          <Counter
            title="کودک"
            sub="۲ تا ۱۲ سال"
            value={kids}
            min={0}
            max={6}
            set={(v) => onChange(adults, v)}
          />
        </div>
      )}
    </div>
  );
}