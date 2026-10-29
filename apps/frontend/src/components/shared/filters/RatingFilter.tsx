"use client";

import { faDigits } from "@/lib/api/hotels";
import type { RatingFilterProps } from "./types";

export default function RatingFilter({
  options,
  selected,
  onChange,
}: RatingFilterProps) {
  return (
    <div className="grid grid-cols-3 gap-1.5">
      {options.map((opt) => {
        const isActive = selected === opt.val;
        return (
          <button
            key={opt.val}
            type="button"
            onClick={() => onChange(isActive ? 0 : opt.val)}
            className={`py-2 rounded-md border text-center transition-all cursor-pointer ${
              isActive
                ? "border-primary bg-primary/5"
                : "border-border hover:border-primary/40"
            }`}
          >
            <div
              className={`text-xs font-black ${
                isActive ? "text-primary" : "text-text-strong"
              }`}
            >
              {faDigits(opt.val)}+
            </div>
            <div className="text-[9px] text-text-muted mt-0.5">{opt.label}</div>
          </button>
        );
      })}
    </div>
  );
}