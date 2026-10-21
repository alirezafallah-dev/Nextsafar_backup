"use client";

import { BedDouble } from "lucide-react";
import { HotelGridCard, HotelListItem } from "@/components/shared/cards";
import type { Hotel } from "@/types/hotel";
import type { ViewMode } from "../constants";

interface HotelResultsProps {
  hotels: Hotel[];
  view: ViewMode;
  onHover: (key: string | null) => void;
}

export default function HotelResults({
  hotels,
  view,
  onHover,
}: HotelResultsProps) {
  if (hotels.length === 0) {
    return <EmptyState />;
  }

  if (view === "list") {
    return (
      <div className="space-y-4">
        {hotels.map((h) => (
          <HotelListItem
            key={h.id}
            hotel={h}
            onHover={(key) => onHover(key)}
          />
        ))}
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      {hotels.map((h) => (
        <HotelGridCard key={h.id} hotel={h} />
      ))}
    </div>
  );
}

function EmptyState() {
  return (
    <div className="rounded-lg border-2 border-dashed border-border bg-bg-sec/30 p-12 text-center">
      <BedDouble className="w-12 h-12 text-primary/50 mx-auto mb-4" />
      <h3 className="text-lg font-extrabold text-text-strong mb-2">
        هتلی یافت نشد
      </h3>
      <p className="text-sm text-text-muted">
        فیلترهای خود را تغییر دهید یا مقصد دیگری را انتخاب کنید
      </p>
    </div>
  );
}