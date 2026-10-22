"use client";

import type { FilterChipsProps } from "./types";

export default function FilterChips({ chips, className = "" }: FilterChipsProps) {
  if (chips.length === 0) return null;

  return (
    <div className={`flex flex-wrap gap-2 ${className}`}>
      {chips.map((chip) => (
        <button
          key={chip.key}
          onClick={chip.onRemove}
          className="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center gap-1 hover:bg-primary/20 transition cursor-pointer"
        >
          {chip.label}
          <span className="text-primary/60">×</span>
        </button>
      ))}
    </div>
  );
}