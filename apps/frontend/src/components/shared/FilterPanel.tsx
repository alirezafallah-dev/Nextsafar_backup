"use client";

import { useState } from "react";
import { SlidersHorizontal, ChevronDown, X } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import type { FilterPanelProps } from "@/types/listing";

export default function FilterPanel({
  activeCount = 0,
  onClear,
  children,
}: FilterPanelProps) {
  const [showAdvanced, setShowAdvanced] = useState(false);

  return (
    <div className="rounded-xl border border-border bg-white shadow-sm">
      {/* ═══ دکمه باز/بستن ═══ */}
      <button
        onClick={() => setShowAdvanced(!showAdvanced)}
        className={`w-full flex items-center justify-between px-4 py-3 text-sm font-bold cursor-pointer transition ${
          showAdvanced
            ? "bg-primary text-white rounded-t-xl"
            : "bg-white text-text-strong hover:bg-bg-sec rounded-xl"
        }`}
      >
        <div className="flex items-center gap-2">
          <SlidersHorizontal className="w-4 h-4" />
          فیلترهای پیشرفته
          {activeCount > 0 && (
            <span className={`px-1.5 py-0.5 rounded-full text-[10px] ${
              showAdvanced ? "bg-white/20" : "bg-primary text-white"
            }`}>
              {faDigits(activeCount)}
            </span>
          )}
        </div>
        <ChevronDown
          className={`w-4 h-4 transition-transform ${showAdvanced ? "rotate-180" : ""}`}
        />
      </button>

      {/* ═══ محتوای فیلترها ═══ */}
      {showAdvanced && (
        <div className="p-4 border-t border-border animate-in fade-in slide-in-from-top-2 duration-300">
          {/* دکمه پاک کردن */}
          {activeCount > 0 && onClear && (
            <div className="mb-4 flex justify-end">
              <button
                onClick={onClear}
                className="flex items-center gap-1 text-xs text-danger hover:text-danger-dark font-bold cursor-pointer"
              >
                <X className="w-3 h-3" />
                پاک کردن همه فیلترها
              </button>
            </div>
          )}

          {/* محتوای فیلترها (slot) */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {children}
          </div>
        </div>
      )}
    </div>
  );
}