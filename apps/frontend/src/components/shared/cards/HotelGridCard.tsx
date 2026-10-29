"use client";
import { useState, useRef, memo } from "react";
import Link from "next/link";
import Image from "next/image";
import { MapPin, Heart, Star, ChevronLeft, ChevronRight } from "lucide-react";
import { faDigits, buildHotelUrl } from "@/lib/api/hotels";
import { AMENITY_CONFIG } from "@/lib/hotel-config";
import { useFavorites } from "@/lib/favorites";
import type { Hotel } from "@/types/hotel";

interface Props {
  hotel: Hotel;
  showPrice?: boolean;
}

const HotelGridCard = memo(function HotelGridCard({ hotel, showPrice = false }: Props) {
  const { has, toggle } = useFavorites();
  const isFavorite = has(`hotel-${hotel.id}`);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const touchStartX = useRef<number>(0);

  const images =
    hotel.gallery && hotel.gallery.length > 0
      ? hotel.gallery.slice(0, 5)
      : hotel.image
      ? [hotel.image]
      : [];

  const amenities = Array.isArray(hotel.amenities) ? hotel.amenities : [];
  const visibleAmenities = amenities
    .slice(0, 3)
    .map((k) => AMENITY_CONFIG[k])
    .filter(Boolean);

  const nextImage = (e?: React.MouseEvent) => {
    e?.preventDefault();
    e?.stopPropagation();
    setCurrentImageIndex((prev) => (prev + 1) % images.length);
  };

  const prevImage = (e?: React.MouseEvent) => {
    e?.preventDefault();
    e?.stopPropagation();
    setCurrentImageIndex((prev) => (prev - 1 + images.length) % images.length);
  };

  const handleTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.touches[0].clientX;
  };

  const handleTouchEnd = (e: React.TouchEvent) => {
    const diff = touchStartX.current - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) {
      diff > 0 ? prevImage() : nextImage();
    }
  };

  const currentImage = images[currentImageIndex] || null;

  const handleToggleFavorite = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    toggle(`hotel-${hotel.id}`, {
      source: "hotels",
      title: hotel.title,
      image: currentImage || hotel.image,
      url: buildHotelUrl(hotel),
    });
  };

  return (
    <article className="group bg-white rounded-[10px] md:rounded-lg border border-border hover:border-primary-light hover:shadow-card-hover transition-all duration-500 ease-out overflow-hidden cursor-pointer w-full">
      {/* ✅ aspect-[16/9] برای موبایل (کوتاه‌تر)، aspect-[4/3] برای دسکتاپ */}
      <div
        className="relative aspect-[16/9] md:aspect-[4/3] overflow-hidden"
        onTouchStart={handleTouchStart}
        onTouchEnd={handleTouchEnd}
      >
        {currentImage ? (
          <Image
            src={currentImage}
            alt={hotel.title}
            fill
            sizes="(max-width: 640px) 100vw, (max-width: 768px) 50vw, (max-width: 1024px) 33vw, 300px"
            className="object-cover transition-transform duration-500 group-hover:scale-105"
            priority={currentImageIndex === 0}
          />
        ) : (
          <div className="w-full h-full bg-bg-sec flex items-center justify-center">
            <div className="flex flex-col items-center gap-1 text-text-subtle/40">
              <svg className="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
              </svg>
              <span className="text-[10px] font-bold">بدون تصویر</span>
            </div>
          </div>
        )}

        <div className="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent pointer-events-none" />

        {images.length > 1 && (
          <>
            <button
              type="button"
              onClick={nextImage}
              className="hidden sm:flex absolute end-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white/95 backdrop-blur items-center justify-center text-text-strong hover:bg-white transition-all opacity-0 group-hover:opacity-100 shadow-md z-10"
              aria-label="عکس بعدی"
            >
              <ChevronLeft className="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              onClick={prevImage}
              className="hidden sm:flex absolute start-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white/95 backdrop-blur items-center justify-center text-text-strong hover:bg-white transition-all opacity-0 group-hover:opacity-100 shadow-md z-10"
              aria-label="عکس قبلی"
            >
              <ChevronRight className="w-3.5 h-3.5" />
            </button>
          </>
        )}

        {images.length > 1 && (
          <div className="absolute bottom-2 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full bg-black/70 backdrop-blur text-white text-[9px] font-bold z-10 tabular-nums">
            {faDigits(currentImageIndex + 1)} / {faDigits(images.length)}
          </div>
        )}

        <button
          type="button"
          onClick={handleToggleFavorite}
          className={`absolute top-2 end-2 w-8 h-8 rounded-full backdrop-blur flex items-center justify-center transition-all shadow-md z-10 ${
            isFavorite
              ? "bg-danger text-white scale-110"
              : "bg-white/95 text-text-muted hover:text-danger"
          }`}
          aria-label={isFavorite ? "حذف از علاقه‌مندی" : "افزودن به علاقه‌مندی"}
        >
          <Heart className={`w-3.5 h-3.5 ${isFavorite ? "fill-white" : ""}`} />
        </button>

        {hotel.featured && (
          <div className="absolute top-2 start-2 z-10">
            <span className="px-2 py-0.5 rounded-md text-[10px] font-bold bg-warning text-white shadow-md">
              ویژه
            </span>
          </div>
        )}
      </div>

      <Link href={buildHotelUrl(hotel)} className="block p-4">
        <div className="flex items-start justify-between gap-2 mb-2">
          <div className="min-w-0 flex-1">
            <h3 className="font-extrabold text-sm text-text-strong line-clamp-1 group-hover:text-primary transition-colors">
              {hotel.title}
            </h3>
            {hotel.title_en && (
              <p className="text-[10px] text-text-subtle mt-0.5 line-clamp-1" dir="ltr">
                {hotel.title_en}
              </p>
            )}
          </div>
          {hotel.rating > 0 && (
            <div className="px-1.5 py-0.5 rounded-md bg-success text-white text-[11px] font-extrabold shadow-sm shrink-0">
              {faDigits(hotel.rating.toFixed(1))}
            </div>
          )}
        </div>

        <div className="flex items-center gap-2 mb-3 min-w-0">
          <div className="flex items-center gap-1 min-w-0 flex-1">
            <MapPin className="w-3 h-3 shrink-0 text-primary" />
            <span className="text-[11px] text-text-muted line-clamp-1">
              {hotel.address}
            </span>
          </div>
          {hotel.stars > 0 && (
            <div className="flex gap-0.5 shrink-0">
              {[...Array(Math.min(hotel.stars, 5))].map((_, i) => (
                <Star key={i} className="w-2.5 h-2.5 fill-warning text-warning" />
              ))}
            </div>
          )}
        </div>

        <div className="flex items-center gap-1.5 min-w-0">
          {visibleAmenities.length > 0 ? (
            visibleAmenities.map((amenity, i) => {
              const Icon = amenity.icon;
              return (
                <div key={i} className="flex items-center gap-1 shrink-0" title={amenity.label}>
                  <div className="w-6 h-6 rounded bg-primary-lightest flex items-center justify-center">
                    <Icon className="w-3 h-3 text-primary" />
                  </div>
                </div>
              );
            })
          ) : (
            <div className="text-[10px] text-text-subtle">مشاهده جزئیات</div>
          )}
          {amenities.length > 3 && (
            <span className="text-[10px] text-text-subtle font-bold whitespace-nowrap">
              +{faDigits(amenities.length - 3)}
            </span>
          )}
        </div>

      </Link>
    </article>
  );
});

export default HotelGridCard;