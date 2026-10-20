"use client";

import type { HotelFilterState } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";

interface Props {
  filters: HotelFilterState;
  onChange: (filters: HotelFilterState) => void;
  maxPrice: number;
}

export default function HotelFilters({ filters, onChange, maxPrice }: Props) {
  const update = (partial: Partial<HotelFilterState>) => {
    onChange({ ...filters, ...partial });
  };

  return (
    <>
      {/* ستاره */}
      <div>
        <label className="text-xs font-bold text-text-muted mb-2 block">حداقل ستاره</label>
        <div className="flex gap-1">
          {[1, 2, 3, 4, 5].map((n) => (
            <button
              key={n}
              onClick={() => update({ minStars: filters.minStars === n ? 0 : n })}
              className={`flex-1 py-2 rounded-lg border text-sm font-bold transition cursor-pointer ${
                filters.minStars >= n
                  ? "bg-primary text-white border-primary"
                  : "bg-white text-text-muted border-border hover:border-primary"
              }`}
            >
              {faDigits(n)}★
            </button>
          ))}
        </div>
      </div>

      {/* امتیاز */}
      <div>
        <label className="text-xs font-bold text-text-muted mb-2 block">حداقل امتیاز</label>
        <div className="flex gap-1">
          {[7, 8, 9].map((n) => (
            <button
              key={n}
              onClick={() => update({ minRating: filters.minRating === n ? 0 : n })}
              className={`flex-1 py-2 rounded-lg border text-sm font-bold transition cursor-pointer ${
                filters.minRating >= n
                  ? "bg-success text-white border-success"
                  : "bg-white text-text-muted border-border hover:border-success"
              }`}
            >
              {faDigits(n)}+
            </button>
          ))}
        </div>
      </div>

      {/* بازه قیمت */}
      <div>
        <label className="text-xs font-bold text-text-muted mb-2 block">
          حداکثر قیمت: {faDigits(filters.maxPrice.toLocaleString())} تومان
        </label>
        <input
          type="range"
          min={0}
          max={maxPrice}
          step={100000}
          value={filters.maxPrice}
          onChange={(e) => update({ maxPrice: parseInt(e.target.value) })}
          className="w-full accent-primary"
        />
      </div>

      {/* امکانات */}
      <div>
        <label className="text-xs font-bold text-text-muted mb-2 block">امکانات</label>
        <div className="flex flex-wrap gap-1">
          {["wifi", "pool", "parking", "gym"].map((key) => {
            const labels: Record<string, string> = {
              wifi: "وای‌فای",
              pool: "استخر",
              parking: "پارکینگ",
              gym: "باشگاه",
            };
            return (
              <button
                key={key}
                onClick={() => {
                  const has = filters.amenities.includes(key);
                  update({
                    amenities: has
                      ? filters.amenities.filter((a) => a !== key)
                      : [...filters.amenities, key],
                  });
                }}
                className={`px-2 py-1 rounded-md text-[11px] font-bold transition cursor-pointer ${
                  filters.amenities.includes(key)
                    ? "bg-primary text-white"
                    : "bg-bg-sec text-text-muted hover:bg-primary-lightest"
                }`}
              >
                {labels[key]}
              </button>
            );
          })}
        </div>
      </div>
    </>
  );
}