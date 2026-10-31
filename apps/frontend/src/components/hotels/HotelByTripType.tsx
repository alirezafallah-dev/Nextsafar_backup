// src/components/hotels/HotelByTripType.tsx
"use client";

import { useState } from "react";
import { HotelGridCard } from "@/components/shared/cards";
import SectionHeader from "@/components/home/SectionHeader";
import hotelsData from "@/data/hotels.json";
import type { HotelsData, Hotel } from "@/types/hotel";

/* ═══ انواع سفر ═══ */
type TripType = "family" | "romantic" | "business" | "luxury";

const TRIP_TABS: { id: TripType; label: string }[] = [
  { id: "family", label: "سفر خانوادگی" },
  { id: "romantic", label: "سفر رمانتیک" },
  { id: "business", label: "سفر کاری" },
  { id: "luxury", label: "سفر لوکس" },
];

/* ═══ دسته‌بندی هتل‌ها بر اساس نوع سفر ═══ */
function categorizeHotels(hotels: Hotel[]): Record<TripType, Hotel[]> {
  const result: Record<TripType, Hotel[]> = {
    family: [],
    romantic: [],
    business: [],
    luxury: [],
  };

  if (!hotels || !Array.isArray(hotels)) return result;

  hotels.forEach((hotel) => {
    const cat = hotel.category?.toLowerCase() || "";

    if (cat.includes("family") || cat.includes("خانوادگی")) {
      result.family.push(hotel);
    } else if (cat.includes("romantic") || cat.includes("رمانتیک")) {
      result.romantic.push(hotel);
    } else if (cat.includes("business") || cat.includes("کاری")) {
      result.business.push(hotel);
    } else if (cat.includes("luxury") || cat.includes("لوکس") || (hotel.stars ?? 0) >= 5) {
      result.luxury.push(hotel);
    }
  });

  return result;
}

/* ═══ کلاس مشترک کانتینر (مثل TrendingClient) ═══ */
const containerCls =
  "ns-bleed-x flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-5 md:overflow-visible md:pb-0";

interface Props {
  hotels?: Hotel[];
}

export default function HotelByTripType({ hotels }: Props) {
  const [activeTab, setActiveTab] = useState<TripType>("family");

  const allHotels: Hotel[] = hotels ?? (hotelsData as HotelsData).hotels ?? [];
  const categorized = categorizeHotels(allHotels);
  const activeHotels = categorized[activeTab] || [];

  return (
    <section className="py-8 md:py-12">
      <SectionHeader
        title="بهترین هتل‌ها بر اساس نوع سفر"
        subtitle="هتل مناسب سفر خود را پیدا کنید"
      />

      {/* ═══ تب‌های نوع سفر (بدون آیکون) ═══ */}
      <div className="mb-5">
        <div className="flex items-center gap-1 bg-bg-sec border border-border rounded-lg p-1 w-fit max-w-full overflow-x-auto scrollbar-hide">
          {TRIP_TABS.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`px-4 py-2 rounded-md text-xs md:text-sm font-bold whitespace-nowrap transition-all duration-300 cursor-pointer ${
                activeTab === tab.id
                  ? "bg-primary text-white shadow-md"
                  : "text-text-muted hover:text-primary-dark"
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>
      </div>

      {/* ═══ کارت‌ها: موبایل اسکرول افقی / دسکتاپ گرید ═══ */}
      {activeHotels.length > 0 ? (
        <div className={containerCls}>
          {activeHotels.slice(0, 8).map((hotel) => (
            <div key={hotel.id} className="w-[280px] md:w-auto flex-shrink-0 snap-start">
              <HotelGridCard hotel={hotel} />
            </div>
          ))}
        </div>
      ) : (
        <div className="rounded-[10px] md:rounded-lg border border-border bg-white p-10 text-center text-sm text-text-muted">
          هتلی برای این نوع سفر یافت نشد
        </div>
      )}
    </section>
  );
}