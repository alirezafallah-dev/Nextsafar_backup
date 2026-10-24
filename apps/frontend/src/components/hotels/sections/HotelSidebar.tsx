"use client";

import HotelFiltersSidebar from "../HotelFiltersSidebar";
import { ResponsiveMap } from "@/components/shared/map";
import type { ListingMapPoint } from "@/components/shared/map";
import type { HotelFiltersState } from "../constants";

interface HotelSidebarProps {
  mapPoints: ListingMapPoint[];
  filters: HotelFiltersState;
  totalCount: number;
  filteredCount: number;
  priceMax: number;
  hoveredKey: string | null;
  onFiltersChange: (filters: HotelFiltersState) => void;
  onHoverChange: (key: string | null) => void;
}

export default function HotelSidebar({
  mapPoints,
  filters,
  totalCount,
  filteredCount,
  priceMax,
  hoveredKey,
  onFiltersChange,
  onHoverChange,
}: HotelSidebarProps) {
  return (
    <aside className="hidden lg:block w-[300px] xl:w-[320px] flex-shrink-0 sticky top-24 self-start">
      <div className="space-y-3">
        {/* ═══ نقشه ماژولار (دسکتاپ: نقشه، موبایل: دکمه شناور) ═══ */}
        <div className="bg-white rounded-lg border border-border overflow-hidden shadow-sm">
          <ResponsiveMap
            points={mapPoints}
            title="نقشه هتل‌ها"
            desktopHeight={180}
            activeKey={hoveredKey}
            onPointHover={onHoverChange}
            className="!rounded-none"
          />
        </div>

        {/* ═══ فیلترها ═══ */}
        <HotelFiltersSidebar
          filters={filters}
          onChange={onFiltersChange}
          totalCount={totalCount}
          filteredCount={filteredCount}
          priceMax={priceMax}
        />
      </div>
    </aside>
  );
}