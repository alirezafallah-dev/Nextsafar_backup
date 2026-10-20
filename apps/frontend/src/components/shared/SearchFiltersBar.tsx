"use client";

import { Search, SlidersHorizontal } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import type { SearchFiltersBarProps } from "@/types/listing";

export default function SearchFiltersBar({
  searchQuery,
  onSearchChange,
  searchPlaceholder = "جستجو...",
  sortOptions,
  sortValue,
  onSortChange,
  resultCount,
  resultLabel = "مورد",
  extraActions,
}: SearchFiltersBarProps) {
  return (
    <div className="rounded-xl border border-border bg-white p-4 shadow-sm">
      <div className="flex flex-wrap items-center gap-3">
        {/* جستجو */}
        <div className="flex-1 min-w-[200px] relative">
          <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" />
          <input
            type="text"
            placeholder={searchPlaceholder}
            value={searchQuery}
            onChange={(e) => onSearchChange(e.target.value)}
            className="w-full ps-9 pe-4 py-2.5 rounded-lg border border-border bg-bg-sec text-sm placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
          />
        </div>

        {/* مرتب‌سازی */}
        <select
          value={sortValue}
          onChange={(e) => onSortChange(e.target.value)}
          className="px-4 py-2.5 rounded-lg border border-border bg-white text-sm font-bold text-text-strong focus:outline-none focus:ring-2 focus:ring-primary/30 cursor-pointer"
        >
          {sortOptions.map((option) => (
            <option key={option.key} value={option.key}>
              {option.label}
            </option>
          ))}
        </select>

        {/* تعداد نتایج */}
        <span className="text-xs text-text-muted">
          <strong className="text-text-strong">{faDigits(resultCount)}</strong>{" "}
          {resultLabel}
        </span>

        {/* دکمه‌های اضافی */}
        {extraActions}
      </div>
    </div>
  );
}