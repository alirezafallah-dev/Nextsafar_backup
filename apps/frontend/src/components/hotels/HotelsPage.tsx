"use client";

import { useState, useEffect } from "react";
import type { HotelsData } from "@/types/hotel";
import { pullFromServer } from "@/lib/favorites";
import { useMediaQuery } from "@/hooks/use-media-query";

// Hooks
import { useHotelFilters } from "./hooks/useHotelFilters";
import { useHotelMapPoints } from "./hooks/useHotelMapPoints";

// Sections
import HotelToolbar from "./sections/HotelToolbar";
import MobileFilterChips from "./sections/MobileFilterChips";
import HotelSidebar from "./sections/HotelSidebar";
import HotelResults from "./sections/HotelResults";
import MobileFilterSheet from "./MobileFilterSheet";

// Map
import ResponsiveMap from "@/components/shared/map/ResponsiveMap";

// Bottom Sections (Direct Import)
import HotelGuideSection from "./HotelGuideSection";
import HotelByTripType from "./HotelByTripType";
import HotelFAQ from "./HotelFAQ";

interface Props {
  data: HotelsData;
}

export default function HotelsPage({ data }: Props) {
  const [mobileFilterOpen, setMobileFilterOpen] = useState(false);
  const [hoveredKey, setHoveredKey] = useState<string | null>(null);
  
  // ✅ تشخیص موبایل
  const isMobile = useMediaQuery("(max-width: 1023px)");

  // Effects
  useEffect(() => {
    pullFromServer();
  }, []);

  // Hooks
  const {
    filters,
    setFilters,
    sortBy,
    handleSortChange,
    view,
    handleViewChange,
    filteredHotels,
    activeCount,
    priceMax,
  } = useHotelFilters(data.hotels);

  const mapPoints = useHotelMapPoints(filteredHotels);

  return (
    <div className="ns-container py-8 md:py-12">
      {/* Toolbar */}
      <HotelToolbar
        filteredCount={filteredHotels.length}
        activeCount={activeCount}
        sortBy={sortBy}
        view={view}
        onSortChange={handleSortChange}
        onViewChange={handleViewChange}
        onMobileFilterClick={() => setMobileFilterOpen(true)}
      />

      {/* Mobile Filter Chips */}
      <MobileFilterChips filters={filters} onFiltersChange={setFilters} />

      {/* ✅ نقشه شناور موبایل */}
      {isMobile && mapPoints.length > 0 && (
        <ResponsiveMap
          points={mapPoints}
          title="نقشه هتل‌ها"
          itemLabel="هتل"
          desktopHeight={180}
          activeKey={hoveredKey}
          onPointHover={setHoveredKey}
        />
      )}

      {/* Main Layout */}
      <div className="flex gap-6">
        <HotelSidebar
          mapPoints={mapPoints}
          filters={filters}
          totalCount={data.hotels.length}
          filteredCount={filteredHotels.length}
          priceMax={priceMax}
          hoveredKey={hoveredKey}
          onFiltersChange={setFilters}
          onHoverChange={setHoveredKey}
        />

        <main className="flex-1 min-w-0">
          <HotelResults
            hotels={filteredHotels}
            view={view}
            onHover={setHoveredKey}
          />
        </main>
      </div>

      {/* Bottom Sections */}
      <HotelGuideSection />
      <HotelByTripType />
      <HotelFAQ />

      {/* Mobile Filter Sheet */}
      <MobileFilterSheet
        open={mobileFilterOpen}
        onClose={() => setMobileFilterOpen(false)}
        filters={filters}
        onChange={setFilters}
        totalCount={data.hotels.length}
        filteredCount={filteredHotels.length}
        priceMax={priceMax}
      />
    </div>
  );
}