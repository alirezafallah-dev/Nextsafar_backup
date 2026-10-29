"use client";

import { Search } from "lucide-react";
import type { SearchFilterProps } from "./types";

export default function SearchFilter({
  value,
  onChange,
  placeholder = "جستجو...",
  label = "جستجو",
}: SearchFilterProps) {
  return (
    <div className="mb-4">
      <label className="flex items-center gap-1.5 text-xs font-bold text-text-strong mb-2">
        <Search className="w-3.5 h-3.5 text-primary" />
        {label}
      </label>
      <div className="relative">
        <input
          type="text"
          value={value}
          onChange={(e) => onChange(e.target.value)}
          placeholder={placeholder}
          className="w-full h-9 px-3 pe-9 rounded-md border border-border bg-white text-xs outline-none focus:border-primary transition-colors"
        />
        <Search className="absolute end-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-text-muted pointer-events-none" />
      </div>
    </div>
  );
}