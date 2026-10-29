"use client";
import Image from "next/image";
import { Star, MapPin, BedDouble, Utensils, Plane, Landmark, Building2, ChevronLeft } from "lucide-react";
import { faDigits } from "@/lib/api/hotels";
import type { ListingMapPoint } from "./types";

interface PlaceCardProps {
  place: ListingMapPoint;
  isActive: boolean;
  isHovered: boolean;
  onHover: (key: string | null) => void;
  onClick: (place: ListingMapPoint) => void;
  /** برچسب دکمه - مثلا "مشاهده" */
  actionLabel?: string;
}

/* ═══ آیکون بر اساس نوع ═══ */
function getTypeIcon(type: string) {
  switch (type) {
    case "hotel": return <BedDouble className="w-3.5 h-3.5" />;
    case "restaurant": return <Utensils className="w-3.5 h-3.5" />;
    case "airport": return <Plane className="w-3.5 h-3.5" />;
    case "destination":
    case "attraction": return <Landmark className="w-3.5 h-3.5" />;
    default: return <Building2 className="w-3.5 h-3.5" />;
  }
}

/* ═══ برچسب فارسی ═══ */
function getTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    hotel: "هتل",
    restaurant: "رستوران",
    airport: "فرودگاه",
    destination: "جاذبه",
    attraction: "جاذبه",
    hospital: "بیمارستان",
  };
  return labels[type] || "مکان";
}

/* ═══ ستاره‌ها ═══ */
function StarRating({ stars }: { stars: number }) {
  return (
    <div className="flex items-center gap-0.5">
      {Array.from({ length: 5 }).map((_, i) => (
        <Star
          key={i}
          className={`w-3 h-3 ${
            i < stars ? "text-amber-400 fill-amber-400" : "text-gray-300"
          }`}
        />
      ))}
    </div>
  );
}

export default function PlaceCard({
  place,
  isActive,
  isHovered,
  onHover,
  onClick,
  actionLabel = "مشاهده",
}: PlaceCardProps) {
  const typeLabel = getTypeLabel(place.type);
  const href = place.href || `/${place.type}s/${place.key}`;

  return (
    <div
      onMouseEnter={() => onHover(place.key)}
      onMouseLeave={() => onHover(null)}
      onClick={() => onClick(place)}
      className={`group relative flex gap-3 p-3 rounded-xl border transition-all cursor-pointer ${
        isActive
          ? "bg-primary/5 border-primary shadow-md ring-2 ring-primary/20"
          : isHovered
          ? "bg-bg-sec border-primary/30 shadow-sm"
          : "bg-white border-border hover:border-primary/30 hover:shadow-sm"
      }`}
    >
      {/* ═══ تصویر ═══ */}
      <div className="relative w-[110px] h-[110px] sm:w-[120px] sm:h-[120px] flex-shrink-0 rounded-lg overflow-hidden bg-bg-sec">
        {place.image ? (
          <Image
            src={place.image}
            alt={place.title}
            fill
            className="object-cover group-hover:scale-105 transition-transform duration-500"
            sizes="100px"
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-primary/50">
            {getTypeIcon(place.type)}
          </div>
        )}

        {/* بج نوع */}
        <div className="absolute top-1.5 start-1.5 flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-black/70 backdrop-blur text-white text-[9px] font-bold">
          {getTypeIcon(place.type)}
          {typeLabel}
        </div>
      </div>

      {/* ═══ اطلاعات ═══ */}
      <div className="flex-1 min-w-0 flex flex-col">
        {/* عنوان */}
        <h4 className="font-extrabold text-sm text-text-strong line-clamp-1 group-hover:text-primary transition-colors">
          {place.title}
        </h4>

        {/* ستاره و امتیاز */}
        <div className="flex items-center gap-1.5 mt-1 flex-wrap">
          {place.stars && place.stars > 0 && (
            <StarRating stars={place.stars} />
          )}
          {place.rating && (
            <span className="flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-primary/10 text-primary text-[10px] font-extrabold">
              {faDigits(place.rating.toFixed(1))}
            </span>
          )}
          {place.reviewsCount && place.reviewsCount > 0 && (
            <span className="text-[10px] text-text-muted">
              ({faDigits(place.reviewsCount)} نظر)
            </span>
          )}
        </div>

        {/* آدرس */}
        {place.address && (
          <div className="flex items-start gap-1 mt-1.5 text-[11px] text-text-muted">
            <MapPin className="w-3 h-3 mt-0.5 flex-shrink-0 text-primary" />
            <span className="line-clamp-1">{place.address}</span>
          </div>
        )}

        {/* فاصله پایین - دکمه */}
        <div className="mt-auto pt-2 flex items-center justify-between gap-2">
          {/* برچسب‌های اضافی */}
          <div className="flex items-center gap-1 flex-wrap">
            {place.cuisine && (
              <span className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-bg-sec text-[9px] text-text-muted">
                <Utensils className="w-2.5 h-2.5" />
                {place.cuisine}
              </span>
            )}
            {place.airportCode && (
              <span className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-bg-sec text-[9px] text-text-muted font-bold">
                <Plane className="w-2.5 h-2.5" />
                {place.airportCode}
              </span>
            )}
          </div>

          {/* دکمه مشاهده */}
          <a
            href={href}
            onClick={(e) => e.stopPropagation()}
            className="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:text-primary-dark whitespace-nowrap"
          >
            {actionLabel}
            <ChevronLeft className="w-3 h-3" />
          </a>
        </div>
      </div>
    </div>
  );
}