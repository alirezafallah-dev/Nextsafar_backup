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
    <section className="space-y-5">
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
            className="flex items-center gap-1.5 shrink-0 whitespace-nowrap px-3 py-1.5 md:px-4 md:py-2 rounded-md border border-border bg-white text-[11px] md:text-xs font-bold text-text-muted hover:border-primary hover:text-primary-dark hover:bg-primary-lightest/30 transition"
          >
            مشاهده همه
            <ArrowLeft className="w-3.5 h-3.5 shrink-0" />
          </Link>
        </div>
      )}

      {/* ═══ گرید کارت‌ها ═══ */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
        {hotels.map((hotel) => (
          <HotelGridCard key={hotel.id} hotel={hotel} showPrice />
        ))}
      </div>

      {/* ═══ لینک موبایل ═══ */}
      <Link
        href="/hotels"
        className="md:hidden inline-flex items-center justify-center gap-2 w-full py-3 rounded-lg border border-border hover:border-primary/40 text-xs font-bold text-text-muted hover:text-primary transition"
      >
        <span>مشاهده همه هتل‌ها</span>
        <ArrowLeft className="w-4 h-4" />
      </Link>
    </section>
  );
}