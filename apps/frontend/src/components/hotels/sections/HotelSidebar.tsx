"use client";

import { useState } from "react";
import { Maximize2 } from "lucide-react";
import ListingMap from "@/components/shared/map/ListingMap";
import MapModal from "@/components/shared/map/MapModal";
import HotelFiltersSidebar from "../HotelFiltersSidebar";
import type { ListingMapPoint } from "@/components/shared/map/ListingMap";
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
  const [isMapModalOpen, setIsMapModalOpen] = useState(false);

  return (
    <>
      <aside className="hidden lg:block w-[300px] xl:w-[320px] flex-shrink-0 sticky top-24 self-start">
        <div className="space-y-3">
          {/* Map */}
          <div className="relative bg-white rounded-lg border border-border overflow-hidden shadow-sm">
            <ListingMap
              points={mapPoints}
              height={180}
              className="!rounded-none"
              activeKey={hoveredKey}
              onPointHover={onHoverChange}
              showControls={false}
              showFullscreen={false}
              markerStyle="icon"
            />

            {/* دکمه مودال */}
            <button
              onClick={() => setIsMapModalOpen(true)}
              className="absolute bottom-3 end-3 flex items-center gap-1.5 px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold shadow-lg hover:bg-primary-dark transition cursor-pointer z-10"
            >
              <Maximize2 className="w-3.5 h-3.5" />
              نقشه کامل
            </button>
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

      {/* Map Modal */}
      <MapModal
        open={isMapModalOpen}
        onClose={() => setIsMapModalOpen(false)}
        title="نقشه هتل‌ها"
        points={mapPoints}
        activeKey={hoveredKey}
        onPointHover={onHoverChange}
      />
    </>
  );
}