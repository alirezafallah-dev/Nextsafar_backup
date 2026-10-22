"use client";

import { useState, useEffect } from "react";
import type { HotelsData } from "@/types/hotel";
import { pullFromServer } from "@/lib/favorites";

// Hooks
import { useHotelFilters } from "./hooks/useHotelFilters";
import { useHotelMapPoints } from "./hooks/useHotelMapPoints";

// Sections
import HotelToolbar from "./sections/HotelToolbar";
import MobileFilterChips from "./sections/MobileFilterChips";
import HotelSidebar from "./sections/HotelSidebar";
import HotelResults from "./sections/HotelResults";
import MobileHotelMap from "./sections/MobileHotelMap";
import MobileFilterSheet from "./MobileFilterSheet";

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
    <div className="ns-container py-6">
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

      {/* Mobile Map */}
      <MobileHotelMap
        mapPoints={mapPoints}
        hoveredKey={hoveredKey}
        onHoverChange={setHoveredKey}
      />

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