"use client";
import { useState, useRef, memo, useEffect } from "react";
import Link from "next/link";
import Image from "next/image";
import {
  ArrowLeft, MapPin, Heart, Star, BadgeCheck,
  ChevronLeft, ChevronRight, Users,
} from "lucide-react";
import { buildHotelUrl, faDigits } from "@/lib/api/hotels";
import { AMENITY_CONFIG } from "@/lib/hotel-config";
import { useFavorites } from "@/lib/favorites";
import type { Hotel } from "@/types/hotel";

const HOVER_DELAY = 500;

interface Props {
  hotel: Hotel;
  onHover?: (key: string | null) => void;
  showPrice?: boolean;
}

const HotelListItem = memo(function HotelListItem({ hotel, onHover, showPrice = false }: Props) {
  const { has, toggle } = useFavorites();
  const isFavorite = has(`hotel-${hotel.id}`);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const touchStartX = useRef<number>(0);
  const hoverTimeoutRef = useRef<NodeJS.Timeout | null>(null);
  const isHoveringRef = useRef(false);

  const images =
    hotel.gallery && hotel.gallery.length > 0
      ? hotel.gallery.slice(0, 5)
      : hotel.image
      ? [hotel.image]
      : [];

  const amenities = Array.isArray(hotel.amenities) ? hotel.amenities : [];

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

  const handleMouseEnter = () => {
    isHoveringRef.current = true;
    if (hoverTimeoutRef.current) clearTimeout(hoverTimeoutRef.current);
    hoverTimeoutRef.current = setTimeout(() => {
      if (isHoveringRef.current) onHover?.(`hotel-${hotel.id}`);
    }, HOVER_DELAY);
  };

  const handleMouseLeave = () => {
    isHoveringRef.current = false;
    if (hoverTimeoutRef.current) {
      clearTimeout(hoverTimeoutRef.current);
      hoverTimeoutRef.current = null;
    }
    onHover?.(null);
  };

  useEffect(() => {
    return () => {
      if (hoverTimeoutRef.current) clearTimeout(hoverTimeoutRef.current);
    };
  }, []);

  const visibleAmenities = amenities.slice(0, 4).map((key) => AMENITY_CONFIG[key]).filter(Boolean);
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
    <article
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      className="group relative bg-white rounded-xl overflow-hidden border border-border hover:border-primary/40 hover:shadow-xl transition-all duration-300 flex flex-row h-36 sm:h-44 lg:h-52 w-full cursor-pointer"
    >
      <div
        className="relative w-30 sm:w-36 md:w-44 lg:w-56 flex-shrink-0 overflow-hidden"
        onTouchStart={handleTouchStart}
        onTouchEnd={handleTouchEnd}
      >
        {currentImage ? (
          <Image
            src={currentImage}
            alt={hotel.title}
            fill
            sizes="(max-width: 640px) 112px, (max-width: 768px) 144px, (max-width: 1024px) 176px, 224px"
            className="object-cover transition-all duration-500 group-hover:scale-105"
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
            <button type="button" onClick={nextImage} className="hidden sm:flex absolute end-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white/95 backdrop-blur items-center justify-center text-text-strong hover:bg-white transition-all opacity-0 group-hover:opacity-100 shadow-md z-10" aria-label="عکس بعدی">
              <ChevronLeft className="w-3.5 h-3.5" />
            </button>
            <button type="button" onClick={prevImage} className="hidden sm:flex absolute start-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white/95 backdrop-blur items-center justify-center text-text-strong hover:bg-white transition-all opacity-0 group-hover:opacity-100 shadow-md z-10" aria-label="عکس قبلی">
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
          className={`absolute top-2 end-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full backdrop-blur flex items-center justify-center transition-all shadow-md z-10 ${
            isFavorite ? "bg-danger text-white scale-110" : "bg-white/95 text-text-muted hover:text-danger"
          }`}
          aria-label={isFavorite ? "حذف از علاقه‌مندی" : "افزودن به علاقه‌مندی"}
        >
          <Heart className={`w-3 h-3 sm:w-3.5 sm:h-3.5 ${isFavorite ? "fill-white" : ""}`} />
        </button>

        {hotel.featured && (
          <div className="absolute top-2 start-2 z-10">
            <span className="px-2 py-0.5 rounded-md text-[10px] font-bold bg-warning text-white shadow-md">
              ویژه
            </span>
          </div>
        )}
      </div>

      <Link href={buildHotelUrl(hotel)} className="flex-1 p-2.5 sm:p-3 lg:p-4 flex flex-col justify-between min-w-0">
        <div className="min-w-0">
          <div className="flex items-start justify-between gap-2 mb-1">
            <div className="min-w-0 flex-1">
              <h3 className="font-extrabold text-sm sm:text-base lg:text-lg text-text-strong line-clamp-1 group-hover:text-primary transition-colors">
                {hotel.title}
              </h3>
              {hotel.title_en && (
                <p className="text-[10px] sm:text-xs text-text-subtle mt-0.5 line-clamp-1" dir="ltr">
                  {hotel.title_en}
                </p>
              )}
            </div>
            {hotel.rating > 0 && (
              <div className="flex items-center gap-1 shrink-0">
                <div className="hidden md:flex items-center gap-1">
                  <BadgeCheck className="w-3.5 h-3.5 text-success" />
                  <span className="text-[11px] font-bold text-text-muted">
                    {hotel.reviews ? `${faDigits(hotel.reviews)} نظر` : "عالی"}
                  </span>
                </div>
                <div className="px-1.5 py-0.5 rounded-md bg-success text-white text-[11px] sm:text-xs font-extrabold shadow-sm">
                  {faDigits(hotel.rating.toFixed(1))}
                </div>
              </div>
            )}
          </div>

          <div className="flex items-center gap-2 mb-1.5 min-w-0">
            <div className="flex items-center gap-1 min-w-0 flex-1">
              <MapPin className="w-3 h-3 shrink-0 text-primary" />
              <span className="text-[11px] sm:text-xs text-text-muted line-clamp-1">{hotel.address}</span>
            </div>
            {hotel.stars > 0 && (
              <div className="flex gap-0.5 shrink-0">
                {[...Array(Math.min(hotel.stars, 5))].map((_, i) => (
                  <Star key={i} className="w-2.5 h-2.5 fill-warning text-warning" />
                ))}
              </div>
            )}
          </div>
        </div>

        <div className="flex items-center justify-between gap-2 pt-1.5 border-t border-border/60 min-w-0">
          <div className="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-1 overflow-hidden">
            {visibleAmenities.length > 0 ? (
              visibleAmenities.map((amenity, i) => {
                const Icon = amenity.icon;
                return (
                  <div key={i} className="flex items-center gap-1 shrink-0" title={amenity.label}>
                    <div className="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7 rounded-md bg-primary-lightest flex items-center justify-center">
                      <Icon className="w-2.5 h-2.5 sm:w-3 sm:h-3 lg:w-3.5 lg:h-3.5 text-primary" />
                    </div>
                    <span className="hidden xl:inline text-[10px] lg:text-[11px] text-text-muted font-medium whitespace-nowrap">
                      {amenity.label}
                    </span>
                  </div>
                );
              })
            ) : (
              <div className="flex items-center gap-1 text-[11px] text-text-subtle">
                <Users className="w-3.5 h-3.5" />
                <span>مشاهده جزئیات</span>
              </div>
            )}
            {amenities.length > 4 && (
              <span className="hidden sm:inline text-[10px] text-text-subtle font-bold whitespace-nowrap">
                +{faDigits(amenities.length - 4)}
              </span>
            )}
          </div>

          {showPrice && hotel.price_per_night && hotel.price_per_night > 0 && (
            <div className="flex items-center gap-2 shrink-0">
              <span className="text-xs sm:text-sm font-black text-primary">
                {faDigits(hotel.price_per_night.toLocaleString())}
              </span>
              <span className="text-[10px] text-text-muted">تومان</span>
            </div>
          )}

          <div className="flex items-center gap-1 text-[11px] sm:text-xs font-extrabold text-primary group-hover:gap-2 transition-all shrink-0 whitespace-nowrap">
            <span className="hidden sm:inline">جزئیات</span>
            <ArrowLeft className="w-3.5 h-3.5 group-hover:-translate-x-1 transition-transform" />
          </div>
        </div>
      </Link>
    </article>
  );
});

export default HotelListItem;