"use client";

import { ListingMap } from "@/components/shared/map";
import type { ListingMapPoint } from "@/components/shared/map";

interface MobileHotelMapProps {
  mapPoints: ListingMapPoint[];
  hoveredKey: string | null;
  onHoverChange: (key: string | null) => void;
}

export default function MobileHotelMap({
  mapPoints,
  hoveredKey,
  onHoverChange,
}: MobileHotelMapProps) {
  return (
    <div className="lg:hidden mt-8">
      <div className="rounded-lg border border-border overflow-hidden bg-white shadow-sm">
        <ListingMap
          points={mapPoints}
          height={220}
          className="!rounded-none"
          activeKey={hoveredKey}
          onPointHover={onHoverChange}
        />
      </div>
    </div>
  );
}