"use client";

import { MobileFilterSheet as BaseMobileFilterSheet } from "@/components/shared/filters";
import HotelFiltersSidebar, { HotelFiltersState } from "./HotelFiltersSidebar";

interface Props {
  open: boolean;
  onClose: () => void;
  filters: HotelFiltersState;
  onChange: (f: HotelFiltersState) => void;
  totalCount: number;
  filteredCount: number;
  priceMax: number;
}

export default function MobileFilterSheet({
  open,
  onClose,
  filters,
  onChange,
  filteredCount,
  priceMax,
}: Props) {
  return (
    <BaseMobileFilterSheet
      open={open}
      onClose={onClose}
      title="فیلترها"
      resultCount={filteredCount}
      resultLabel="هتل"
    >
      <HotelFiltersSidebar
        filters={filters}
        onChange={onChange}
        totalCount={0}
        filteredCount={filteredCount}
        priceMax={priceMax}
        compact={true}  // ✅ حالت فشرده برای موبایل
      />
    </BaseMobileFilterSheet>
  );
}