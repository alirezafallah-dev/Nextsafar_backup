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
      <div className="ns-container pt-6 md:pt-8 pb-4 md:pb-4">

        {/* ═══ بردکرامپ — تمام‌عرض با طراحی مدرن ═══ */}
        <nav
          aria-label="مسیر"
          className="w-full flex items-center justify-between flex-wrap gap-2 mb-5 md:mb-6 px-4 md:px-5 py-2.5 md:py-3 rounded-[10px] md:rounded-lg bg-gradient-to-l from-primary/5 via-bg-sec/50 to-bg-sec/80 border border-border/60 shadow-sm"
        >
          {/* ═══ مسیر — سمت راست (RTL) ═══ */}
          <div className="flex items-center flex-wrap gap-1.5 md:gap-2 min-w-0">
            {breadcrumbItems.map((item, i) => {
              const Icon = item.icon;
              const isLast = i === breadcrumbItems.length - 1;
              return (
                <div key={item.href} className="flex items-center gap-1.5 md:gap-2 min-w-0">
                  {/* Separator */}
                  {i > 0 && (
                    <span className="text-text-subtle/40 shrink-0">
                      <ChevronLeft className="w-2.5 h-2.5 md:w-3 md:h-3" />
                    </span>
                  )}

                  {/* آیتم آخر — pill با رنگ primary */}
                  {isLast ? (
                    <span className="inline-flex items-center gap-1.5 px-2 md:px-2.5 py-0.5 md:py-1 rounded-md bg-primary/10 border border-primary/20 text-[11px] md:text-xs font-bold text-primary truncate max-w-[160px] md:max-w-none">
                      {item.label}
                    </span>
                  ) : (
                    /* آیتم‌های قابل کلیک */
                    <Link
                      href={item.href}
                      className="inline-flex items-center gap-1 md:gap-1.5 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md text-[11px] md:text-xs font-medium text-text-muted hover:text-primary hover:bg-primary/5 transition shrink-0"
                    >
                      {Icon && (
                        <Icon className="w-3 h-3 md:w-3.5 md:h-3.5 text-primary" />
                      )}
                      <span className="hidden sm:inline">{item.label}</span>
                      <span className="sm:hidden">{item.label.split(" ")[0]}</span>
                    </Link>
                  )}
                </div>
              );
            })}
          </div>
        </nav>

        {/* ═══ grid هم‌تراز با سایدبار ═══ */}
        <div className="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-4 lg:gap-6 items-start">
          {/* ─── ستون محتوا ─── */}
          <div className="min-w-0 space-y-3">
            <h1 className="text-lg md:text-2xl lg:text-3xl font-extrabold text-text-strong leading-tight tracking-tight">
              {hotel.title}
            </h1>

            {hotel.title_en && (
              <p className="text-[11px] md:text-sm text-text-muted" dir="ltr" style={{ fontStyle: "italic" }}>
                {hotel.title_en}
              </p>
            )}

            <div className="flex items-center justify-between gap-2 flex-wrap">
              <div className="flex items-center gap-2 md:gap-3 flex-wrap">
                {hotel.stars > 0 && (
                  <div className="flex gap-0.5">
                    {Array.from({ length: Math.min(hotel.stars, 5) }).map((_, i) => (
                      <Star key={i} className="w-3 h-3 md:w-4 md:h-4 text-amber-400 fill-amber-400" />
                    ))}
                  </div>
                )}

                {hotel.stars > 0 && hotel.rating > 0 && (
                  <span className="text-text-subtle hidden md:inline">•</span>
                )}

                {hotel.rating > 0 && (
                  <div className="inline-flex items-center gap-1 md:gap-1.5">
                    <span className="px-1.5 py-0.5 md:px-2 md:py-1 rounded-md bg-success/10 border border-success/20 text-[11px] md:text-sm font-black text-success leading-none">
                      {faDigits(hotel.rating.toFixed(1))}
                    </span>
                    <span className="text-[10px] md:text-xs font-bold text-success">عالی</span>
                    <span className="text-[9px] md:text-[11px] text-text-muted">
                      ({faDigits((hotel.reviews || 0).toLocaleString())} نظر)
                    </span>
                  </div>
                )}
              </div>

              <div className="inline-flex items-center gap-1 md:gap-1.5 text-[11px] md:text-sm text-text-muted">
                <span dir="ltr" className="truncate max-w-[120px] md:max-w-none">
                  {hotel.city}
                  {hotel.country && `، ${hotel.country}`}
                </span>
                <MapPin className="w-3 h-3 md:w-4 md:h-4 text-primary shrink-0" />
              </div>
            </div>
          </div>

          {/* ─── ستون دکمه‌ها — فقط دسکتاپ نمایش داده می‌شود ─── */}
          <div className="hidden lg:flex items-center gap-2 justify-end shrink-0">
            <button
              onClick={() => toggle(`hotel-${hotel.id}`, {
                source: "hotels",
                title: hotel.title,
                image: hotel.image,
                url: `/hotels/${hotel.slug}`,
              })}
              className={`inline-flex items-center gap-2 px-4 py-2 rounded-[10px] lg:rounded-lg text-xs font-bold transition-all ${
                isFavorite
                  ? "bg-primary text-white shadow-md hover:bg-primary-dark"
                  : "border border-border text-text hover:border-primary-light hover:text-primary hover:bg-primary-lightest/30"
              }`}
            >
              <Heart className={`w-4 h-4 ${isFavorite ? "fill-white" : ""}`} />
              {isFavorite ? "ذخیره شده" : "ذخیره"}
            </button>

            <button
              onClick={handleShare}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-[10px] lg:rounded-lg border border-border text-xs font-bold text-text hover:border-primary-light hover:text-primary hover:bg-primary-lightest/30 transition-all"
            >
              <Share2 className="w-4 h-4" />
              اشتراک‌گذاری
            </button>
          </div>
        </div>
      </div>
    </header>
  );
}