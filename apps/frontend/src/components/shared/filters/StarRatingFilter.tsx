"use client";

import { Star } from "lucide-react";
import type { StarRatingFilterProps } from "./types";

export default function StarRatingFilter({
  options,
  selected,
  onChange,
}: StarRatingFilterProps) {
  const toggleStar = (star: number) => {
    const newSelected = selected.includes(star)
      ? selected.filter((s) => s !== star)
      : [...selected, star];
    onChange(newSelected);
  };

  return (
    <div className="flex gap-1.5">
      {options.map((star) => {
        const isActive = selected.includes(star);
        return (
          <button
            key={star}
            type="button"
            onClick={() => toggleStar(star)}
            className={`flex-1 flex items-center justify-center gap-0.5 py-2 rounded-md border transition-all cursor-pointer ${
              isActive
                ? "border-primary bg-primary/5"
                : "border-border hover:border-primary/40"
            }`}
          >
            {Array.from({ length: star }).map((_, i) => (
              <Star
                key={i}
                className={`w-3 h-3 ${
                  isActive
                    ? "text-warning fill-warning"
                    : "text-text-subtle fill-text-subtle"
                }`}
              />
            ))}
          </button>
        );
      })}
    </div>
  );
}