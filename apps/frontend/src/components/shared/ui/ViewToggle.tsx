"use client";
import { LayoutGrid, List } from "lucide-react";
import { cn } from "@/lib/utils";

interface Props {
  view: "grid" | "list";
  onChange: (v: "grid" | "list") => void;
}

export default function ViewToggle({ view, onChange }: Props) {
  return (
    <div className="inline-flex bg-bg-sec rounded-lg border border-border p-0.5 h-10">
      {/* ✅ list اول (سمت راست در RTL)، grid دوم */}
      {(["list", "grid"] as const).map((v) => {
        const Icon = v === "grid" ? LayoutGrid : List;
        return (
          <button
            key={v}
            onClick={() => onChange(v)}
            className={cn(
              "flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-bold transition",
              view === v
                ? "bg-white shadow-sm text-text-strong"
                : "text-text-muted hover:text-text-strong"
            )}
          >
            <Icon className="w-4 h-4" />
            <span className="hidden sm:inline">{v === "grid" ? "کارت" : "لیست"}</span>
          </button>
        );
      })}
    </div>
  );
}