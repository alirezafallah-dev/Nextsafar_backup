"use client";

import { ListingMap } from "@/components/shared/map";
import type { ListingMapPoint } from "@/components/shared/map";
import HotelFiltersSidebar from "../HotelFiltersSidebar";
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
        {/* Map */}
        <div className="bg-white rounded-lg border border-border overflow-hidden shadow-sm">
          <ListingMap
            points={mapPoints}
            height={180}
            className="!rounded-none"
            activeKey={hoveredKey}
            onPointHover={onHoverChange}
          />
        </div>

        {/* Filters */}
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