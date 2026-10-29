// src/components/hotels/single/HotelHeader.tsx
"use client";

import Link from "next/link";
import { MapPin, Star, Share2, Heart, ChevronLeft, Home } from "lucide-react";
import type { Hotel } from "@/types/hotel";
import { faDigits } from "@/lib/api/hotels";
import { useFavorites } from "@/lib/favorites";

interface Props { hotel: Hotel }

export default function HotelHeader({ hotel }: Props) {
  const { has, toggle } = useFavorites();
  const isFavorite = has(`hotel-${hotel.id}`);

  const handleShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share({ title: hotel.title, url: window.location.href });
      } catch {}
    } else {
      await navigator.clipboard.writeText(window.location.href);
      alert("لینک کپی شد");
    }
  };

  const breadcrumbItems = [
    { label: "خانه", href: "/", icon: Home },
    { label: "هتل‌ها", href: "/hotels" },
    hotel.country && { label: hotel.country, href: `/hotels?country=${hotel.country}` },
    hotel.city && { label: hotel.city, href: `/hotels?city=${hotel.city}` },
  ].filter(Boolean) as Array<{ label: string; href: string; icon?: any }>;

  return (
    <header className="bg-white">
      <div className="ns-container pt-6 md:pt-8 pb-6 md:pb-8">

        {/* ═══ بردکرامپ (هم‌استایل صفحه اصلی) ═══ */}
        <nav aria-label="مسیر" className="inline-flex items-center flex-wrap gap-1.5 mb-5 md:mb-6">
          {breadcrumbItems.map((item, i) => {
            const Icon = item.icon;
            const isLast = i === breadcrumbItems.length - 1;
            return (
              <div key={item.href} className="flex items-center gap-1.5">
                {i > 0 && <ChevronLeft className="w-3 h-3 text-text-subtle rtl:rotate-180 shrink-0" />}
                {isLast ? (
                  <span className="text-xs md:text-sm font-bold text-primary">{item.label}</span>
                ) : (
                  <Link
                    href={item.href}
                    className="inline-flex items-center gap-1 text-xs md:text-sm font-medium text-text-muted hover:text-primary transition"
                  >
                    {Icon && <Icon className="w-3 h-3 md:w-3.5 md:h-3.5" />}
                    {item.label}
                  </Link>
                )}
              </div>
            );
          })}
        </nav>

        {/* ═══ grid هم‌تراز با سایدبار ═══ */}
        <div className="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6 items-start">

          {/* ─── ستون محتوا ─── */}
          <div className="min-w-0 space-y-3">

            <h1 className="text-xl md:text-2xl lg:text-3xl font-extrabold text-text-strong leading-tight tracking-tight">
              {hotel.title}
            </h1>

            {hotel.title_en && (
              <p className="text-xs md:text-sm text-text-muted" dir="ltr">
                {hotel.title_en}
              </p>
            )}

            <div className="flex items-center justify-between gap-3 flex-wrap pt-2">
              <div className="flex items-center gap-3 flex-wrap">
                {hotel.stars > 0 && (
                  <div className="flex gap-0.5">
                    {Array.from({ length: Math.min(hotel.stars, 5) }).map((_, i) => (
                      <Star key={i} className="w-3.5 h-3.5 md:w-4 md:h-4 text-amber-400 fill-amber-400" />
                    ))}
                  </div>
                )}

                {hotel.stars > 0 && hotel.rating > 0 && (
                  <span className="text-text-subtle">•</span>
                )}

                {hotel.rating > 0 && (
                  <div className="inline-flex items-center gap-1.5">
                    <span className="px-2 py-0.5 md:py-1 rounded-md bg-success/10 border border-success/20 text-xs md:text-sm font-black text-success leading-none">
                      {faDigits(hotel.rating.toFixed(1))}
                    </span>
                    <span className="text-[11px] md:text-xs font-bold text-success">عالی</span>
                    <span className="text-[10px] md:text-[11px] text-text-muted">
                      ({faDigits((hotel.reviews || 0).toLocaleString())} نظر)
                    </span>
                  </div>
                )}

                {hotel.rating > 0 && (
                  <span className="text-text-subtle"></span>
                )}
              </div>

              <div className="inline-flex items-center gap-1.5 text-xs md:text-sm text-text-muted">
                <span dir="ltr">{hotel.city}{hotel.country && `، ${hotel.country}`}</span>
                <MapPin className="w-3.5 h-3.5 md:w-4 md:h-4 text-primary shrink-0" />
              </div>
            </div>
          </div>

          {/* ─── ستون دکمه‌ها ─── */}
          <div className="flex items-center gap-2 lg:justify-end shrink-0">
            <button
              onClick={() => toggle(`hotel-${hotel.id}`, {
                source: "hotels",
                title: hotel.title,
                image: hotel.image,
                url: `/hotels/${hotel.slug}`,
              })}
              className={`inline-flex items-center gap-2 px-3 md:px-4 py-1.5 md:py-2 rounded-lg text-[11px] md:text-xs font-bold transition-all ${
                isFavorite
                  ? "bg-primary text-white shadow-md hover:bg-primary-dark"
                  : "border border-border text-text hover:border-primary-light hover:text-primary hover:bg-primary-lightest/30"
              }`}
            >
              <Heart className={`w-3.5 h-3.5 md:w-4 md:h-4 ${isFavorite ? "fill-white" : ""}`} />
              {isFavorite ? "ذخیره شده" : "ذخیره"}
            </button>

            <button
              onClick={handleShare}
              className="inline-flex items-center gap-2 px-3 md:px-4 py-1.5 md:py-2 rounded-lg border border-border text-[11px] md:text-xs font-bold text-text hover:border-primary-light hover:text-primary hover:bg-primary-lightest/30 transition-all"
            >
              <Share2 className="w-3.5 h-3.5 md:w-4 md:h-4" />
              اشتراک‌گذاری
            </button>
          </div>
        </div>
      </div>
    </header>
  );
}