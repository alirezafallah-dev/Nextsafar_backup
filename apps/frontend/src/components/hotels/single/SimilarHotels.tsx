// src/components/hotels/single/SimilarHotels.tsx
"use client";

import Link from "next/link";
import { ArrowLeft } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import HotelGridCard from "@/components/shared/cards/HotelGridCard";

interface Props {
  hotels: Hotel[];
  /** نمایش هدِر داخلی — پیش‌فرض: true */
  showHeader?: boolean;
}

export default function SimilarHotels({ hotels, showHeader = true }: Props) {
  if (!hotels?.length) return null;

  return (
    <section className="ns-container space-y-5">
      {/* ═══ هدِر داخلی — فقط وقتی showHeader=true ═══ */}
      {showHeader && (
        <div className="flex items-center justify-between gap-3 mb-5 md:mb-6">
          <div className="min-w-0">
            <h2 className="ns-section-title !mb-1">هتل‌های مشابه</h2>
            <p className="text-xs md:text-sm text-text-muted">
              شاید این هتل‌ها هم برای شما جذاب باشد
            </p>
          </div>
          <Link
            href="/hotels"
            className="flex items-center gap-1.5 shrink-0 whitespace-nowrap px-3 py-1.5 md:px-4 md:py-2 rounded-[10px] md:rounded-lg border border-border bg-white text-[11px] md:text-xs font-bold text-text-muted hover:border-primary hover:text-primary-dark hover:bg-primary-lightest/30 transition"
          >
            مشاهده همه
            <ArrowLeft className="w-3.5 h-3.5 shrink-0" />
          </Link>
        </div>
      )}

      {/* ═══ موبایل: Carousel افقی با snap ═══ */}
      <div className="md:hidden">
        <div className="relative ns-bleed-x">
          {/* گرادیان fade سمت چپ (برای نشان دادن اسکرول بیشتر) */}
          <div className="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-l from-transparent to-white z-10 pointer-events-none hidden peer-has-scroll:block" />

          <div className="flex gap-3 overflow-x-auto snap-x snap-mandatory scrollbar-hide pb-2 px-4 -mx-4">
            {/* فاصله ابتدایی */}
            <div className="shrink-0 w-2" />

            {hotels.map((hotel) => (
              <div
                key={hotel.id}
                className="w-[280px] shrink-0 snap-start"
              >
                <HotelGridCard hotel={hotel} showPrice />
              </div>
            ))}

            {/* فاصله انتهایی */}
            <div className="shrink-0 w-2" />
          </div>
        </div>
      </div>

      {/* ═══ دسکتاپ: Grid 3 ستونه ═══ */}
      <div className="hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
        {hotels.map((hotel) => (
          <HotelGridCard key={hotel.id} hotel={hotel} showPrice />
        ))}
      </div>

      {/* ═══ لینک مشاهده همه در موبایل ═══ */}
      <Link
        href="/hotels"
        className="md:hidden inline-flex items-center justify-center gap-2 w-full py-3 rounded-[10px] border border-border hover:border-primary/40 text-xs font-bold text-text-muted hover:text-primary transition"
      >
        <span>مشاهده همه هتل‌ها</span>
        <ArrowLeft className="w-4 h-4" />
      </Link>
    </section>
  );
}