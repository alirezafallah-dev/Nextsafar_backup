"use client";
import { useState } from "react";
import { HotelGridCard, TourCategoryCard } from "@/components/shared/cards";
import type { Hotel } from "@/types/hotel";

export interface HotelItem {
  id?: number;
  slug: string;
  title: string;
  image: string | null;
  stars: number | null;
  city: string;
  city_slug?: string;
  country_slug?: string;
  category_slug?: string;
  price: number | null;
  rating: number | null;
}

export interface TourCatItem {
  slug: string;
  name: string;
  count: number;
  image: string | null;
  duration?: number | null;
}

export default function TrendingClient({
  hotels,
  tourCats,
}: {
  hotels: HotelItem[];
  tourCats: TourCatItem[];
}) {
  const [tab, setTab] = useState<"hotel" | "tour">(
    hotels.length ? "hotel" : "tour",
  );

// تبدیل HotelItem به Hotel type
const convertToHotel = (item: HotelItem): Hotel => ({
  id: item.id || 0,
  title: item.title,
  title_en: "",
  image: item.image || "",
  gallery: [],
  address: item.city,
  stars: item.stars || 0,
  rating: item.rating || 0,
  price_per_night: item.price || 0,
  amenities: [],
  featured: false,
  reviews: 0,
  slug: item.slug,
  country: "",
  country_slug: "",
  city: item.city,
  city_slug: "",
  coordinates: { lat: 0, lng: 0 },
});

  // کانتینر مشترک
  const containerCls =
    "ns-bleed-x flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-5 md:overflow-visible md:pb-0";

  return (
    <div>
      {/* Header با title و tabs */}
      <div className="flex items-end justify-between mb-5 gap-4 flex-wrap">
        <div>
          <h2 className="ns-section-title !mb-1">داغ‌ترین‌های هفته</h2>
          <p className="text-xs md:text-sm text-text-muted">
            پیشنهادهایی که همین حالا بیشترین رزرو را دارند
          </p>
        </div>
        <div className="flex items-center gap-1 bg-bg-sec border border-border rounded-lg p-1">
          <button
            onClick={() => setTab("hotel")}
            className={`px-4 py-2 rounded-lg text-xs md:text-sm font-bold transition-all duration-300 cursor-pointer ${
              tab === "hotel"
                ? "bg-primary text-white shadow-md"
                : "text-text-muted hover:text-primary-dark"
            }`}
          >
            هتل‌ها
          </button>
          <button
            onClick={() => setTab("tour")}
            className={`px-4 py-2 rounded-lg text-xs md:text-sm font-bold transition-all duration-300 cursor-pointer ${
              tab === "tour"
                ? "bg-tour text-white shadow-md"
                : "text-text-muted hover:text-tour-hover"
            }`}
          >
            تورها
          </button>
        </div>
      </div>

      {/* Tab: هتل‌ها */}
      {tab === "hotel" &&
        (hotels.length === 0 ? (
          <div className="rounded-[10px] md:rounded-lg border border-border bg-white p-10 text-center text-sm text-text-muted">
            هنوز هتلی ثبت نشده است
          </div>
        ) : (
          <div className={containerCls}>
            {hotels.map((h) => (
              <HotelGridCard key={h.slug} hotel={convertToHotel(h)} />
            ))}
          </div>
        ))}

      {/* Tab: تورها */}
      {tab === "tour" &&
        (tourCats.length === 0 ? (
          <div className="rounded-[10px] md:rounded-lg border border-border bg-white p-10 text-center text-sm text-text-muted">
            هنوز دسته‌بندی توری ثبت نشده است
          </div>
        ) : (
          <div className={containerCls}>
            {tourCats.map((c) => (
              <TourCategoryCard
                key={c.slug}
                slug={c.slug}
                name={c.name}
                count={c.count}
                image={c.image}
                duration={c.duration}
              />
            ))}
          </div>
        ))}
    </div>
  );
}