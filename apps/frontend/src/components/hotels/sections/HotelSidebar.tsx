"use client";

import HotelFiltersSidebar from "../HotelFiltersSidebar";
import { ListingMap } from "@/components/shared/map";
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
    <aside className="hidden lg:block w-[300px] xl:w-[320px] flex-shrink-0 sticky top-[150px] self-start">
      <div className="space-y-4 md:space-y-5">
        <div className="bg-white rounded-lg border border-border overflow-hidden shadow-card hover:shadow-card-hover transition-shadow duration-500 ease-out">
          <ListingMap
            points={mapPoints}
            height={180}
            activeKey={hoveredKey}
            onPointHover={onHoverChange}
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