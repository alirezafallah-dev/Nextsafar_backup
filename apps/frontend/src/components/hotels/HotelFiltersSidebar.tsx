"use client";

import {
  Search, Star, RotateCcw, Waves, Utensils, Wifi, Car, Dumbbell,
  Sparkles, Coffee, BedDouble, Users, SlidersHorizontal,
} from "lucide-react";
import {
  FilterSection,
  SearchFilter,
  StarRatingFilter,
  RatingFilter,
  AmenitiesFilter,
  CheckboxGroupFilter,
} from "@/components/shared/filters";
import { faDigits } from "@/lib/api/hotels";

export interface HotelFiltersState {
  search: string;
  stars: number[];
  amenities: string[];
  priceRange: [number, number];
  minRating: number;
}

interface Props {
  filters: HotelFiltersState;
  onChange: (f: HotelFiltersState) => void;
  totalCount: number;
  filteredCount: number;
  priceMax: number;
}

/* ═══ پیکربندی فیلدهای اختصاصی هتل ═══ */
const STAR_OPTIONS = [5, 4, 3];

const AMENITY_OPTIONS = [
  { id: "wifi", label: "وای‌فای", icon: Wifi },
  { id: "pool", label: "استخر", icon: Waves },
  { id: "parking", label: "پارکینگ", icon: Car },
  { id: "gym", label: "باشگاه", icon: Dumbbell },
  { id: "spa", label: "اسپا", icon: Sparkles },
  { id: "restaurant", label: "رستوران", icon: Utensils },
  { id: "breakfast", label: "صبحانه", icon: Coffee },
  { id: "beach", label: "ساحل", icon: Waves },
];

const RATING_OPTIONS = [
  { val: 9, label: "عالی ۹+" },
  { val: 8, label: "خیلی خوب ۸+" },
  { val: 7, label: "خوب ۷+" },
];

const ACCOMMODATION_TYPES = ["هتل", "هتل آپارتمان", "اقامتگاه بوم‌گردی", "ویلا"];

export default function HotelFiltersSidebar({
  filters,
  onChange,
  priceMax,
}: Props) {
  const hasActive =
    filters.search ||
    filters.stars.length > 0 ||
    filters.amenities.length > 0 ||
    filters.priceRange[0] > 0 ||
    filters.priceRange[1] < priceMax ||
    filters.minRating > 0;

  const reset = () => {
    onChange({
      search: "",
      stars: [],
      amenities: [],
      priceRange: [0, priceMax],
      minRating: 0,
    });
  };

  return (
    <aside className="bg-white rounded-lg border border-border overflow-hidden shadow-sm text-[13px]">
      {/* Header */}
      <div className="flex items-center justify-between px-4 py-3 border-b border-border">
        <div className="flex items-center gap-1.5">
          <SlidersHorizontal className="w-4 h-4 text-primary" />
          <span className="font-bold text-text-strong text-sm">فیلترها</span>
        </div>
        {hasActive && (
          <button
            onClick={reset}
            className="flex items-center gap-1 text-[11px] font-bold text-danger hover:text-danger-dark transition cursor-pointer"
          >
            <RotateCcw className="w-3 h-3" />
            <span>پاک کردن</span>
          </button>
        )}
      </div>

      {/* Body */}
      <div className="px-4 py-3">
        {/* جستجو */}
        <SearchFilter
          value={filters.search}
          onChange={(value) => onChange({ ...filters, search: value })}
          placeholder="نام هتل..."
          label="جستجوی هتل"
        />

        {/* ستاره هتل */}
        <FilterSection
          title="ستاره هتل"
          icon={<Star className="w-3.5 h-3.5" />}
          badge={filters.stars.length || undefined}
        >
          <StarRatingFilter
            options={STAR_OPTIONS}
            selected={filters.stars}
            onChange={(stars) => onChange({ ...filters, stars })}
          />
        </FilterSection>

        {/* امتیاز مهمان */}
        <FilterSection
          title="امتیاز مهمان"
          icon={<Sparkles className="w-3.5 h-3.5" />}
        >
          <RatingFilter
            options={RATING_OPTIONS}
            selected={filters.minRating}
            onChange={(minRating) => onChange({ ...filters, minRating })}
          />
        </FilterSection>

        {/* امکانات */}
        <FilterSection
          title="امکانات"
          icon={<Waves className="w-3.5 h-3.5" />}
          badge={filters.amenities.length || undefined}
        >
          <AmenitiesFilter
            options={AMENITY_OPTIONS}
            selected={filters.amenities}
            onChange={(amenities) => onChange({ ...filters, amenities })}
          />
        </FilterSection>

        {/* نوع اقامت */}
        <FilterSection
          title="نوع اقامت"
          icon={<Users className="w-3.5 h-3.5" />}
          defaultOpen={false}
        >
          <CheckboxGroupFilter options={ACCOMMODATION_TYPES} />
        </FilterSection>
      </div>
    </aside>
  );
}