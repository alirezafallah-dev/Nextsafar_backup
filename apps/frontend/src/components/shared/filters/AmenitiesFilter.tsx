"use client";

import type { AmenitiesFilterProps } from "./types";

export default function AmenitiesFilter({
  options,
  selected,
  onChange,
  columns = 2,
}: AmenitiesFilterProps) {
  const toggleAmenity = (id: string) => {
    const newSelected = selected.includes(id)
      ? selected.filter((a) => a !== id)
      : [...selected, id];
    onChange(newSelected);
  };

  const gridCols = columns === 2 ? "grid-cols-2" : "grid-cols-3";

  return (
    <div className={`grid ${gridCols} gap-1.5`}>
      {options.map((option) => {
        const isActive = selected.includes(String(option.id));
        const Icon = option.icon;
        return (
          <button
            key={option.id}
            type="button"
            onClick={() => toggleAmenity(String(option.id))}
            className={`flex items-center gap-2 py-2 px-2.5 rounded-md border transition-all cursor-pointer ${
              isActive
                ? "border-primary bg-primary/5"
                : "border-border hover:border-primary/40"
            }`}
          >
            {Icon && (
              <div
                className={`w-7 h-7 rounded flex items-center justify-center transition-colors flex-shrink-0 ${
                  isActive
                    ? "bg-primary text-white"
                    : "bg-bg-sec text-text-muted"
                }`}
              >
                <Icon className="w-3.5 h-3.5" />
              </div>
            )}
            <span className="text-[11px] font-medium text-text-strong leading-tight text-start">
              {option.label}
            </span>
          </button>
        );
      })}
    </div>
  );
}