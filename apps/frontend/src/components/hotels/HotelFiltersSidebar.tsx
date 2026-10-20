"use client";
import {
  Search, Star, RotateCcw, Waves, Utensils, Wifi, Car, Dumbbell,
  Sparkles, Coffee, BedDouble, Users, SlidersHorizontal,
} from "lucide-react";
import FilterSection from "@/components/shared/FilterSection";
import PriceRangeSlider from "@/components/shared/PriceRangeSlider";
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

const starOptions = [5, 4, 3];

const amenityOptions = [
  { id: "wifi", label: "وای‌فای", icon: Wifi },
  { id: "pool", label: "استخر", icon: Waves },
  { id: "parking", label: "پارکینگ", icon: Car },
  { id: "gym", label: "باشگاه", icon: Dumbbell },
  { id: "spa", label: "اسپا", icon: Sparkles },
  { id: "restaurant", label: "رستوران", icon: Utensils },
  { id: "breakfast", label: "صبحانه", icon: Coffee },
  { id: "beach", label: "ساحل", icon: Waves },
];

export default function HotelFiltersSidebar({
  filters, onChange, totalCount, filteredCount, priceMax,
}: Props) {
  // ✅ Generic Type اضافه شود
  const toggleArray = <K extends "stars" | "amenities">(
    key: K,
    value: HotelFiltersState[K][number]
  ) => {
    const currentArray = filters[key] as Array<HotelFiltersState[K][number]>;
    const newArray = currentArray.includes(value)
      ? currentArray.filter((v) => v !== value)
      : [...currentArray, value];
    
    onChange({
      ...filters,
      [key]: newArray,
    });
  };

  const reset = () => {
    onChange({
      search: "",
      stars: [],
      amenities: [],
      priceRange: [0, priceMax],
      minRating: 0,
    });
  };

  const hasActive =
    filters.search ||
    filters.stars.length > 0 ||
    filters.amenities.length > 0 ||
    filters.priceRange[0] > 0 ||
    filters.priceRange[1] < priceMax ||
    filters.minRating > 0;

  return (
    <aside className="bg-white rounded-lg border border-border overflow-hidden shadow-sm text-[13px]">
      <div className="flex items-center justify-between px-4 py-3 border-b border-border">
        <div className="flex items-center gap-1.5">
          <SlidersHorizontal className="w-4 h-4 text-primary" />
          <span className="font-bold text-text-strong text-sm">فیلترها</span>
        </div>
        {hasActive && (
          <button
            onClick={reset}
            className="flex items-center gap-1 text-[11px] font-bold text-danger hover:text-danger-dark transition"
          >
            <RotateCcw className="w-3 h-3" />
            <span>پاک کردن</span>
          </button>
        )}
      </div>

      <div className="px-4 py-3">
        <div className="mb-4">
          <label className="flex items-center gap-1.5 text-xs font-bold text-text-strong mb-2">
            <Search className="w-3.5 h-3.5 text-primary" />
            جستجوی هتل
          </label>
          <div className="relative">
            <input
              type="text"
              value={filters.search}
              onChange={(e) => onChange({ ...filters, search: e.target.value })}
              placeholder="نام هتل..."
              className="w-full h-9 px-3 pe-9 rounded-md border border-border bg-white text-xs outline-none focus:border-primary transition-colors"
            />
            <Search className="absolute end-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-text-muted pointer-events-none" />
          </div>
        </div>

        <FilterSection title="ستاره هتل" icon={<Star className="w-3.5 h-3.5" />} badge={filters.stars.length || undefined}>
          <div className="flex gap-1.5">
            {starOptions.map((s) => {
              const active = filters.stars.includes(s);
              return (
                <button
                  key={s}
                  onClick={() => toggleArray("stars", s)}
                  className={`flex-1 flex items-center justify-center gap-0.5 py-2 rounded-md border transition-all ${
                    active
                      ? "border-primary bg-primary/5"
                      : "border-border hover:border-primary/40"
                  }`}
                >
                  {Array.from({ length: s }).map((_, i) => (
                    <Star
                      key={i}
                      className={`w-3 h-3 ${
                        active ? "text-warning fill-warning" : "text-text-subtle fill-text-subtle"
                      }`}
                    />
                  ))}
                </button>
              );
            })}
          </div>
        </FilterSection>

        <FilterSection title="بازه قیمت (هر شب)" icon={<BedDouble className="w-3.5 h-3.5" />}>
          <PriceRangeSlider
            min={0}
            max={priceMax}
            value={filters.priceRange}
            onChange={(v) => onChange({ ...filters, priceRange: v })}
          />
        </FilterSection>

        <FilterSection title="امتیاز مهمان" icon={<Sparkles className="w-3.5 h-3.5" />}>
          <div className="grid grid-cols-3 gap-1.5">
            {[
              { val: 9, label: "عالی ۹+" },
              { val: 8, label: "خیلی خوب ۸+" },
              { val: 7, label: "خوب ۷+" },
            ].map((opt) => {
              const active = filters.minRating === opt.val;
              return (
                <button
                  key={opt.val}
                  onClick={() =>
                    onChange({ ...filters, minRating: active ? 0 : opt.val })
                  }
                  className={`py-2 rounded-md border text-center transition-all ${
                    active
                      ? "border-primary bg-primary/5"
                      : "border-border hover:border-primary/40"
                  }`}
                >
                  <div className={`text-xs font-black ${active ? "text-primary" : "text-text-strong"}`}>
                    {faDigits(opt.val)}+
                  </div>
                  <div className="text-[9px] text-text-muted mt-0.5">{opt.label}</div>
                </button>
              );
            })}
          </div>
        </FilterSection>

        <FilterSection title="امکانات" icon={<Waves className="w-3.5 h-3.5" />} badge={filters.amenities.length || undefined}>
          <div className="grid grid-cols-2 gap-1.5">
            {amenityOptions.map((a) => {
              const active = filters.amenities.includes(a.id);
              const Icon = a.icon;
              return (
                <button
                  key={a.id}
                  onClick={() => toggleArray("amenities", a.id)}
                  className={`flex items-center gap-2 py-2 px-2.5 rounded-md border transition-all ${
                    active
                      ? "border-primary bg-primary/5"
                      : "border-border hover:border-primary/40"
                  }`}
                >
                  <div
                    className={`w-7 h-7 rounded flex items-center justify-center transition-colors flex-shrink-0 ${
                      active
                        ? "bg-primary text-white"
                        : "bg-bg-sec text-text-muted"
                    }`}
                  >
                    <Icon className="w-3.5 h-3.5" />
                  </div>
                  <span className="text-[11px] font-medium text-text-strong leading-tight text-start">
                    {a.label}
                  </span>
                </button>
              );
            })}
          </div>
        </FilterSection>

        <FilterSection title="نوع اقامت" icon={<Users className="w-3.5 h-3.5" />} defaultOpen={false}>
          <div className="space-y-1.5">
            {["هتل", "هتل آپارتمان", "اقامتگاه بوم‌گردی", "ویلا"].map((t) => (
              <label key={t} className="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" className="w-3.5 h-3.5 rounded accent-primary" />
                <span className="text-xs text-text group-hover:text-primary transition">
                  {t}
                </span>
              </label>
            ))}
          </div>
        </FilterSection>
      </div>
    </aside>
  );
}