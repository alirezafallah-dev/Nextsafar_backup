"use client";

import { SlidersHorizontal } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import { SortSelect, ViewToggle } from "@/components/shared/ui";
import { SORT_OPTIONS } from "../constants";
import type { SortKey, ViewMode } from "../constants";

interface HotelToolbarProps {
  filteredCount: number;
  activeCount: number;
  sortBy: SortKey;
  view: ViewMode;
  onSortChange: (sort: SortKey) => void;
  onViewChange: (view: ViewMode) => void;
  onMobileFilterClick: () => void;
}

export default function HotelToolbar({
  filteredCount,
  activeCount,
  sortBy,
  view,
  onSortChange,
  onViewChange,
  onMobileFilterClick,
}: HotelToolbarProps) {
  return (
    <div className="flex items-center justify-between gap-3 mb-5">
      <span className="text-sm text-text-muted">
        <strong className="text-text-strong">
          {faDigits(filteredCount)}
        </strong>{" "}
        هتل یافت شد
      </span>

      <div className="flex items-center gap-2">
        {/* Mobile Filter Button */}
        <button
          onClick={onMobileFilterClick}
          className="lg:hidden flex items-center gap-1.5 px-3 py-2 rounded-lg border border-border bg-white text-xs font-bold hover:border-primary/40 transition"
        >
          <SlidersHorizontal className="w-4 h-4" />
          فیلتر
          {activeCount > 0 && (
            <span className="px-1.5 py-0.5 rounded-full bg-primary text-white text-[10px]">
              {faDigits(activeCount)}
            </span>
          )}
        </button>

        <SortSelect
          value={sortBy}
          onChange={onSortChange}
          options={SORT_OPTIONS}
        />

        <ViewToggle view={view} onChange={onViewChange} />
      </div>
    </div>
  );
}