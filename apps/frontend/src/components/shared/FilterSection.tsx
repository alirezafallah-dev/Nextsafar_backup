"use client";
import { useState } from "react";
import { ChevronDown } from "lucide-react";
import { cn } from "@/lib/utils";

interface Props {
  title: string;
  icon?: React.ReactNode;
  children: React.ReactNode;
  defaultOpen?: boolean;
  badge?: string | number;
}

export default function FilterSection({
  title, icon, children, defaultOpen = true, badge,
}: Props) {
  const [open, setOpen] = useState(defaultOpen);

  return (
    <div className="border-b border-border last:border-b-0">
      <button
        onClick={() => setOpen(!open)}
        className="w-full flex items-center justify-between py-2.5 text-right"
      >
        <div className="flex items-center gap-1.5">
          {icon && <span className="text-primary">{icon}</span>}
          <span className="text-xs font-bold text-text-strong">{title}</span>
          {badge !== undefined && (
            <span className="px-1.5 py-0.5 rounded bg-primary/10 text-primary text-[9px] font-black">
              {badge}
            </span>
          )}
        </div>
        <ChevronDown
          className={cn(
            "w-3.5 h-3.5 text-text-muted transition-transform duration-200",
            open && "rotate-180"
          )}
        />
      </button>
      <div
        className={cn(
          "overflow-hidden transition-all duration-300",
          open ? "max-h-[1000px] pb-3" : "max-h-0"
        )}
      >
        {children}
      </div>
    </div>
  );
}