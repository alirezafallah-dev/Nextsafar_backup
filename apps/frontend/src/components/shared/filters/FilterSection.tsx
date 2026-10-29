"use client";

import { useState } from "react";
import { ChevronDown } from "lucide-react";
import type { FilterSectionProps } from "./types";

export default function FilterSection({
  title,
  icon,
  badge,
  defaultOpen = true,
  children,
}: FilterSectionProps) {
  const [isOpen, setIsOpen] = useState(defaultOpen);

  return (
    <div className="border-b border-border last:border-0 pb-4 mb-4 last:pb-0 last:mb-0">
      <button
        type="button"
        onClick={() => setIsOpen(!isOpen)}
        className="w-full flex items-center justify-between gap-2 py-1 cursor-pointer group"
      >
        <div className="flex items-center gap-1.5 min-w-0">
          {icon && (
            <span className="text-text-muted group-hover:text-primary transition-colors">
              {icon}
            </span>
          )}
          <span className="text-xs font-bold text-text-strong truncate">
            {title}
          </span>
          {badge && badge > 0 && (
            <span className="px-1.5 py-0.5 rounded-full bg-primary text-white text-[9px] font-bold shrink-0">
              {badge}
            </span>
          )}
        </div>
        <ChevronDown
          className={`w-4 h-4 text-text-muted transition-transform duration-200 shrink-0 ${
            isOpen ? "rotate-180" : ""
          }`}
        />
      </button>

      {isOpen && <div className="mt-3">{children}</div>}
    </div>
  );
}