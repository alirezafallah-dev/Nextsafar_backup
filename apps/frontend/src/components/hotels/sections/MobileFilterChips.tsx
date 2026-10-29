"use client";

import { faDigits } from "@/lib/api/hotels";
import type { HotelFiltersState } from "../constants";

interface MobileFilterChipsProps {
  filters: HotelFiltersState;
  onFiltersChange: (filters: HotelFiltersState) => void;
}

export default function MobileFilterChips({
  filters,
  onFiltersChange,
}: MobileFilterChipsProps) {
  const activeCount =
    filters.stars.length +
    filters.amenities.length +
    (filters.minRating > 0 ? 1 : 0) +
    (filters.priceRange[0] > 0 || filters.priceRange[1] < 10_000_000 ? 1 : 0);

  if (activeCount === 0) return null;

  return (
    <div className="lg:hidden flex flex-wrap gap-2 mb-4">
      {filters.stars.map((s) => (
        <button
          key={s}
          onClick={() =>
            onFiltersChange({
              ...filters,
              stars: filters.stars.filter((x) => x !== s),
            })
          }
          className="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center gap-1"
        >
          {faDigits(s)} ستاره
          <span className="text-primary/60">×</span>
        </button>
      ))}
      {filters.amenities.map((a) => (
        <button
          key={a}
          onClick={() =>
            onFiltersChange({
              ...filters,
              amenities: filters.amenities.filter((x) => x !== a),
            })
          }
          className="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center gap-1"
        >
          {a}
          <span className="text-primary/60">×</span>
        </button>
      ))}
    </div>
  );
}